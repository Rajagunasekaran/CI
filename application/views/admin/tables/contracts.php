<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns = array('subject', 'client', 'contract_type', 'datestart','dateend');
$sIndexColumn = "id";
$sTable = 'tblcontracts';
$additionalSelect = array('company','tblcontracts.id','tblcontracttypes.name','trash');
$join = array(
    'LEFT JOIN tblclients ON tblclients.userid = tblcontracts.client',
    'LEFT JOIN tblcontracttypes ON tblcontracttypes.id = tblcontracts.contract_type'
    );

$custom_fields = get_custom_fields('contracts',array('show_on_table'=>1));

$i = 0;
foreach($custom_fields as $field){
    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblcontracts.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
    $i++;
}

$where = array();
if(is_numeric($clientid)){
    $where = array('WHERE client='.$clientid);
}

if(!$this->_instance->input->post('custom_view')){
    array_push($where,'AND trash = 0');
} else {
    $custom_view = $this->_instance->input->post('custom_view');
    if($custom_view == 'trash'){
        array_push($where,'AND trash = 1');
    } else if($custom_view == 'expired') {
        array_push($where,'AND dateend IS NOT NULL AND dateend >"'.date('Y-m-d').'"');
    } else if($custom_view == 'without_dateend'){
        array_push($where,'AND dateend IS NULL');
    } else {
                    // is contract type (id)
        if(is_numeric($custom_view)){
            array_push($where,'AND contract_type ='. $custom_view);
        }

                    // else is ALL contracts we dont need conditions
    }
}

             // Fix for big queries. Some hosting have max_join_limit
if(count($custom_fields) > 4){
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns,$sIndexColumn,$sTable,$join,$where,$additionalSelect);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ( $rResult as $aRow )
{
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
            $_data = $aRow[ strafter($aColumns[$i],'as ')];
        } else {
            $_data = $aRow[ $aColumns[$i] ];
        }
        if($aColumns[$i] == 'client'){
            $_data = '<a href="'.admin_url('clients/client/'.$aRow['client']).'">'. $aRow['company'] . '</a>';
        } else if($aColumns[$i] == 'dateend' || $aColumns[$i] == 'datestart'){
            $_data = _d($_data);
        } else if($aColumns[$i] == 'subject'){
            $_data = '<a href="'.admin_url('contracts/contract/'.$aRow['id']).'">'.$_data.'</a>';
            if($aRow['trash'] == 1){
                $_data .= '<span class="label label-danger pull-right">'._l('contract_trash').'</span>';
            }
        } else if($aColumns[$i] == 'contract_type'){
            $_data = $aRow['name'];
        }

        $row[] = $_data;
    }

    $options = icon_btn('contracts/contract/'.$aRow['id'],'pencil-square-o');
    $options .= icon_btn('contracts/delete/'.$aRow['id'],'remove','btn-danger _delete');

    $row[] = $options;

    if(!empty($aRow['dateend'])){
        $_date_end = date('Y-m-d',strtotime($aRow['dateend']));
        if($_date_end < date('Y-m-d')){
            $row['DT_RowClass'] = 'alert-danger';
        }
    }

    $output['aaData'][] = $row;
}
