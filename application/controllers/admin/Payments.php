<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payments extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('payments_model');
    }
    /* In case if user go only on /payments*/
    public function index($clientid = false)
    {
        $this->list_payments($clientid);
    }
    /* List all invoice paments */
    public function list_payments($clientid = false)
    {
        $has_permission = has_permission('manageSales');
        if (!$has_permission && !$this->input->is_ajax_request()) {
            access_denied('manageSales');
        }
        $_custom_view = '';
        if ($this->input->get('custom_view')) {
            $_custom_view = $this->input->get('custom_view');
        }
        if ($this->input->is_ajax_request()) {
            // From client profile
            if (is_numeric($clientid)) {
                if (!$has_permission) {
                    echo json_encode(json_decode('{"draw":1,"iTotalRecords":"0","iTotalDisplayRecords":"0","aaData":[]}'));
                    die;
                }
            }
            $this->perfex_base->get_table_data('payments', array(
                'clientid' => $clientid
            ));
        }
        $data['custom_view'] = $_custom_view;
        $data['title']       = _l('payments');
        $this->load->view('admin/payments/manage', $data);
    }
    /* Update payment data */
    public function payment($id = '')
    {
        if (!$id) {
            redirect(admin_url('payments'));
        }
        if ($this->input->post()) {
            $success = $this->payments_model->update($this->input->post(), $id);
            if ($success) {
                set_alert('success', _l('updated_successfuly', _l('payment')));
            }
            redirect(admin_url('payments/payment/' . $id));
        }
        $data['payment'] = $this->payments_model->get($id);
        if (!$data['payment']) {
            blank_page(_l('payment_not_exists'));
        }
        $this->load->model('invoices_model');
        $data['invoice'] = $this->invoices_model->get($data['payment']->invoiceid);
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get();
        $data['title']         = _l('edit', _l('payment_lowercase'));
        $this->load->view('admin/payments/payment', $data);
    }
    /**
     * Generate payment pdf
     * @since  Version 1.0.1
     * @param  mixed $id Payment id
     */
    public function pdf($id)
    {
        $payment = $this->payments_model->get($id);
        $this->load->model('invoices_model');
        $payment->invoice_data = $this->invoices_model->get($payment->invoiceid);
        $paymentpdf            = payment_pdf($payment);
        $type = 'D';
        if($this->input->get('print')){
            $type = 'I';
        }
        $paymentpdf->Output(strtoupper(slug_it(_l('payment') . '-' . $payment->paymentid)) . '.pdf', 'D');
    }
    /* Delete payment */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('payments'));
        }
        $response = $this->payments_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));
        }
        redirect(admin_url('payments'));
    }
}
