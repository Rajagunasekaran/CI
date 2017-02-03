<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns = array(
    'name',
    'startdate',
    'duedate',
    'rel_id',
    'billable',
    'billed',
    'finished',
    );

$where = array();

if ($this->_instance->input->post('custom_view')) {
    $view   = $this->_instance->input->post('custom_view');
    $_where = '';
    if ($view == 'finished') {
        $_where = 'AND finished = 1';
    } else if ($view == 'unfinished') {
        $_where = 'AND finished = 0';
    } else if ($view == 'not_assigned') {
        $_where = 'AND tblstafftasks.id NOT IN (SELECT taskid FROM tblstafftaskassignees)';
    } else if ($view == 'due_date_passed') {
        $_where = 'AND (duedate < "' . date('Y-m-d') . '" AND duedate IS NOT NULL) AND finished = 0';
    } else if($view == 'tasks_staff_private'){
        $_where = 'AND is_public = 0';
    } else if($view == 'tasks_staff_public'){
        $_where = 'AND is_public = 1';
    } else if($view == 'my_tasks'){
     $_where = 'AND (tblstafftasks.id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . '))';
    } else if($view == 'my_following_tasks'){
         $_where = 'AND (tblstafftasks.id IN (SELECT taskid FROM tblstafftasksfollowers WHERE staffid = ' . get_staff_user_id() . '))';
    } else if($view == 'billable'){
        $_where = 'AND billable = 1';
    } else if($view == 'billed'){
        $_where = 'AND billed = 1';
    } else if($view == 'not_billed'){
        $_where = 'AND billable =1 AND billed=0';
    }

 if($_where != ''){
    array_push($where, $_where);
}
}

if (!is_admin()) {
    $sql = 'AND (tblstafftasks.id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . ') OR tblstafftasks.id IN (SELECT taskid FROM tblstafftasksfollowers WHERE staffid = ' . get_staff_user_id() . ') OR addedfrom=' . get_staff_user_id() . ') OR is_public = 1';
    array_push($where, $sql);
}

$join = array();
$custom_fields = get_custom_fields('tasks',array('show_on_table'=>1));

$i = 0;
foreach($custom_fields as $field){
    array_push($aColumns,'ctable_'.$i.'.value as cvalue_'.$i);
    array_push($join,'LEFT JOIN tblcustomfieldsvalues as ctable_'.$i . ' ON tblstafftasks.id = ctable_'.$i . '.relid AND ctable_'.$i . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$i . '.fieldid='.$field['id']);
    $i++;
}

$sIndexColumn = "id";
$sTable       = 'tblstafftasks';

            // Fix for big queries. Some hosting have max_join_limit
if(count($custom_fields) > 4){
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}

$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblstafftasks.id',
    'dateadded',
    'priority',
    'finished',
    'rel_type',
    'invoice_id',
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

        if ($aColumns[$i] == 'name') {
            $task_name = $_data;
            $_data     = '';
            if ($aRow['finished'] == 1) {
                $_data .= '<a href="#" onclick="unmark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l('task_unmark_as_complete') . '"></i></a>';
            } else {
                $_data .= '<a href="#" onclick="mark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l('task_single_mark_as_complete') . '"></i></a>';
            }
            $_data .= '<a href="#" class="main-tasks-table-href-name" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $task_name . '</a>';
        } else if ($aColumns[$i] == 'startdate' || $aColumns[$i] == 'duedate') {
            if($aColumns[$i] == 'startdate'){
                $_data = _d($aRow['startdate']);
            } else {
                $_data = _d($aRow['duedate']);
            }
        } else if($aColumns[$i] == 'finished'){
            if($_data == 1){
                $_data = '<span class="label label-success pull-left">'._l('task_table_is_finished_indicator').'</span>';
            } else {
                $_data = '<span class="label label-warning pull-left">'._l('task_table_is_not_finished_indicator').'</span>';
            }
        } else if ($aColumns[$i] == 'priority') {
            if ($_data == 1) {
                $label_class = 'label-default';
            } else if ($_data == 2) {
                $label_class = 'label-info';
            } else if ($_data == 3) {
                $label_class = 'label-warning';
            } else {
                $label_class = 'label-danger';
            }
            $_data = '<span class="label ' . $label_class . '">' . $aRow['priority'] . '</span>';
        } else if ($aColumns[$i] == 'rel_id') {
            if (!empty($_data)) {
                $rel_data   = get_relation_data($aRow['rel_type'], $aRow['rel_id']);
                $rel_values = get_relation_values($rel_data, $aRow['rel_type']);
                $_data      = '<a data-toggle="tooltip" class="pull-left" title="' . ucfirst($aRow['rel_type']) . '" href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
                // for exporting
                $_data .= '<span class="hide"> - ' . ucfirst($aRow['rel_type']) . '</span>';
            }
        } else if($aColumns[$i] == 'billable'){
            if($_data == 1){
                $billable = _l("task_billable_yes");
            } else {
                $billable = _l("task_billable_no");
            }
            $_data = $billable;
        } else if($aColumns[$i] == 'billed'){
        if($aRow['billable'] == 1){
             if($_data == 1){
          $_data = '<span class="label label-success pull-left">'._l('task_billed_yes').'</span>';
     } else {
          $_data = '<span class="label label-danger pull-left">'._l('task_billed_no').'</span>';
    }
} else {
    $_data = '';
}
    }
    $row[] = $_data;
}

$options = '';
if(has_permission('manageTasks')){
$options .= icon_btn('#', 'pencil-square-o','btn-default',array(
    'onclick'=>'edit_task('.$aRow['id'].'); return false'
    ));
}
  $class = 'btn-success';
    $atts = array(
        'onclick'=>'timer_action('.$aRow['id'].'); return false'
        );
    $tooltip = '';
    $is_assigned = $this->_instance->tasks_model->is_task_assignee(get_staff_user_id(),$aRow['id']);
    $is_task_billed = $this->_instance->tasks_model->is_task_billed($aRow['id']);
    if($is_task_billed || !$is_assigned){
        $class = 'btn-default disabled';
        if($is_task_billed){
           $tooltip = ' data-toggle="tooltip" data-title="'._l('task_billed_cant_start_timer').'"';
        } else {
           $tooltip = ' data-toggle="tooltip" data-title="'._l('task_start_timer_only_assignee').'"';
        }
    }
      if(!$this->_instance->tasks_model->is_timer_started($aRow['id'])) {
            $options .= '<span'.$tooltip.'>'.icon_btn('#', 'clock-o',$class,$atts).'</span>';
        } else {
           $options .= icon_btn('#', 'clock-o','btn-danger',array(
            'onclick'=>'timer_action('.$aRow['id'].','.$this->_instance->tasks_model->get_last_timer($aRow['id'])->id.'); return false'
            ));
       }
if(has_permission('manageTasks')){
    $options .= icon_btn('tasks/delete_task/'.$aRow['id'], 'remove','btn-danger _delete');
}
$row[] = $options;

$row_finished_class = '';
if ((!empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['finished'] == 0) {
    $row_finished_class = 'text-danger bold ';
}
if($aRow['finished'] == 0){
    $row_finished_class .= 'task-unfinished-table';
} else {
    $row_finished_class .= 'task-finished-table';
}
$row['DT_RowClass'] = $row_finished_class;
$output['aaData'][] = $row;
}
