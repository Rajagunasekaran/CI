<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns = array('name', 'clientid', 'start_date', 'deadline','status','billing_type','project_created');
$sIndexColumn = "id";
$sTable = 'tblprojects';
$additionalSelect = array('company','id');
$join = array(
    'JOIN tblclients ON tblclients.userid = tblprojects.clientid',
    );

$where = array();
if(is_numeric($clientid)){
    $where = array('WHERE clientid='.$clientid);
}

if(!has_permission('manageProjects')){
    array_push($where,' AND tblprojects.id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id='.get_staff_user_id().')');
}
if($this->_instance->input->post('custom_view')){
  array_push($where,'AND status='.$this->_instance->input->post('custom_view'));
}
$result = data_tables_init($aColumns,$sIndexColumn,$sTable,$join,$where,$additionalSelect);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ( $rResult as $aRow )
{
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        $_data = $aRow[ $aColumns[$i] ];

        if($aColumns[$i] == 'clientid'){
            $_data = '<a href="'.admin_url('clients/client/'.$aRow['clientid']).'">'. $aRow['company'] . '</a>';
        } else if($aColumns[$i] == 'start_date' || $aColumns[$i] == 'deadline' || $aColumns[$i] == 'project_created'){
            $_data = _d($_data);
        } else if($aColumns[$i] == 'name'){
            $_data = '<a href="'.admin_url('projects/view/'.$aRow['id']).'">'.$_data.'</a>';
        } else if($aColumns[$i] == 'billing_type'){
            if($aRow['billing_type'] == 1){
              $type_name = 'project_billing_type_fixed_cost';
          } else if($aRow['billing_type'] == 2){
              $type_name = 'project_billing_type_project_hours';
          } else {
              $type_name = 'project_billing_type_project_task_hours';
          }
          $_data = _l($type_name);
      } else if($aColumns[$i] == 'status'){
          if($_data == 1){
            $label = 'default';
          } else if($_data == 2){
            $label = 'info';
          } else if($_data == 3){
             $label = 'warning';
          } else {
             $label = 'success';
          }
          $status = '<span class="label label-'.$label.' pull-left">'._l('project_status_'.$_data).'</span>';
          $_data = $status;
      }

        $row[] = $_data;
    }
    $options = '';
    if(has_permission('manageProjects')){
        $options .= icon_btn('projects/project/'.$aRow['id'],'pencil-square-o');
        $options .= icon_btn('projects/delete/'.$aRow['id'],'remove','btn-danger _delete');
    }
    $row[] = $options;
    $output['aaData'][] = $row;
}
