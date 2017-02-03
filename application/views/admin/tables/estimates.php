<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns = array(
    'number',
    'total',
    'total_tax',
    'company',
    'date',
    'expirydate',
    'reference_no',
    'status',
    );

$join = array(
    'LEFT JOIN tblclients ON tblclients.userid = tblestimates.clientid',
    'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblestimates.currency'
    );

$custom_fields = get_custom_fields('estimate',array('show_on_table'=>1));

$i = 0;
foreach($custom_fields as $field){
    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblestimates.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
    $i++;
}
$where                    = array();
if (is_numeric($clientid)) {
    $where                    = array(
        'AND clientid=' . $clientid
        );
}

if ($this->_instance->input->post('status')) {
    array_push($where, 'AND status=' . $this->_instance->input->post('status'));
}

if($this->_instance->input->post('custom_view')){
    $custom_view = $this->_instance->input->post('custom_view');
    if($custom_view == 'not_sent'){
       array_push($where,'AND sent=0');
   }
}

$sIndexColumn = "id";
$sTable       = 'tblestimates';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblestimates.id',
    'company',
    'clientid',
    'symbol',
    'total',
    'status'
    ));
$output       = $result['output'];
$rResult      = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    for ($i = 0; $i < count($aColumns); $i++) {

        if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
            $_data = $aRow[ strafter($aColumns[$i],'as ')];
        } else {
            $_data = $aRow[ $aColumns[$i] ];
        }

        if ($aColumns[$i] == 'number') {
                        // If is from client area table
            if (is_numeric($clientid)) {
                $__data = '<a href="' . admin_url('estimates/list_estimates/' . $aRow['id']) . '" target="_blank">' . format_estimate_number($aRow['id']) . '</a><br />';
            } else {
             $__data = '<a href="#" onclick="init_estimate(' . $aRow['id'] . '); return false;">' . format_estimate_number($aRow['id']) . '</a><br />';
         }
     } else if ($aColumns[$i] == 'date') {
        $__data = _d($_data);
    } else if ($aColumns[$i] == 'company') {
        $__data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a><br />';
    } else if ($aColumns[$i] == 'expirydate') {
        $__data = _d($_data);
    } else if ($aColumns[$i] == 'total' || $aColumns[$i] == 'total_tax') {
        $__data = format_money($_data, $aRow['symbol']);
    } else if($aColumns[$i] == 'status') {
        $__data = format_estimate_status($aRow['status']);
                        // Status
    } else if($aColumns[$i] == 'reference_no') {
                        // is estimate reference
       $__data = $aRow[$aColumns[$i]];
   } else {
    $__data = $_data;
}
$row[] = $__data;
}

$output['aaData'][] = $row;
}

echo json_encode($output);
die();
