<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'subject',
    'proposal_to',
    'total',
    'date',
    'open_till',
    'datecreated',
    'status',
    );
$sIndexColumn = "id";
$sTable       = 'tblproposals';

$where = array();
if($this->_instance->input->post('custom_view')){
    $custom_view = $this->_instance->input->post('custom_view');
    if(is_numeric($custom_view)){
        array_push($where,'AND status='.$custom_view);
    } else if($custom_view == 'leads_related'){
        array_push($where,'AND rel_type="lead"');
    }else if($custom_view == 'customers_related'){
        array_push($where,'AND rel_type="customer"');
    }
}

$join = array();
$custom_fields = get_custom_fields('proposal',array('show_on_table'=>1));

$i = 0;
foreach($custom_fields as $field){
    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblproposals.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
    $i++;
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblproposals.id',
    'currency',
    'rel_id',
    'rel_type',
    ));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {

        if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
            $_data = $aRow[ strafter($aColumns[$i],'as ')];
        } else {
            $_data = $aRow[ $aColumns[$i] ];
        }
        if ($aColumns[$i] == 'subject') {
            $_data = '<a href="#" onclick="init_proposal(' . $aRow['id'] . '); return false;">' . $_data . '</a>';
        } else if ($aColumns[$i] == 'status') {
            $_data = format_proposal_status($aRow['status']);
        } else if($aColumns[$i] == 'total'){
            if($aRow['currency'] != 0){
                $_data = format_money($_data,$this->_instance->currencies_model->get_currency_symbol($aRow['currency']));
            } else {
                $_data = format_money($_data,$this->_instance->currencies_model->get_base_currency($aRow['currency'])->symbol);
            }

        } else if($aColumns[$i] == 'open_till' || $aColumns[$i] == 'datecreated'){
            $_data = _d($_data);
        } else if($aColumns[$i] == 'proposal_to'){
           if(!empty($_data)){
              if(!empty($aRow['rel_id']) && $aRow['rel_id'] != 0){
                if($aRow['rel_type'] == 'lead'){
                  $_data = '<a href="#" onclick="init_lead('.$aRow['rel_id'].');return false;" target="_blank" data-toggle="tooltip" data-title="'._l('lead').'">'.$_data.'</a>';
              } else if($aRow['rel_type'] == 'customer'){
                  $_data = '<a href="'.admin_url('clients/client/'.$aRow['rel_id']).'" target="_blank" data-toggle="tooltip" data-title="'._l('client').'">'.$_data.'</a>';
              }
          }
      }
  }
  $row[] = $_data;
}

$output['aaData'][] = $row;
}
