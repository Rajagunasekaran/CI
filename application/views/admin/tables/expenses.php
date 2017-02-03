<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns = array(
    'category',
    'amount',
    'date',
    'clientid',
    'reference_no',
    'paymentmode'
    );

$join = array(
    'LEFT JOIN tblclients ON tblclients.userid = tblexpenses.clientid',
    'LEFT JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category'
    );

$custom_fields = get_custom_fields('expenses',array('show_on_table'=>1));

$i = 0;
foreach($custom_fields as $field){
    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblexpenses.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
    $i++;
}


$where = array();
if (is_numeric($clientid)) {
    $where = array(
        'AND clientid=' . $clientid
        );
}

if ($this->_instance->input->post('custom_view')) {
    $custom_view = $this->_instance->input->post('custom_view');
    if ($custom_view == 'invoiced') {
        array_push($where, 'AND invoiceid IS NOT NULL');
    } else if ($custom_view == 'billable') {
        array_push($where, 'AND billable = 1');
    } else if ($custom_view == 'non-billable') {
        array_push($where, 'AND billable = 0');
    } else if ($custom_view == 'unbilled') {
        array_push($where, 'AND (SELECT 1 from tblinvoices WHERE tblinvoices.id = tblexpenses.invoiceid AND status != 2)');
    } else if ($custom_view == 'recurring') {
        array_push($where, 'AND recurring = 1');
    } else {
                    // is expense category
        if (is_numeric($custom_view)) {
            array_push($where, 'AND category = ' . $custom_view);
        }
    }
}

$sIndexColumn = "id";
$sTable       = 'tblexpenses';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'name',
    'tblexpenses.id',
    'company',
    'billable',
    'invoiceid'
    ));
$output       = $result['output'];
$rResult      = $result['rResult'];
$this->_instance->load->model('currencies_model');
$this->_instance->load->model('payment_modes_model');
$base_currency_symbol = $this->_instance->currencies_model->get_base_currency()->symbol;

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {

        if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
            $_data = $aRow[ strafter($aColumns[$i],'as ')];
        } else {
            $_data = $aRow[ $aColumns[$i] ];
        }

        if ($aColumns[$i] == 'category') {
            if (is_numeric($clientid)) {
                $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';
            } else {
                $_data = '<a href="#" onclick="init_expense(' . $aRow['id'] . ');return false;">' . $aRow['name'] . '</a>';
            }
            if($aRow['billable'] == 1){
                if($aRow['invoiceid'] == NULL){
                    $_data .= '<p class="text-danger">'._l('expense_list_unbilled').'</p>';
                } else {
                    if(total_rows('tblinvoices',array('id'=>$aRow['invoiceid'],'status'=>2)) > 0){
                     $_data .= '<br /><p class="text-success">'._l('expense_list_billed').'</p>';
                 } else {
                     $_data .= '<p class="text-success">'._l('expense_list_invoice').'</p>';
                 }
             }
         }
     } else if ($aColumns[$i] == 'amount') {
        $_data = format_money($_data, $base_currency_symbol);
    } else if ($aColumns[$i] == 'clientid') {
        $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
    } else if ($aColumns[$i] == 'paymentmode') {
        $_data = '';
        if ($aRow['paymentmode'] != 0) {
            $_data = $this->_instance->payment_modes_model->get($aRow['paymentmode'])->name;
        }
    }
    $row[] = $_data;
}

$output['aaData'][] = $row;
}
