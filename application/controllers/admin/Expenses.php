<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Expenses extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('expenses_model');
    }
    public function index($id = '', $clientid = '')
    {
        $this->list_expenses($id, $clientid);
    }
    public function list_expenses($id = '', $clientid = '')
    {
        $has_permission = has_permission('manageExpenses');
        if (!$has_permission && !$this->input->is_ajax_request()) {
            access_denied('manageExpenses');
        }
        if ($this->input->is_ajax_request()) {
            // From client profile
            if (is_numeric($clientid)) {
                if (!$has_permission) {
                    echo json_encode(json_decode('{"draw":1,"iTotalRecords":"0","iTotalDisplayRecords":"0","aaData":[]}'));
                    die;
                }
            }
            $this->perfex_base->get_table_data('expenses', array(
                'id' => $id,
                'clientid' => $clientid
            ));
        }
        $data['expenseid'] = '';
        if (is_numeric($id)) {
            $data['expenseid'] = $id;
        }
        $data['total_unbilled'] = $this->db->query('SELECT count(*) as num_rows FROM tblexpenses WHERE (SELECT 1 from tblinvoices WHERE tblinvoices.id = tblexpenses.invoiceid AND status != 2)')->row()->num_rows;
        $data['categories']     = $this->expenses_model->get_category();
        $data['title']          = _l('expenses');
        $this->load->view('admin/expenses/manage', $data);
    }
    public function expense($id = '')
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->expenses_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('expense')));
                    echo json_encode(array(
                        'url' => admin_url('expenses/list_expenses/' . $id),
                        'expenseid' => $id
                    ));
                    die;
                }
                echo json_encode(array(
                    'url' => admin_url('expenses/expense')
                ));
                die;
            } else {
                $success = $this->expenses_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('expense')));
                }
                echo json_encode(array(
                    'url' => admin_url('expenses/list_expenses/' . $id),
                    'expenseid' => $id
                ));
                die;
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('expense_lowercase'));
        } else {
            $data['expense'] = $this->expenses_model->get($id);
            $title           = _l('edit', _l('expense_lowercase'));
        }
        $this->load->model('clients_model');
        $this->load->model('taxes_model');
        $this->load->model('payment_modes_model');
        $this->load->model('currencies_model');
        $data['customers']     = $this->clients_model->get();
        $data['taxes']         = $this->taxes_model->get();
        $data['categories']    = $this->expenses_model->get_category();
        $data['payment_modes'] = $this->payment_modes_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title']         = $title;
        $this->load->view('admin/expenses/expense', $data);
    }
    public function delete($id)
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        if (!$id) {
            redirect(admin_url('expenses/list_expenses'));
        }
        $response = $this->expenses_model->delete($id);
        if ($response === true) {
            set_alert('success', _l('deleted', _l('expense')));
        } else {
            if (is_array($response) && $response['invoiced'] == true) {
                set_alert('warning', _l('expense_invoice_delete_not_allowed'));
            } else {
                set_alert('warning', _l('problem_deleting', _l('expense_lowercase')));
            }
        }
        redirect(admin_url('expenses/list_expenses'));
    }
    public function copy($id)
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        $new_expense_id = $this->expenses_model->copy($id);
        if ($new_expense_id) {
            set_alert('success', _l('expense_copy_success'));
            redirect(admin_url('expenses/expense/' . $new_expense_id));
        } else {
            set_alert('warning', _l('expense_copy_fail'));
        }
        redirect(admin_url('expenses/list_expenses/' . $id));
    }
    public function convert_to_invoice($id)
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        if (!$id) {
            redirect(admin_url('expenses/list_expenses'));
        }
        $invoiceid = $this->expenses_model->convert_to_invoice($id);
        if ($invoiceid) {
            set_alert('success', _l('expense_converted_to_invoice'));
            redirect(admin_url('invoices/invoice/' . $invoiceid));
        } else {
            set_alert('warning', _l('expense_converted_to_invoice_fail'));
        }
        redirect(admin_url('expenses/list_expenses/' . $id));
    }
    public function get_expense_data_ajax($id)
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $expense               = $this->expenses_model->get($id);
        $data['expense']       = $expense;
        if ($expense->billable == 1) {
            if ($expense->invoiceid !== NULL) {
                $this->load->model('invoices_model');
                $data['invoice'] = $this->invoices_model->get($expense->invoiceid);
            }
        }
        $this->load->view('admin/expenses/expense_preview_template', $data);
    }
    public function categories()
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('expenses_categories');
        }
        $data['title'] = _l('expense_categories');
        $this->load->view('admin/expenses/manage_categories', $data);
    }
    public function category()
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->expenses_model->add_category($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('expense_category')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->expenses_model->update_category($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('expense_category')));
                }
            }
        }
    }
    public function delete_category($id)
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        if (!$id) {
            redirect(admin_url('expenses/categories'));
        }
        $response = $this->expenses_model->delete_category($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('expense_category_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('expense_category')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expense_category_lowercase')));
        }
        redirect(admin_url('expenses/categories'));
    }
    public function add_expense_attachment($id)
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        handle_expense_attachments($id);
    }
    public function delete_expense_attachment($id, $preview = '')
    {
        if (!has_permission('manageExpenses')) {
            access_denied('manageExpenses');
        }
        $success = $this->expenses_model->delete_expense_attachment($id);
        if ($success) {
            set_alert('success', _l('deleted', _l('expense_receipt')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expense_receipt_lowercase')));
        }
        if ($preview == '') {
            redirect(admin_url('expenses/expense/' . $id));
        } else {
            redirect(admin_url('expenses/list_expenses/' . $id));
        }
    }
}
