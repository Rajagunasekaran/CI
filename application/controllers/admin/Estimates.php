<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Estimates extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('estimates_model');
    }
    /* Get all estimates in case user go on index page */
    public function index($id = false, $clientid = false)
    {
        $this->list_estimates($id, $clientid);
    }
    /* List all estimates datatables */
    public function list_estimates($id = false, $clientid = false)
    {
        $has_permission = has_permission('manageSales');
        if (!$has_permission && !$this->input->is_ajax_request()) {
            access_denied('manageSales');
        }
        $_status = '';
        if ($this->input->get('status')) {
            $_status = $this->input->get('status');
        }
        if ($this->input->is_ajax_request()) {
            // From client profile
            if (is_numeric($clientid)) {
                if (!$has_permission) {
                    echo json_encode(json_decode('{"draw":1,"iTotalRecords":"0","iTotalDisplayRecords":"0","aaData":[]}'));
                    die;
                }
            }
            $this->perfex_base->get_table_data('estimates', array(
                'clientid' => $clientid,
                'id' => $id
            ));
        }
        $data['estimateid'] = '';
        if (is_numeric($id)) {
            $data['estimateid'] = $id;
        }
        $data['status'] = $_status;
        $data['title']  = _l('estimates');
        $this->load->view('admin/estimates/manage', $data);
    }
    /* Add new estimate or update existing */
    public function estimate($id = '')
    {
        if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->estimates_model->add($this->input->post());
                if ($id) {
                    $estimates = $this->estimates_model->get($id);
                    if ($estimates->number != $this->input->post('_number')) {
                        set_alert('warning', _l('estimate_number_changed'));
                    } else {
                        set_alert('success', _l('added_successfuly', _l('estimate')));
                    }
                    redirect(admin_url('estimates/list_estimates/' . $id));
                }
            } else {
                $success = $this->estimates_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('estimate')));
                }
                redirect(admin_url('estimates/list_estimates/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('create_new_estimate');
        } else {
            $estimates             = $this->estimates_model->get($id);
            $estimates->date       = _d($estimates->date);
            $estimates->expirydate = _d($estimates->expirydate);
            $this->load->model('emails_model');
            if ($estimates->sent == 0) {
                $data['template'] = $this->emails_model->parse_template('estimate-send-to-client', $estimates->clientid, false, false, false, $id);
            } else {
                $data['template'] = $this->emails_model->parse_template('estimate-already-send', $estimates->clientid, false, false, false, $id);
            }
            $data['estimate'] = $estimates;
            $data['edit']     = true;
            $title            = _l('edit', _l('estimate_lowercase'));
        }
        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $this->load->model('invoice_items_model');
        $data['items'] = $this->invoice_items_model->get();
        $this->load->model('clients_model');
        $data['clients'] = $this->clients_model->get();
        $this->load->model('staff_model');
        $data['staff'] = $this->staff_model->get('', 1);
        $data['title'] = $title;
        $this->load->view('admin/estimates/estimate', $data);
    }
    public function init_estimate_items_ajax($id)
    {
        echo json_encode($this->estimates_model->get_estimate_items($id));
    }
    /* Get all estimate data used when user click on estimate number in a datatable left side*/
    public function get_estimate_data_ajax($id)
    {
        if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if (!$id) {
            die('No estimate found');
        }
        $estimate = $this->estimates_model->get($id);
        if (!$estimate) {
            echo 'Estimate Not Found';
            die;
        }
        $estimate->date       = _d($estimate->date);
        $estimate->expirydate = _d($estimate->expirydate);
        if ($estimate->invoiceid !== NULL) {
            $this->load->model('invoices_model');
            $estimate->invoice = $this->invoices_model->get($estimate->invoiceid);
        }
        $this->load->model('emails_model');
        if ($estimate->sent == 0) {
            $data['template'] = $this->emails_model->parse_template('estimate-send-to-client', $estimate->clientid, false, false, false, $id);
        } else {
            $data['template'] = $this->emails_model->parse_template('estimate-already-send', $estimate->clientid, false, false, false, $id);
        }
        $data['activity'] = $this->estimates_model->get_estimate_activity($id);
        $data['estimate'] = $estimate;
        $this->load->view('admin/estimates/estimate_preview_template', $data);
    }
    public function get_estimates_total()
    {
        if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if ($this->input->post()) {
            $data['totals'] = $this->estimates_model->get_estimates_total($this->input->post());
            $this->load->model('currencies_model');
            $base_currency = $this->currencies_model->get_base_currency()->id;
            if (is_using_multiple_currencies('tblestimates') || (total_rows('tblestimates', array(
                'currency' => $base_currency
            )) == 0) && total_rows('tblestimates') > 0) {
                $this->load->model('currencies_model');
                $data['currencies'] = $this->currencies_model->get();
            }
            $this->load->view('admin/estimates/estimates_total_template', $data);
        }
    }
    public function mark_action_status($status, $id)
    {
        $success = $this->estimates_model->mark_action_status($status, $id);
        if ($success) {
            set_alert('success', _l('estimate_status_changed_success'));
        } else {
            set_alert('danger', _l('estimate_status_changed_fail'));
        }
        redirect(admin_url('estimates/list_estimates/' . $id));
    }
    /* Send estimate to email */
    public function send_to_email($id)
    {
        if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        $success = $this->estimates_model->sent_estimate_to_client($id, '', $this->input->post('attach_pdf'));
        if ($success) {
            set_alert('success', _l('estimate_sent_to_client_success'));
        } else {
            set_alert('danger', _l('estimate_sent_to_client_fail'));
        }
        redirect(admin_url('estimates/list_estimates/' . $id));
    }
    /* Convert estimate to invoice */
    public function convert_to_invoice($id)
    {
        if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if (!$id) {
            die('No estimate found');
        }
        $invoiceid = $this->estimates_model->convert_to_invoice($id);
        if ($invoiceid) {
            set_alert('success', _l('estimate_convert_to_invoice_successfuly'));
            redirect(admin_url('invoices/list_invoices/' . $invoiceid));
        } else {
            redirect(admin_url('invoices/list_estimates/' . $id));
        }
    }
    function copy($id)
    {
        if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if (!$id) {
            die('No estimate found');
        }
        $new_id = $this->estimates_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('estimate_copied_successfuly'));
            redirect(admin_url('estimates/estimate/' . $new_id));
        }
        set_alert('danger', _l('estimate_copied_fail'));
        redirect(admin_url('estimates/list_Estimates/' . $id));
    }
    /* Delete estimate */
    public function delete($id)
    {
        if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if (!$id) {
            redirect(admin_url('estimates/list_estimates'));
        }
        $success = $this->estimates_model->delete($id);
        if (is_array($success)) {
            set_alert('warning', _l('is_invoiced_estimate_delete_error'));
        } else if ($success == true) {
            set_alert('success', _l('deleted', _l('estimate')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('estimate_lowercase')));
        }
        redirect(admin_url('estimates/list_estimates'));
    }
    /* Generates estimate PDF and senting to email of $send_to_email = true is passed */
    public function pdf($id)
    {
        if (!has_permission('manageSales')) {
            access_denied('manageSales');
        }
        if (!$id) {
            redirect(admin_url('estimates/list_estimates'));
        }
        $estimate        = $this->estimates_model->get($id);
        $estimate_number = format_estimate_number($estimate->id);
        $pdf             = estimate_pdf($estimate);
        $type = 'D';
        if($this->input->get('print')){
            $type='I';
        }

        $pdf->Output(strtoupper(slug_it($estimate_number)) . '.pdf', $type);
    }

}
