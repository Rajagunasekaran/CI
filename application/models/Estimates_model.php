<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Estimates_model extends CRM_Model
{
    private $shipping_fields = array('shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country');
    function __construct()
    {
        parent::__construct();
        $this->load->model('emails_model');
    }
    /**
     * Get estimate by id
     * @param  mixed $id
     * @return array
     */
    public function get($id = '',$where = array())
    {
        $this->db->select('*,tblcurrencies.id as currencyid, tblestimates.id as id, tblcurrencies.name as currency_name');
        $this->db->from('tblestimates');
        $this->db->join('tblcurrencies', 'tblcurrencies.id = tblestimates.currency', 'left');
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('tblestimates.id', $id);
            $estimate = $this->db->get()->row();
            if ($estimate) {
                $this->load->model('clients_model');
                $estimate->items  = $this->get_estimate_items($id);
                $estimate->client = $this->clients_model->get($estimate->clientid);
                if ($estimate->client->company == '') {
                    $estimate->client->company = $estimate->client->firstname . ' ' . $estimate->client->lastname;
                }
            }
            return $estimate;
        }
        return $this->db->get()->result_array();
    }
    /**
     * Convert estimate to invoice
     * @param  mixed $id estimate id
     * @return mixed     New invoice ID
     */
    public function convert_to_invoice($id, $client = false)
    {
        // Recurring invoice date is okey lets convert it to new invoice
        $_estimate                    = $this->get($id);
        $new_invoice_data             = array();
        $new_invoice_data['clientid'] = $_estimate->clientid;
        $new_invoice_data['_number']  = get_option('next_invoice_number');
        $new_invoice_data['date']     = _d(date('Y-m-d'));
        $new_invoice_data['duedate']  = _d(date('Y-m-d'));
        if (get_option('invoice_due_after') != 0) {
            $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
        }
        $new_invoice_data['show_quantity_as'] = $_estimate->show_quantity_as;
        $new_invoice_data['currency']         = $_estimate->currency;
        $new_invoice_data['subtotal']         = $_estimate->subtotal;
        $new_invoice_data['total']            = $_estimate->total;
        $new_invoice_data['adjustment']       = $_estimate->adjustment;
        $new_invoice_data['discount_percent'] = $_estimate->discount_percent;
        $new_invoice_data['discount_total']   = $_estimate->discount_total;
        $new_invoice_data['discount_type']    = $_estimate->discount_type;
        $new_invoice_data['sale_agent']       = $_estimate->sale_agent;
        // Since version 1.0.6
        $new_invoice_data['billing_street']   = $_estimate->billing_street;
        $new_invoice_data['billing_city']     = $_estimate->billing_city;
        $new_invoice_data['billing_state']    = $_estimate->billing_state;
        $new_invoice_data['billing_zip']      = $_estimate->billing_zip;
        $new_invoice_data['billing_country']  = $_estimate->billing_country;
        $new_invoice_data['shipping_street']  = $_estimate->shipping_street;
        $new_invoice_data['shipping_city']    = $_estimate->shipping_city;
        $new_invoice_data['shipping_state']   = $_estimate->shipping_state;
        $new_invoice_data['shipping_zip']     = $_estimate->shipping_zip;
        $new_invoice_data['shipping_country'] = $_estimate->shipping_country;
        if ($_estimate->include_shipping == 1) {
            $new_invoice_data['include_shipping'] = 1;
        }
        $new_invoice_data['show_shipping_on_invoice'] = $_estimate->show_shipping_on_estimate;
        $new_invoice_data['terms']                    = get_option('predefined_terms_invoice');
        $new_invoice_data['clientnote']               = get_option('predefined_clientnote_invoice');
        // Set to unpaid status automatically
        $new_invoice_data['status']                   = 1;
        $new_invoice_data['clientnote']               = '';
        $new_invoice_data['adminnote']                = 'Converted from estimate #' . format_estimate_number($_estimate->id);
        $this->load->model('payment_modes_model');
        $modes      = $this->payment_modes_model->get();
        $temp_modes = array();
        foreach ($modes as $mode) {
            $temp_modes[] = $mode['id'];
        }
        $new_invoice_data['allowed_payment_modes'] = $temp_modes;
        $new_invoice_data['newitems']              = array();
        $key                                       = 1;
        foreach ($_estimate->items as $item) {
            $new_invoice_data['newitems'][$key]['description']      = $item['description'];
            $new_invoice_data['newitems'][$key]['long_description'] = $item['long_description'];
            $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
            $new_invoice_data['newitems'][$key]['taxname']          = array();
            $taxes                                                  = get_estimate_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_invoice_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_invoice_data['newitems'][$key]['rate']  = $item['rate'];
            $new_invoice_data['newitems'][$key]['order'] = $item['item_order'];
            $key++;
        }
        $this->load->model('invoices_model');
        $id = $this->invoices_model->add($new_invoice_data);
        if ($id) {
            // Update estimate with the new invoice data and set to status accepted
            $this->db->where('id', $_estimate->id);
            $this->db->update('tblestimates', array(
                'invoiced_date' => date('Y-m-d H:i:s'),
                'invoiceid' => $id,
                'status' => 4
            ));
            if ($client == false) {
                $this->log_estimate_activity($_estimate->id, _l('estimate_activity_converted', '<a href="' . admin_url('invoices/list_invoices/' . $id) . '">' . format_invoice_number($id) . '</a>'));
            }
        }
        return $id;
    }
    /**
     * Copy estimate
     * @param  mixed $id estimate id to copy
     * @return mixed
     */
    public function copy($id)
    {
        $_estimate                             = $this->get($id);
        $new_estimate_data                     = array();
        $new_estimate_data['clientid']         = $_estimate->clientid;
        $new_estimate_data['_number']          = get_option('next_estimate_number');
        $new_estimate_data['date']             = _d(date('Y-m-d'));
        $new_estimate_data['expirydate']       = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
        $new_estimate_data['show_quantity_as'] = $_estimate->show_quantity_as;
        $new_estimate_data['currency']         = $_estimate->currency;
        $new_estimate_data['subtotal']         = $_estimate->subtotal;
        $new_estimate_data['total']            = $_estimate->total;
        $new_estimate_data['adminnote']        = $_estimate->adminnote;
        $new_estimate_data['adjustment']       = $_estimate->adjustment;
        $new_estimate_data['discount_percent'] = $_estimate->discount_percent;
        $new_estimate_data['discount_total']   = $_estimate->discount_total;
        $new_estimate_data['discount_type']    = $_estimate->discount_type;
        $new_estimate_data['terms']            = $_estimate->terms;
        $new_estimate_data['sale_agent']       = $_estimate->sale_agent;
        $new_estimate_data['reference_no']     = $_estimate->reference_no;
        // Since version 1.0.6
        $new_estimate_data['billing_street']   = $_estimate->billing_street;
        $new_estimate_data['billing_city']     = $_estimate->billing_city;
        $new_estimate_data['billing_state']    = $_estimate->billing_state;
        $new_estimate_data['billing_zip']      = $_estimate->billing_zip;
        $new_estimate_data['billing_country']  = $_estimate->billing_country;
        $new_estimate_data['shipping_street']  = $_estimate->shipping_street;
        $new_estimate_data['shipping_city']    = $_estimate->shipping_city;
        $new_estimate_data['shipping_state']   = $_estimate->shipping_state;
        $new_estimate_data['shipping_zip']     = $_estimate->shipping_zip;
        $new_estimate_data['shipping_country'] = $_estimate->shipping_country;
        if ($_estimate->include_shipping == 1) {
            $new_estimate_data['include_shipping'] = $_estimate->include_shipping;
        }
        $new_estimate_data['show_shipping_on_estimate'] = $_estimate->show_shipping_on_estimate;
        // Set to unpaid status automatically
        $new_estimate_data['status']                    = 1;
        $new_estimate_data['clientnote']                = $_estimate->clientnote;
        $new_estimate_data['adminnote']                 = '';
        $new_estimate_data['newitems']                  = array();
        $key                                            = 1;
        foreach ($_estimate->items as $item) {
            $new_estimate_data['newitems'][$key]['description']      = $item['description'];
            $new_estimate_data['newitems'][$key]['long_description'] = $item['long_description'];
            $new_estimate_data['newitems'][$key]['qty']              = $item['qty'];
            $new_estimate_data['newitems'][$key]['taxname']          = array();
            $taxes                                                   = get_estimate_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_estimate_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_estimate_data['newitems'][$key]['rate']  = $item['rate'];
            $new_estimate_data['newitems'][$key]['order'] = $item['item_order'];
            $key++;
        }
        $id = $this->add($new_estimate_data);
        if ($id) {
            $custom_fields = get_custom_fields('estimate');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($_estimate->id, $field['id'], 'estimate');
                if ($value == '') {
                    continue;
                }
                $this->db->insert('tblcustomfieldsvalues', array(
                    'relid' => $id,
                    'fieldid' => $field['id'],
                    'fieldto' => 'estimate',
                    'value' => $value
                ));
            }
            logActivity('Copied Estimate ' . format_estimate_number($_estimate->id));
            return $id;
        }
        return false;
    }
    public function get_estimates_total($data)
    {
        $statuses = array(
            1,
            2,
            3,
            4,
            5
        );
        $this->load->model('currencies_model');
        if ((is_using_multiple_currencies('tblestimates') && !isset($data['currency'])) || !isset($data['currency'])) {
            $currencyid = $this->currencies_model->get_base_currency()->id;
        } else if (isset($data['currency'])) {
            $currencyid = $data['currency'];
        }
        $symbol = $this->currencies_model->get_currency_symbol($currencyid);
        $sql    = 'SELECT';
        foreach ($statuses as $estimate_status) {
            $sql .= '(SELECT SUM(total) FROM tblestimates WHERE status=' . $estimate_status;
            $sql .= ' AND currency =' . $currencyid;
            $sql .= ') as "' . $estimate_status . '",';
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
     * Get all estimate items
     * @param  mixed $id estimateid
     * @return array
     */
    public function get_estimate_items($id)
    {
        $this->db->select('tblestimateitems.id,qty,rate,description as description,long_description,item_order');
        $this->db->from('tblestimateitems');
        $this->db->where('tblestimateitems.estimateid', $id);
        $this->db->order_by('item_order', 'asc');
        return $this->db->get()->result_array();
    }
    /**
     * Insert new estimate to database
     * @param array $data invoiec data
     * @return mixed - false if not insert, estimate ID if succes
     */
    public function add($data)
    {
        $data['number'] = $data['_number'];
        $unsetters      = array(
            '_number',
            'currency_symbol',
            'price',
            'taxname',
            'description',
            'long_description',
            'taxid',
            'rate',
            'quantity',
            'item_select'
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
        $data['date'] = to_sql_date($data['date']);
        if (!empty($data['expirydate'])) {
            $data['expirydate'] = to_sql_date($data['expirydate']);
        } else {
            unset($data['expirydate']);
        }
        $data['hash'] = md5(rand() . microtime());
        // Check if the key exists
        $this->db->where('hash', $data['hash']);
        $exists = $this->db->get('tblestimates')->row();
        if ($exists) {
            $data['hash'] = md5(rand() . microtime());
        }
        $data['terms']       = nl2br($data['terms']);
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['year']        = get_option('estimate_year');
        $data['addedfrom']   = get_staff_user_id();
        $items               = array();
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
            $data['show_shipping_on_estimate'] = 1;
            $data['include_shipping']          = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_estimate'])) {
                $data['show_shipping_on_estimate'] = 1;
            } else {
                $data['show_shipping_on_estimate'] = 0;
            }
        }
        if ($data['discount_total'] == 0) {
            $data['discount_type'] = '';
        }
        $_data = do_action('before_estimate_added', array(
            'data' => $data,
            'items' => $items
        ));
        if ($data['sale_agent'] == '') {
            $data['sale_agent'] = 0;
        }
        $data  = $_data['data'];
        $items = $_data['items'];
        $this->db->insert('tblestimates', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            // Update next estimate number in settings
            $this->db->where('name', 'next_estimate_number');
            $this->db->set('value', 'value+1', FALSE);
            $this->db->update('tbloptions');
            if (count($items) > 0) {
                foreach ($items as $key => $item) {
                    $this->db->insert('tblestimateitems', array(
                        'description' => $item['description'],
                        'long_description' => nl2br($item['long_description']),
                        'qty' => $item['qty'],
                        'rate' => $item['rate'],
                        'estimateid' => $insert_id,
                        'item_order' => $item['order']
                    ));
                    $itemid = $this->db->insert_id();
                    if ($itemid) {
                        foreach ($item['taxname'] as $taxname) {
                            if ($taxname != '') {
                                $_temp    = explode('|', $taxname);
                                $tax_name = $_temp[0];
                                $tax_rate = $_temp[1];
                                $this->db->insert('tblestimateitemstaxes', array(
                                    'itemid' => $itemid,
                                    'taxrate' => $tax_rate,
                                    'taxname' => $tax_name,
                                    'estimate_id' => $insert_id
                                ));
                            }
                        }
                    }
                }
            }
            $this->log_estimate_activity($insert_id, _l('estimate_activity_created'));
            do_action('after_estimate_added', $insert_id);
            return $insert_id;
        }
        return false;
    }
    public function get_estimate_item($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblestimateitems')->row();
    }
    /**
     * Update estimate data
     * @param  array $data estimate data
     * @param  mixed $id   estimateid
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows   = 0;
        $prefix         = get_option('estimate_prefix');
        $data['number'] = substr($data['number'], strlen($prefix));
        $data['number'] = trim($data['number']);
        if (get_option('estimate_number_format') == 2) {
            $_temp_number   = explode('/', $data['number']);
            $data['number'] = $_temp_number[1];
        }
        $original_estimate        = $this->get($id);
        $original_status          = $original_estimate->status;
        $original_number          = $original_estimate->number;
        $original_number_formated = format_estimate_number($id);
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
        if ($data['sale_agent'] == '') {
            $data['sale_agent'] = 0;
        }
        $data['terms']      = nl2br($data['terms']);
        $data['date']       = to_sql_date($data['date']);
        $data['expirydate'] = to_sql_date($data['expirydate']);
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
        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = NULL;
                }
            }
            $data['show_shipping_on_estimate'] = 1;
            $data['include_shipping']          = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_estimate'])) {
                $data['show_shipping_on_estimate'] = 1;
            } else {
                $data['show_shipping_on_estimate'] = 0;
            }
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
        $_data                 = do_action('before_estimate_updated', $action_data);
        $data['removed_items'] = $_data['removed_items'];
        $newitems              = $_data['newitems'];
        $items                 = $_data['items'];
        $data                  = $_data['data'];
        // Delete items checked to be removed from database
        if (isset($data['removed_items'])) {
            foreach ($data['removed_items'] as $remove_item_id) {
                foreach ($data['removed_items'] as $remove_item_id) {
                    $original_item = $this->get_estimate_item($remove_item_id);
                    $this->db->where('estimateid', $id);
                    $this->db->where('id', $remove_item_id);
                    $this->db->delete('tblestimateitems');
                    if ($this->db->affected_rows() > 0) {
                        $this->log_estimate_activity($id, _l('invoice_estimate_activity_removed_item', $original_item->description));
                        $affectedRows++;
                        $this->db->where('itemid', $remove_item_id);
                        $this->db->delete('tblestimateitemstaxes');
                    }
                }
            }
            unset($data['removed_items']);
        }
        $this->db->where('id', $id);
        $this->db->update('tblestimates', $data);
        if ($this->db->affected_rows() > 0) {
            // Check for status change
            if ($original_status != $data['status']) {
                $activity = _l('not_estimate_status_updated', array(
                    format_estimate_status($original_status),
                    format_estimate_status($data['status'])
                ));
                $this->log_estimate_activity($original_estimate->id, $activity);
            }
            if ($original_number != $data['number']) {
                $activity = _l('estimate_activity_number_changed', array(
                    $original_number_formated,
                    format_estimate_number($original_estimate->id)
                ));
                $this->log_estimate_activity($original_estimate->id, $activity);
            }
            $affectedRows++;
        }
        $this->load->model('taxes_model');
        if (count($items) > 0) {
            foreach ($items as $key => $item) {
                $estimate_item_id = $item['itemid'];
                $original_item    = $this->get_estimate_item($estimate_item_id);
                $this->db->where('id', $estimate_item_id);
                $this->db->update('tblestimateitems', array(
                    'item_order' => $item['order']
                ));
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
                // Check for invoice item short description change
                $this->db->where('id', $estimate_item_id);
                $this->db->update('tblestimateitems', array(
                    'description' => $item['description']
                ));
                if ($this->db->affected_rows() > 0) {
                    $activity = _l('invoice_estimate_activity_updated_item_short_description', array(
                        $original_item->description,
                        $item['description']
                    ));
                    $this->log_estimate_activity($id, $activity);
                    $affectedRows++;
                }
                // Check for item long description change
                $this->db->where('id', $estimate_item_id);
                $this->db->update('tblestimateitems', array(
                    'long_description' => nl2br($item['long_description'])
                ));
                if ($this->db->affected_rows() > 0) {
                    $activity = _l('invoice_estimate_activity_updated_item_long_description', array(
                        $original_item->long_description,
                        $item['long_description']
                    ));
                    $this->log_estimate_activity($id, $activity);
                    $affectedRows++;
                }
                if (count($item['taxname']) == 0) {
                    $this->db->where('itemid', $invoice_item_id);
                    $this->db->delete('tblestimateitemstaxes');
                } else {
                    $item_taxes        = get_estimate_item_taxes($estimate_item_id);
                    $_item_taxes_names = array();
                    foreach ($item_taxes as $_item_tax) {
                        array_push($_item_taxes_names, $_item_tax['taxname']);
                    }
                    $i = 0;
                    foreach ($_item_taxes_names as $_item_tax) {
                        if (!in_array($_item_tax, $item['taxname'])) {
                            $this->db->where('id', $item_taxes[$i]['id']);
                            $this->db->delete('tblestimateitemstaxes');
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
                            if (total_rows('tblestimateitemstaxes', array(
                                'taxname' => $tax_name,
                                'itemid' => $estimate_item_id,
                                'taxrate' => $tax_rate
                            )) == 0) {
                                $this->db->insert('tblestimateitemstaxes', array(
                                    'taxrate' => $tax_rate,
                                    'taxname' => $tax_name,
                                    'itemid' => $estimate_item_id,
                                    'estimate_id' => $id
                                ));
                                if ($this->db->affected_rows() > 0) {
                                    $affectedRows++;
                                }
                            }
                        }
                    }
                }
                // Check for item rate change
                $this->db->where('id', $estimate_item_id);
                $this->db->update('tblestimateitems', array(
                    'rate' => $item['rate']
                ));
                if ($this->db->affected_rows() > 0) {
                    $activity = _l('invoice_estimate_activity_updated_item_rate', array(
                        $original_item->rate,
                        $item['rate']
                    ));
                    $this->log_estimate_activity($id, $activity);
                    $affectedRows++;
                }
                // CHeck for invoice quantity change
                $this->db->where('id', $estimate_item_id);
                $this->db->update('tblestimateitems', array(
                    'qty' => $item['qty']
                ));
                if ($this->db->affected_rows() > 0) {
                    $activity = _l('invoice_estimate_activity_updated_qty_item', array(
                        $item['description'],
                        $original_item->qty,
                        $item['qty']
                    ));
                    $this->log_estimate_activity($id, $activity);
                    $affectedRows++;
                }
            }
        }
        if (count($newitems) > 0) {
            foreach ($newitems as $key => $item) {
                $this->db->insert('tblestimateitems', array(
                    'description' => $item['description'],
                    'long_description' => nl2br($item['long_description']),
                    'qty' => $item['qty'],
                    'rate' => $item['rate'],
                    'estimateid' => $id,
                    'item_order' => $item['order']
                ));
                $new_item_added = $this->db->insert_id();
                if ($new_item_added) {
                    foreach ($item['taxname'] as $taxname) {
                        if($taxname != ''){
                            $_temp    = explode('|', $taxname);
                            $tax_name = $_temp[0];
                            $tax_rate = $_temp[1];
                            $this->db->insert('tblestimateitemstaxes', array(
                                'taxrate' => $tax_rate,
                                'taxname' => $tax_name,
                                'itemid' => $new_item_added,
                                'estimate_id' => $id
                            ));
                        }
                    }
                    $activity = _l('invoice_estimate_activity_added_item', $item['description']);
                    $this->log_estimate_activity($id, $activity);
                    $affectedRows++;
                }
            }
        }
        if ($affectedRows > 0) {
            do_action('after_estimate_updated', $id);
            return true;
        }
        return false;
    }
    public function mark_action_status($action, $id, $client = false)
    {
        $this->db->where('id', $id);
        $this->db->update('tblestimates', array(
            'status' => $action
        ));
        if ($this->db->affected_rows() > 0) {
            $estimate = $this->get($id);
            if ($client == true) {
                $this->db->where('staffid', $estimate->addedfrom);
                $this->db->or_where('staffid', $estimate->sale_agent);
                $staff_estimate = $this->db->get('tblstaff')->result_array();
                $invoiceid      = false;
                $invoiced       = false;
                if ($action == 4) {
                    if (get_option('estimate_auto_convert_to_invoice_on_client_accept') == 1) {
                        $invoiceid = $this->convert_to_invoice($id, true);
                        $this->load->model('invoices_model');
                        if ($invoiceid) {
                            $invoiced = true;
                            $invoice  = $this->invoices_model->get($invoiceid);
                            $activity = _l('estimate_activity_client_accepted_and_converted', '<a href="' . admin_url('invoices/list_invoices/' . $invoiceid) . '">' . format_invoice_number($invoice->id) . '</a>');
                            $this->log_estimate_activity($id, $activity, true);
                        }
                    } else {
                        $this->log_estimate_activity($id, _l('estimate_activity_client_accepted'), true);
                    }
                    // Send thank you email
                    $this->emails_model->send_email_template('estimate-thank-you-to-customer', $estimate->client->email, $estimate->clientid, false, false, false, $id);
                    // add notifications for all users which have permissions manageSales
                    foreach ($staff_estimate as $member) {
                        if (has_permission('manageSales', $member['staffid'])) {
                            add_notification(array(
                                'fromcompany' => true,
                                'touserid' => $member['staffid'],
                                'description' => _l('not_estimate_customer_accepted', format_estimate_number($estimate->id)),
                                'link' => 'estimates/list_estimates/' . $id
                            ));
                            // Send staff email notification that customer accepted estimate
                            $this->emails_model->send_email_template('estimate-accepted-to-staff', $member['email'], $estimate->clientid, false, false, false, $id);
                        }
                    }
                    return array(
                        'invoiced' => $invoiced,
                        'invoiceid' => $invoiceid
                    );
                } else if ($action == 3) {
                    // add notifications for all users which have permissions manageSales
                    foreach ($staff_estimate as $member) {
                        if (has_permission('manageSales', $member['staffid'])) {
                            add_notification(array(
                                'fromcompany' => true,
                                'touserid' => $member['staffid'],
                                'description' => _l('not_estimate_customer_declined', format_estimate_number($estimate->id)),
                                'link' => 'estimates/list_estimates/' . $id
                            ));
                            // Send staff email notification that customer declined estimate
                            $this->emails_model->send_email_template('estimate-declined-to-staff', $member['email'], $estimate->clientid, false, false, false, $id);
                        }
                    }
                    $this->log_estimate_activity($id, _l('estimate_activity_client_declined'), true);
                    return array(
                        'invoiced' => $invoiced,
                        'invoiceid' => $invoiceid
                    );
                }
            } else {
                // Admin marked estimate
                $activity = _l('estimate_activity_marked', format_estimate_status($action, '', false));
                $this->log_estimate_activity($id, $activity);
                return true;
            }
        }
        return false;
    }
    /**
     * Delete estimate items and all connections
     * @param  mixed $id estimateid
     * @return boolean
     */
    public function delete($id)
    {
        if (get_option('delete_only_on_last_estimate') == 1) {
            if (!is_last_estimate($id)) {
                return false;
            }
        }
        $estimate = $this->get($id);
        if (!is_null($estimate->invoiceid)) {
            return array(
                'is_invoiced_estimate_delete_error' => true
            );
        }
        do_action('before_estimate_deleted', $id);
        $this->db->where('id', $id);
        $this->db->delete('tblestimates');
        if ($this->db->affected_rows() > 0) {
            if (get_option('estimate_number_decrement_on_delete') == 1) {
                $current_next_estimate_number = get_option('next_estimate_number');
                if ($current_next_estimate_number > 1) {
                    // Decrement next estimate number to
                    $this->db->where('name', 'next_estimate_number');
                    $this->db->set('value', 'value-1', FALSE);
                    $this->db->update('tbloptions');
                }
            }
            if (total_rows('tblproposals', array(
                'estimate_id' => $id
            )) > 0) {
                $this->db->where('estimate_id', $id);
                $estimate = $this->db->get('tblproposals')->row();
                $this->db->where('id', $estimate->id);
                $this->db->update('tblproposals', array(
                    'estimate_id' => NULL,
                    'date_converted' => NULL
                ));
            }
            $this->db->where('estimateid', $id);
            $this->db->delete('tblestimateitems');
            $this->db->where('estimateid', $id);
            $this->db->delete('tblestimateactivity');
            $this->db->where('estimate_id', $id);
            $this->db->delete('tblestimateitemstaxes');
            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'invoice');
            $this->db->delete('tblcustomfieldsvalues');

            // Get related tasks
            $this->load->model('tasks_model');
            $this->db->where('rel_type', 'estimate');
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
     * Set estimate to sent when email is successfuly sended to client
     * @param mixed $id estimateid
     */
    public function set_estimate_sent($id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblestimates', array(
            'sent' => 1,
            'datesend' => date('Y-m-d H:i:s')
        ));
        $description = _l('invoice_estimate_activity_sent_to_client') . ' - ' . $this->input->post('sent_to');
        $this->log_estimate_activity($id, $description);
        // Update estimate status to sent
        $this->db->where('id', $id);
        $this->db->update('tblestimates', array(
            'status' => 2
        ));
    }
    /**
     * Sent estimate to client
     * @param  mixed  $id        estimateid
     * @param  string  $template  email template to sent
     * @param  boolean $attachpdf attach estimate pdf or not
     * @return boolean
     */
    public function sent_estimate_to_client($id, $template = '', $attachpdf = true)
    {
        $this->load->model('emails_model');
        $estimate = $this->get($id);
        if ($template == '') {
            if ($estimate->sent == 0) {
                $template = 'estimate-send-to-client';
            } else {
                $template = 'estimate-already-send';
            }
        }
        $estimate_number = format_estimate_number($estimate->id);
        $pdf             = estimate_pdf($estimate);
        if ($attachpdf) {
            $attach = $pdf->Output($estimate_number . '.pdf', 'S');
            $this->emails_model->add_attachment(array(
                'attachment' => $attach,
                'filename' => $estimate_number . '.pdf',
                'type' => 'application/pdf'
            ));
        }

        $email = $estimate->client->email;
        if($this->input->post('sent_to')){
            $email = $this->input->post('sent_to');
        }

        $send = $this->emails_model->send_email_template($template, $email, $estimate->clientid, false, false, false, $id);
        if ($send) {
            $this->set_estimate_sent($id);
            return true;
        }
        return false;
    }
    /**
     * All estimate activity
     * @param  mixed $id estimateid
     * @return array
     */
    public function get_estimate_activity($id)
    {
        $this->db->where('estimateid', $id);
        $this->db->order_by('date', 'asc');
        return $this->db->get('tblestimateactivity')->result_array();
    }
    /**
     * Log estimate activity to database
     * @param  mixed $id   estimateid
     * @param  string $description activity description
     */
    public function log_estimate_activity($id, $description = '', $client = false)
    {
        $staffid = get_staff_user_id();
        if (DEFINED('CRON')) {
            $staffid = 'Cron Job';
        } else if ($client == true) {
            $staffid = NULL;
        }
        $this->db->insert('tblestimateactivity', array(
            'description' => $description,
            'date' => date('Y-m-d H:i:s'),
            'estimateid' => $id,
            'staffid' => $staffid
        ));
    }
}
