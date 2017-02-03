<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Invoices_model extends CRM_Model
{
    private $shipping_fields = array('shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country');
    function __construct()
    {
        parent::__construct();
    }
    /**
     * Get invoice by id
     * @param  mixed $id
     * @return array
     */
    public function get($id = '', $where = array())
    {
        $this->db->select('*, tblcurrencies.id as currencyid, tblinvoices.id as id, tblcurrencies.name as currency_name');
        $this->db->from('tblinvoices');
        $this->db->join('tblcurrencies', 'tblcurrencies.id = tblinvoices.currency', 'left');
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('tblinvoices' . '.id', $id);
            $invoice = $this->db->get()->row();
            if ($invoice) {
                $invoice->items = $this->get_invoice_items($id);
                $i              = 0;
                $this->load->model('payments_model');
                $this->load->model('clients_model');
                $invoice->client = $this->clients_model->get($invoice->clientid);
                if ($invoice->client) {
                    if ($invoice->client->company == '') {
                        $invoice->client->company = $invoice->client->firstname . ' ' . $invoice->client->lastname;
                    }
                }
                $invoice->payments = $this->payments_model->get_invoice_payments($id);
            }
            return $invoice;
        }
        return $this->db->get()->result_array();
    }
    /**
     * Get all invoice items
     * @param  mixed $id invoiceid
     * @return array
     */
    public function get_invoice_items($id)
    {
        $this->db->select('tblinvoiceitems.id,qty,rate,description as description,long_description,item_order');
        $this->db->from('tblinvoiceitems');
        $this->db->where('tblinvoiceitems.invoiceid', $id);
        $this->db->order_by('tblinvoiceitems.item_order', 'asc');
        return $this->db->get()->result_array();
    }
    public function get_invoice_item($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblinvoiceitems')->row();
    }
    public function mark_as_cancelled($id){
        $this->db->where('id',$id);
        $this->db->update('tblinvoices',array('status'=>5));
        if($this->db->affected_rows() > 0){
            return true;
        }

        return false;
    }
    public function unmark_as_cancelled($id){
        $this->db->where('id',$id);
        $this->db->update('tblinvoices',array('status'=>1));
        if($this->db->affected_rows() > 0){
            return true;
        }

        return false;
    }
    /**
     * Get this invoice generated recuring invoices
     * @since  Version 1.0.1
     * @param  mixed $id main invoice id
     * @return array
     */
    public function get_invoice_recuring_invoices($id)
    {
        $this->db->where('is_recurring_from', $id);
        $invoices          = $this->db->get('tblinvoices')->result_array();
        $recuring_invoices = array();
        foreach ($invoices as $invoice) {
            $recuring_invoices[] = $this->get($invoice['id']);
        }
        return $recuring_invoices;
    }
    /**
     * Get invoice total from all statuses
     * @since  Version 1.0.2
     * @param  mixed $data $_POST data
     * @return array
     */
    public function get_invoices_total($data)
    {
        $statuses = array(
            1,
            2,
            3,
            4
        );
        $this->load->model('currencies_model');
        if ((is_using_multiple_currencies() && !isset($data['currency'])) || !isset($data['currency'])) {
            $currencyid = $this->currencies_model->get_base_currency()->id;
        } else if (isset($data['currency'])) {
            $currencyid = $data['currency'];
        }

        $where_project = '';
        if ($this->input->post('project_id')) {
            $where_project = ' AND project_id=' . $this->input->post('project_id');
        }

        $symbol = $this->currencies_model->get_currency_symbol($currencyid);
        $sql    = 'SELECT';
        foreach ($statuses as $invoice_status) {
            $sql .= '(SELECT SUM(total) FROM tblinvoices WHERE status=' . $invoice_status;
            $sql .= ' AND currency =' . $currencyid . $where_project;
            $sql .= ') as "' . $invoice_status . '",';
        }
        $sql     = substr($sql, 0, -1);
        $result  = $this->db->query($sql)->result_array();
        $_result = array();
        $i       = 0;
        foreach ($result as $key => $val) {
            foreach ($val as $total) {
                $_result[$i]['total']  = $total;
                $_result[$i]['symbol'] = $symbol;
                $i++;
            }
        }

        return $_result;
    }
    /**
     * Insert new invoice to database
     * @param array $data invoiec data
     * @return mixed - false if not insert, invoice ID if succes
     */
    public function add($data, $expense = false)
    {
        $data['number'] = $data['_number'];
        if (isset($data['billed_tasks'])) {
            $billed_tasks = array_unique($data['billed_tasks']);
            unset($data['billed_tasks']);
        }
        if (isset($data['invoices_to_merge'])) {
            $invoices_to_merge = $data['invoices_to_merge'];
            unset($data['invoices_to_merge']);
            if(isset($data['cancel_merged_invoices'])){
                $cancel_merged_invoices = true;
                unset($data['cancel_merged_invoices']);
            }
        }
        $unsetters = array(
            '_number',
            'currency_symbol',
            'price',
            'taxname',
            'description',
            'long_description',
            'taxid',
            'rate',
            'quantity',
            'item_select',
            'billed_tasks',
            'task_select',
            'task_id'
        );
        foreach ($unsetters as $unseter) {
            if (isset($data[$unseter])) {
                unset($data[$unseter]);
            }
        }
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $data['hash'] = md5(rand() . microtime());
        // Check if the key exists
        $this->db->where('hash', $data['hash']);
        $exists = $this->db->get('tblinvoices')->row();
        if ($exists) {
            $data['hash'] = md5(rand() . microtime());
        }
        $data['adminnote'] = nl2br($data['adminnote']);
        if (isset($data['terms'])) {
            $data['terms'] = nl2br($data['terms']);
        }
        $data['date'] = to_sql_date($data['date']);
        if (!empty($data['duedate'])) {
            $data['duedate'] = to_sql_date($data['duedate']);
        } else {
            unset($data['duedate']);
        }
        if ($data['sale_agent'] == '') {
            $data['sale_agent'] = 0;
        }
        // Since version 1.0.1
        if (isset($data['allowed_payment_modes'])) {
            $data['allowed_payment_modes'] = serialize($data['allowed_payment_modes']);
        } else {
            $data['allowed_payment_modes'] = serialize(array());
        }
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['year']        = get_option('invoice_year');
        if (!DEFINED('CRON')) {
            $data['addedfrom'] = get_staff_user_id();
        }
        $items = array();
        if (isset($data['newitems'])) {
            $items = $data['newitems'];
            unset($data['newitems']);
        }
        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = NULL;
                }
            }
            $data['show_shipping_on_invoice'] = 1;
            $data['include_shipping']         = 0;
        } else {
            // we dont need to overwrite to 1 unless its coming from the main function add
            if (!DEFINED('CRON') && $expense == false) {
                $data['include_shipping'] = 1;
                // set by default for the next time to be checked
                if (isset($data['show_shipping_on_invoice'])) {
                    $data['show_shipping_on_invoice'] = 1;
                } else {
                    $data['show_shipping_on_invoice'] = 0;
                }
            }
            // else its just like they are passed
        }
        if ($data['discount_total'] == 0) {
            $data['discount_type'] = '';
        }
        $_data = do_action('before_invoice_added', array(
            'data' => $data,
            'items' => $items
        ));
        $data  = $_data['data'];
        $items = $_data['items'];
        $this->db->insert('tblinvoices', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            if (isset($invoices_to_merge)) {
                foreach ($invoices_to_merge as $m) {
                    $or_merge = $this->get($m);
                    if(!isset($cancel_merged_invoices)){
                        if ($this->delete($m, true)) {

                            $this->db->where('invoiceid', $or_merge->id);
                            $is_expense_invoice = $this->db->get('tblexpenses')->row();
                            if ($is_expense_invoice) {
                                $this->db->where('id', $is_expense_invoice->id);
                                $this->db->update('tblexpenses', array(
                                    'invoiceid' => $insert_id
                                    ));
                            }

                            if (total_rows('tblestimates', array(
                                'invoiceid' => $or_merge->id
                                )) > 0) {
                                $this->db->where('invoiceid', $or_merge->id);
                            $estimate = $this->db->get('tblestimates')->row();
                            $this->db->where('id', $estimate->id);
                            $this->db->update('tblestimates', array(
                                'invoiceid' => $insert_id
                                ));
                        } else if (total_rows('tblproposals', array(
                            'invoice_id' => $or_merge->id
                            )) > 0) {
                            $this->db->where('invoice_id', $or_merge->id);
                            $proposal = $this->db->get('tblproposals')->row();
                            $this->db->where('id', $proposal->id);
                            $this->db->update('tblproposals', array(
                                'invoice_id' => $insert_id
                                ));
                        }
                    }
                } else {
                    $this->mark_as_cancelled($m);
                    $admin_note = $or_merge->adminnote;
                    $note = 'Merged into invoice '.format_invoice_number($id);
                    if($admin_note != ''){
                        $admin_note .= "\n\r".$note;
                    } else {
                        $admin_note = $note;
                    }
                    $this->db->where('id',$m);
                    $this->db->update('tblinvoices',array('adminnote'=>$admin_note));
                }
            }
        }

            if (isset($billed_tasks)) {
                $this->load->model('tasks_model');
                foreach ($billed_tasks as $task) {
                    $_task = $this->tasks_model->get($task);

                    $_task_data = array(
                        'billed' => 1,
                        'invoice_id' => $insert_id
                    );

                    if ($_task->finished == 0) {
                        $_task_data['finished']     = 1;
                        $_task_data['datefinished'] = date('Y-m-d H:i:s');
                    }

                    $this->db->where('id', $task);
                    $this->db->update('tblstafftasks', $_task_data);
                }
            }
            // Update next invoice number in settings
            $this->db->where('name', 'next_invoice_number');
            $this->db->set('value', 'value+1', FALSE);
            $this->db->update('tbloptions');
            update_invoice_status($insert_id);
            if (count($items) > 0) {
                foreach ($items as $key => $item) {
                    $this->db->insert('tblinvoiceitems', array(
                        'description' => $item['description'],
                        'long_description' => nl2br($item['long_description']),
                        'qty' => $item['qty'],
                        'rate' => $item['rate'],
                        'invoiceid' => $insert_id,
                        'item_order' => $item['order']
                    ));
                    $itemid = $this->db->insert_id();
                    if ($itemid) {
                        foreach ($item['taxname'] as $taxname) {
                            if ($taxname != '') {
                                $_temp    = explode('|', $taxname);
                                $tax_name = $_temp[0];
                                $tax_rate = $_temp[1];
                                $this->db->insert('tblinvoiceitemstaxes', array(
                                    'itemid' => $itemid,
                                    'taxrate' => $tax_rate,
                                    'taxname' => $tax_name,
                                    'invoice_id' => $insert_id
                                ));
                            }
                        }
                    }
                }
            }
            if (!DEFINED('CRON') && $expense == false) {
                $this->log_invoice_activity($insert_id, _l('invoice_activity_created'));
            } else if (!DEFINED('CRON') && $expense == true) {
                $this->log_invoice_activity($insert_id, _l('invoice_activity_from_expense'));
            } else if (DEFINED('CRON') && $expense == false) {
                $this->log_invoice_activity($insert_id, _l('invoice_activity_recuring_created'));
            } else {
                $this->log_invoice_activity($insert_id, _l('invoice_activity_recuring_from_expense_created'));
            }
            do_action('after_invoice_added', $insert_id);
            return $insert_id;
        }
        return false;
    }

    public function check_for_merge_invoice($client_id, $current_invoice)
    {
         if ($current_invoice != 'undefined') {
            $this->db->select('status');
            $this->db->where('id',$current_invoice);
            $row = $this->db->get('tblinvoices')->row();
            // Cant merge on paid invoice and partialy paid and cancelled
            if($row->status == 2 || $row->status == 3 || $row->status == 5){
                return array();
            }
         }
        $statuses = array(
            1,
            4
        );
        $this->db->select('id');
        $this->db->where('clientid', $client_id);
        $this->db->where('STATUS IN (' . implode(', ', $statuses) . ')');
        if ($current_invoice != 'undefined') {
            $this->db->where('id !=', $current_invoice);
        }
        $invoices  = $this->db->get('tblinvoices')->result_array();
        $_invoices = array();
        foreach ($invoices as $invoice) {
            $_invoices[] = $this->get($invoice['id']);
        }
        return $_invoices;
    }
    /**
     * Copy invoice
     * @param  mixed $id invoice id to copy
     * @return mixed
     */
    public function copy($id)
    {
        $_invoice                     = $this->get($id);
        $new_invoice_data             = array();
        $new_invoice_data['clientid'] = $_invoice->clientid;
        $new_invoice_data['_number']  = get_option('next_invoice_number');
        $new_invoice_data['date']     = _d(date('Y-m-d'));
        if ($_invoice->duedate) {
            if (get_option('invoice_due_after') != 0) {
                $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
            }
        }
        $new_invoice_data['show_quantity_as'] = $_invoice->show_quantity_as;
        $new_invoice_data['currency']         = $_invoice->currency;
        $new_invoice_data['subtotal']         = $_invoice->subtotal;
        $new_invoice_data['total']            = $_invoice->total;
        $new_invoice_data['adminnote']        = $_invoice->adminnote;
        $new_invoice_data['adjustment']       = $_invoice->adjustment;
        $new_invoice_data['discount_percent'] = $_invoice->discount_percent;
        $new_invoice_data['discount_total']   = $_invoice->discount_total;
        $new_invoice_data['recurring']        = $_invoice->recurring;
        $new_invoice_data['discount_type']    = $_invoice->discount_type;
        $new_invoice_data['terms']            = $_invoice->terms;
        $new_invoice_data['sale_agent']       = $_invoice->sale_agent;
        // Since version 1.0.6
        $new_invoice_data['billing_street']   = $_invoice->billing_street;
        $new_invoice_data['billing_city']     = $_invoice->billing_city;
        $new_invoice_data['billing_state']    = $_invoice->billing_state;
        $new_invoice_data['billing_zip']      = $_invoice->billing_zip;
        $new_invoice_data['billing_country']  = $_invoice->billing_country;
        $new_invoice_data['shipping_street']  = $_invoice->shipping_street;
        $new_invoice_data['shipping_city']    = $_invoice->shipping_city;
        $new_invoice_data['shipping_state']   = $_invoice->shipping_state;
        $new_invoice_data['shipping_zip']     = $_invoice->shipping_zip;
        $new_invoice_data['shipping_country'] = $_invoice->shipping_country;
        if ($_invoice->include_shipping == 1) {
            $new_invoice_data['include_shipping'] = $_invoice->include_shipping;
        }
        $new_invoice_data['show_shipping_on_invoice'] = $_invoice->show_shipping_on_invoice;
        // Set to unpaid status automatically
        $new_invoice_data['status']                   = 1;
        $new_invoice_data['clientnote']               = $_invoice->clientnote;
        $new_invoice_data['adminnote']                = $_invoice->adminnote;
        $new_invoice_data['allowed_payment_modes']    = unserialize($_invoice->allowed_payment_modes);
        $new_invoice_data['newitems']                 = array();
        $key                                          = 1;
        foreach ($_invoice->items as $item) {
            $new_invoice_data['newitems'][$key]['description']      = $item['description'];
            $new_invoice_data['newitems'][$key]['long_description'] = $item['long_description'];
            $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
            $new_invoice_data['newitems'][$key]['taxname']          = array();
            $taxes                                                  = get_invoice_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_invoice_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_invoice_data['newitems'][$key]['rate']  = $item['rate'];
            $new_invoice_data['newitems'][$key]['order'] = $item['item_order'];
            $key++;
        }
        $id = $this->invoices_model->add($new_invoice_data);
        if ($id) {
            $custom_fields = get_custom_fields('invoice');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($_invoice->id, $field['id'], 'invoice');
                if ($value == '') {
                    continue;
                }
                $this->db->insert('tblcustomfieldsvalues', array(
                    'relid' => $id,
                    'fieldid' => $field['id'],
                    'fieldto' => 'invoice',
                    'value' => $value
                ));
            }
            logActivity('Copied Invoice ' . format_invoice_number($_invoice->id));
            return $id;
        }
        return false;
    }
    /**
     * Update invoice data
     * @param  array $data invoice data
     * @param  mixed $id   invoiceid
     * @return boolean
     */
    public function update($data, $id)
    {
        $original_invoice = $this->get($id);
        if ($original_invoice->status == 2 && !is_admin()) {
            return false;
        }

        if (isset($data['invoices_to_merge'])) {
            $invoices_to_merge = $data['invoices_to_merge'];
            unset($data['invoices_to_merge']);

             if(isset($data['cancel_merged_invoices'])){
                $cancel_merged_invoices = true;
                unset($data['cancel_merged_invoices']);
            }

        }

        $affectedRows             = 0;
        $prefix                   = get_option('invoice_prefix');
        $data['number']           = substr($data['number'], strlen($prefix));
        $data['number']           = trim($data['number']);
        $original_number_formated = format_invoice_number($id);
        if (get_option('invoice_number_format') == 2) {
            $_temp_number   = explode('/', $data['number']);
            $data['number'] = $_temp_number[1];
        }
        $original_number = $original_invoice->number;
        if (isset($data['billed_tasks'])) {
            $billed_tasks = $data['billed_tasks'];
            unset($data['billed_tasks']);
        }
        unset($data['currency_symbol']);
        unset($data['price']);
        unset($data['taxname']);
        unset($data['taxid']);
        unset($data['isedit']);
        unset($data['description']);
        unset($data['long_description']);
        unset($data['tax']);
        unset($data['rate']);
        unset($data['quantity']);
        unset($data['item_select']);
        unset($data['task_select']);
        unset($data['task_id']);
        if (isset($data['merge_current_invoice'])) {
            unset($data['merge_current_invoice']);
        }
        $items = array();
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }
        $newitems = array();
        if (isset($data['newitems'])) {
            $newitems = $data['newitems'];
            unset($data['newitems']);
        }
        if ($data['adjustment'] == 'NaN') {
            $data['adjustment'] = 0;
        }
        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = NULL;
                }
            }
            $data['show_shipping_on_invoice'] = 1;
            $data['include_shipping']         = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_invoice'])) {
                $data['show_shipping_on_invoice'] = 1;
            } else {
                $data['show_shipping_on_invoice'] = 0;
            }
        }
        if ($data['sale_agent'] == '') {
            $data['sale_agent'] = 0;
        }
        // Since version 1.0.1
        if (isset($data['allowed_payment_modes'])) {
            $data['allowed_payment_modes'] = serialize($data['allowed_payment_modes']);
        } else {
            $data['allowed_payment_modes'] = serialize(array());
        }
        if (isset($data['terms'])) {
            $data['terms'] = nl2br($data['terms']);
        }
        $data['date']    = to_sql_date($data['date']);
        $data['duedate'] = to_sql_date($data['duedate']);
        if ($data['discount_total'] == 0) {
            $data['discount_type'] = '';
        }
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        $action_data = array(
            'data' => $data,
            'newitems' => $newitems,
            'items' => $items,
            'id' => $id,
            'removed_items' => array()
        );
        if (isset($data['removed_items'])) {
            $action_data['removed_items'] = $data['removed_items'];
        }
        $_data                 = do_action('before_invoice_updated', $action_data);
        $data['removed_items'] = $_data['removed_items'];
        $newitems              = $_data['newitems'];
        $items                 = $_data['items'];
        $data                  = $_data['data'];
        if (isset($billed_tasks)) {
            foreach ($billed_tasks as $task) {
                $this->db->where('id', $task);
                $this->db->update('tblstafftasks', array(
                    'billed' => 1,
                    'invoice_id' => $id
                ));
            }
        }
        // Delete items checked to be removed from database
        if (isset($data['removed_items'])) {
            foreach ($data['removed_items'] as $remove_item_id) {
                $original_item = $this->get_invoice_item($remove_item_id);
                $this->db->where('invoiceid', $id);
                $this->db->where('id', $remove_item_id);
                $this->db->delete('tblinvoiceitems');
                if ($this->db->affected_rows() > 0) {
                    $this->log_invoice_activity($id, _l('invoice_estimate_activity_removed_item', $original_item->description));
                    $affectedRows++;
                    $this->db->where('itemid', $remove_item_id);
                    $this->db->delete('tblinvoiceitemstaxes');
                }
                $this->db->where('itemid', $remove_item_id);
                $this->db->delete('tblinvoiceitemstaxes');
            }
            unset($data['removed_items']);
        }
        $this->db->where('id', $id);
        $this->db->update('tblinvoices', $data);


        if ($this->db->affected_rows() > 0) {
            update_invoice_status($id);
            $affectedRows++;
            if ($original_number != $data['number']) {
                $activity = _l('invoice_activity_number_changed', array(
                    $original_number_formated,
                    format_invoice_number($original_invoice->id)
                ));
                $this->log_invoice_activity($original_invoice->id, $activity);
            }
        }
        $this->load->model('taxes_model');
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $invoice_item_id = $item['itemid'];
                $original_item   = $this->get_invoice_item($invoice_item_id);
                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'item_order' => $item['order']
                ));
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
                // Check for invoice item short description change
                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'description' => $item['description']
                ));
                if ($this->db->affected_rows() > 0) {
                    $activity = _l('invoice_estimate_activity_updated_item_short_description', array(
                        $original_item->description,
                        $item['description']
                    ));
                    $this->log_invoice_activity($id, $activity);
                    $affectedRows++;
                }
                // Check for item long description change
                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'long_description' => nl2br($item['long_description'])
                ));
                if ($this->db->affected_rows() > 0) {
                    $activity = _l('invoice_estimate_activity_updated_item_long_description', array(
                        $original_item->long_description,
                        $item['long_description']
                    ));
                    $this->log_invoice_activity($id, $activity);
                    $affectedRows++;
                }
                if (count($item['taxname']) == 0) {
                    $this->db->where('itemid', $invoice_item_id);
                    $this->db->delete('tblinvoiceitemstaxes');
                } else {
                    $item_taxes        = get_invoice_item_taxes($invoice_item_id);
                    $_item_taxes_names = array();
                    foreach ($item_taxes as $_item_tax) {
                        array_push($_item_taxes_names, $_item_tax['taxname']);
                    }
                    $i = 0;
                    foreach ($_item_taxes_names as $_item_tax) {
                        if (!in_array($_item_tax, $item['taxname'])) {
                            $this->db->where('id', $item_taxes[$i]['id']);
                            $this->db->delete('tblinvoiceitemstaxes');
                            if ($this->db->affected_rows() > 0) {
                                $affectedRows++;
                            }
                        }
                        $i++;
                    }
                    foreach ($item['taxname'] as $taxname) {
                        if ($taxname != '') {
                            $_temp    = explode('|', $taxname);
                            $tax_name = $_temp[0];
                            $tax_rate = $_temp[1];
                            if (total_rows('tblinvoiceitemstaxes', array(
                                'taxname' => $tax_name,
                                'itemid' => $invoice_item_id,
                                'taxrate' => $tax_rate
                            )) == 0) {
                                $this->db->insert('tblinvoiceitemstaxes', array(
                                    'taxrate' => $tax_rate,
                                    'taxname' => $tax_name,
                                    'itemid' => $invoice_item_id,
                                    'invoice_id' => $id
                                ));
                                if ($this->db->affected_rows() > 0) {
                                    $affectedRows++;
                                }
                            }
                        }
                    }
                }
                // Check for item rate change
                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'rate' => $item['rate']
                ));
                if ($this->db->affected_rows() > 0) {
                    $activity = _l('invoice_estimate_activity_updated_item_rate', array(
                        $original_item->rate,
                        $item['rate']
                    ));
                    $this->log_invoice_activity($id, $activity);
                    $affectedRows++;
                }
                // CHeck for invoice quantity change
                $this->db->where('id', $invoice_item_id);
                $this->db->update('tblinvoiceitems', array(
                    'qty' => $item['qty']
                ));
                if ($this->db->affected_rows() > 0) {
                    $activity = _l('invoice_estimate_activity_updated_qty_item', array(
                        $item['description'],
                        $original_item->qty,
                        $item['qty']
                    ));
                    $this->log_invoice_activity($id, $activity);
                    $affectedRows++;
                }
            }
        }
        if (count($newitems) > 0) {
            foreach ($newitems as $key => $item) {
                $this->db->insert('tblinvoiceitems', array(
                    'description' => $item['description'],
                    'long_description' => nl2br($item['long_description']),
                    'qty' => $item['qty'],
                    'rate' => $item['rate'],
                    'invoiceid' => $id,
                    'item_order' => $item['order']
                ));
                $new_item_added = $this->db->insert_id();
                if ($new_item_added) {
                    foreach ($item['taxname'] as $taxname) {
                        if($taxname != ''){
                            $_temp    = explode('|', $taxname);
                            $tax_name = $_temp[0];
                            $tax_rate = $_temp[1];
                            $this->db->insert('tblinvoiceitemstaxes', array(
                                'taxrate' => $tax_rate,
                                'taxname' => $tax_name,
                                'itemid' => $new_item_added,
                                'invoice_id' => $id
                            ));
                        }
                    }
                    $activity = _l('invoice_estimate_activity_added_item', $item['description']);
                    $this->log_invoice_activity($id, $activity);
                    $affectedRows++;
                }
            }
        }
        if (isset($invoices_to_merge)) {
            foreach ($invoices_to_merge as $m) {
                $or_merge = $this->get($m);
                if(!isset($cancel_merged_invoices)){
                    if ($this->delete($m, true)) {

                        $this->db->where('invoiceid', $or_merge->id);
                        $is_expense_invoice = $this->db->get('tblexpenses')->row();
                        if ($is_expense_invoice) {
                            $this->db->where('id', $is_expense_invoice->id);
                            $this->db->update('tblexpenses', array(
                                'invoiceid' => $id
                                ));
                        }
                        if (total_rows('tblestimates', array(
                            'invoiceid' => $or_merge->id
                            )) > 0) {
                            $this->db->where('invoiceid', $or_merge->id);
                        $estimate = $this->db->get('tblestimates')->row();
                        $this->db->where('id', $estimate->id);
                        $this->db->update('tblestimates', array(
                            'invoiceid' => $id
                            ));
                    } else if (total_rows('tblproposals', array(
                        'invoice_id' => $or_merge->id
                        )) > 0) {
                        $this->db->where('invoice_id', $or_merge->id);
                        $proposal = $this->db->get('tblproposals')->row();
                        $this->db->where('id', $proposal->id);
                        $this->db->update('tblproposals', array(
                            'invoice_id' => $id
                            ));
                    }
                }
            } else {
                $this->mark_as_cancelled($m);
                $admin_note = $or_merge->adminnote;
                $note = 'Merged into invoice '.format_invoice_number($id);
                if($admin_note != ''){
                    $admin_note .= "\n\r".$note;
                } else {
                    $admin_note = $note;
                }
                $this->db->where('id',$m);
                $this->db->update('tblinvoices',array('adminnote'=>$admin_note));
            }
        }
    }

        if ($affectedRows > 0) {
            do_action('after_invoice_updated', $id);
            return true;
        }
        return false;
    }
    public function get_attachments($invoiceid, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('invoiceid', $invoiceid);
        }
        $result = $this->db->get('tblinvoiceattachments');
        if (is_numeric($id)) {
            return $result->row();
        } else {
            return $result->result_array();
        }
    }
    /**
     *  Delete invoice attachment
     * @since  Version 1.0.4
     * @param   mixed $id  attachmentid
     * @return  boolean
     */
    public function delete_attachment($id)
    {
        $attachment = $this->get_attachments('', $id);
        if ($attachment) {
            if (unlink(INVOICE_ATTACHMENTS_FOLDER . $attachment->invoiceid . '/' . $attachment->file_name)) {
                $this->db->where('id', $id);
                $this->db->delete('tblinvoiceattachments');
                $other_attachments = list_files(INVOICE_ATTACHMENTS_FOLDER . $attachment->invoiceid);
                if (count($other_attachments) == 0) {
                    // delete the dir if no other attachments found
                    delete_dir(INVOICE_ATTACHMENTS_FOLDER . $attachment->invoiceid);
                }
            }
            return true;
        }
        return false;
    }
    /**
     * Delete invoice items and all connections
     * @param  mixed $id invoiceid
     * @return boolean
     */
    public function delete($id, $merge = false)
    {
        if (get_option('delete_only_on_last_invoice') == 1 && $merge == false) {
            if (!is_last_invoice($id)) {
                return false;
            }
        }
        do_action('before_invoice_deleted', $id);
        $this->db->where('id', $id);
        $this->db->delete('tblinvoices');
        if ($this->db->affected_rows() > 0) {

            if (get_option('invoice_number_decrement_on_delete') == 1 && $merge == false) {
                $current_next_invoice_number = get_option('next_invoice_number');
                if ($current_next_invoice_number > 1) {
                    // Decrement next invoice number to
                    $this->db->where('name', 'next_invoice_number');
                    $this->db->set('value', 'value-1', FALSE);
                    $this->db->update('tbloptions');
                }
            }
            if ($merge == false) {
                // Check if is invoice from expense

                $this->db->where('invoiceid', $id);
                $is_expense_invoice = $this->db->get('tblexpenses')->row();
                if ($is_expense_invoice) {
                    $this->db->where('id', $is_expense_invoice->id);
                    $this->db->update('tblexpenses', array(
                        'invoiceid' => NULL
                    ));
                }
                // if is converted from estimate set the estimate invoice to null
                if (total_rows('tblestimates', array(
                    'invoiceid' => $id
                )) > 0) {
                    $this->db->where('invoiceid', $id);
                    $estimate = $this->db->get('tblestimates')->row();
                    $this->db->where('id', $estimate->id);
                    $this->db->update('tblestimates', array(
                        'invoiceid' => NULL,
                        'invoiced_date' => NULL
                    ));

                    $this->load->model('estimates_model');
                    $this->estimates_model->log_estimate_activity($estimate->id, 'deleted the created invoice');

                } else if (total_rows('tblproposals', array(
                        'invoice_id' => $id
                    )) > 0) {
                    $this->db->where('invoice_id', $id);
                    $estimate = $this->db->get('tblproposals')->row();
                    $this->db->where('id', $estimate->id);
                    $this->db->update('tblproposals', array(
                        'invoice_id' => NULL,
                        'date_converted' => NULL
                    ));
                }
            }
            $this->db->where('invoice_id', $id);
            $this->db->delete('tblinvoiceitemstaxes');
            $this->db->where('invoiceid', $id);
            $this->db->delete('tblinvoiceitems');
            $this->db->where('invoiceid', $id);
            $this->db->delete('tblinvoicepaymentrecords');
            $this->db->where('invoiceid', $id);
            $this->db->delete('tblinvoiceactivity');
            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'invoice');
            $this->db->delete('tblcustomfieldsvalues');
            $this->db->where('invoice_id', $id);
            $this->db->delete('tblinvoiceitemstaxes');
            // Get billed tasks for this invoice and set to unbilled
            $this->db->where('invoice_id', $id);
            $tasks = $this->db->get('tblstafftasks')->result_array();
            foreach ($tasks as $task) {
                $this->db->where('id', $task['id']);
                $this->db->update('tblstafftasks', array(
                    'invoice_id' => NULL,
                    'billed' => 0
                ));
            }
            // Get related tasks
            $this->load->model('tasks_model');
            $this->db->where('rel_type', 'invoice');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get('tblstafftasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }
            return true;
        }
        return false;
    }
    /**
     * Set invoice to sent when email is successfuly sended to client
     * @param mixed $id invoiceid
     * @param  mixed $manually is staff manualy marking this invoice as sent
     * @return  boolean
     */
    public function set_invoice_sent($id, $manually = false)
    {
        $this->db->where('id', $id);
        $this->db->update('tblinvoices', array(
            'sent' => 1,
            'datesend' => date('Y-m-d H:i:s')
        ));
        $marked = false;
        if ($this->db->affected_rows() > 0) {
            $marked = true;
        }
        if (DEFINED('CRON')) {
            $description = _l('invoice_activity_sent_to_client_cron');
        } else {
            if ($manually == false) {
                $description = _l('invoice_activity_sent_to_client') . ' - ' . $this->input->post('sent_to');
            } else {
                $description = _l('invoice_activity_marked_as_sent');
            }
        }
        $this->log_invoice_activity($id, $description);
        return $marked;
    }
    /**
     * Sent overdue notice to client for this invoice
     * @since  Since Version 1.0.1
     * @param  mxied  $id   invoiceid
     * @return boolean
     */
    public function send_invoice_overdue_notice($id)
    {
        $this->load->model('emails_model');
        $invoice        = $this->get($id);
        $invoice_number = format_invoice_number($invoice->id);
        $pdf            = invoice_pdf($invoice);
        $attach         = $pdf->Output($invoice_number . '.pdf', 'S');
        $this->emails_model->add_attachment(array(
            'attachment' => $attach,
            'filename' => $invoice_number . '.pdf',
            'type' => 'application/pdf'
        ));
        $send = $this->emails_model->send_email_template('invoice-overdue-notice', $invoice->client->email, $invoice->clientid, $id);
        if ($send) {
            if (DEFINED('CRON')) {
                $_from = '[CRON]';
            } else {
                $_from = get_staff_full_name();
            }
            $this->db->where('id', $id);
            $this->db->update('tblinvoices', array(
                'last_overdue_reminder' => date('Y-m-d')
            ));
            $this->log_invoice_activity($id, _l('user_sent_overdue_reminder', $_from));
            return true;
        }
        return false;
    }
    /**
     * Sent invoice to client
     * @param  mixed  $id        invoiceid
     * @param  string  $template  email template to sent
     * @param  boolean $attachpdf attach invoice pdf or not
     * @return boolean
     */
    public function sent_invoice_to_client($id, $template = '', $attachpdf = true)
    {
        $this->load->model('emails_model');
        $invoice = $this->get($id);
        if ($template == '') {
            if ($invoice->sent == 0) {
                $template = 'invoice-send-to-client';
            } else {
                $template = 'invoice-already-send';
            }

            $template = do_action('after_invoice_sent_template_statement', $template);
        }

        $invoice_number = format_invoice_number($invoice->id);
        $pdf            = invoice_pdf($invoice);
        if ($attachpdf) {
            $attach = $pdf->Output($invoice_number . '.pdf', 'S');
            $this->emails_model->add_attachment(array(
                'attachment' => $attach,
                'filename' => $invoice_number . '.pdf',
                'type' => 'application/pdf'
            ));
        }
        if ($this->input->post('email_attachments')) {
            $_other_attachments = $this->input->post('email_attachments');
            foreach ($_other_attachments as $attachment) {
                $_attachment = $this->get_attachments($id, $attachment);
                $this->emails_model->add_attachment(array(
                    'attachment' => INVOICE_ATTACHMENTS_FOLDER . $id . '/' . $_attachment->file_name,
                    'filename' => $_attachment->file_name,
                    'type' => $_attachment->filetype,
                    'read' => true
                ));
            }
        }
        $email = $invoice->client->email;
        if ($this->input->post('sent_to')) {
            $email = $this->input->post('sent_to');
        }

        $send = $this->emails_model->send_email_template($template, $email, $invoice->clientid, $id);
        if ($send) {
            $this->set_invoice_sent($id);
            return true;
        }
        return false;
    }
    /**
     * All invoice activity
     * @param  mixed $id invoiceid
     * @return array
     */
    public function get_invoice_activity($id)
    {
        $this->db->where('invoiceid', $id);
        $this->db->order_by('date', 'asc');
        return $this->db->get('tblinvoiceactivity')->result_array();
    }
    /**
     * Log invoice activity to database
     * @param  mixed $id   invoiceid
     * @param  string $description activity description
     */
    public function log_invoice_activity($id, $description = '', $client = false)
    {
        $staffid = get_staff_user_id();
        if (DEFINED('CRON')) {
            $staffid = 'Cron Job';
        } else if ($client == true) {
            $staffid = NULL;
        }
        $this->db->insert('tblinvoiceactivity', array(
            'description' => $description,
            'date' => date('Y-m-d H:i:s'),
            'invoiceid' => $id,
            'staffid' => $staffid
        ));
    }
}
