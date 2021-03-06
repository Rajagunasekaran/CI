<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name',
    'due_date'
    );
$sIndexColumn = "id";
$sTable       = 'tblmilestones';
$where        = array(
    'AND project_id=' . $project_id
    );
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array(
    'id',
    'milestone_order'
    ));
$output       = $result['output'];
$rResult      = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            if(has_permission('manageProjects')){
            $_data = '<a href="#" onclick="edit_milestone(this,' . $aRow['id'] . '); return false" data-name="' . $aRow['name'] . '" data-due_date="' . _d($aRow['due_date']) . '" data-order="'.$aRow['milestone_order'].'">' . $_data . '</a>';
             }
        } else if ($aColumns[$i] == 'due_date') {
            $_data = _d($_data);
            if (date('Y-m-d') > $aRow['due_date'] && total_rows('tblstafftasks', array(
                'milestone' => $aRow['id'],
                'finished' => 0,
                'rel_id' => $project_id,
                'rel_type' => 'project'
                )) > 0) {
                $_data .= ' <span class="label label-danger pull-right">' . _l('project_milestone_duedate_passed') . '</span>';
        }
    }
    $row[] = $_data;
}
$options = '';
if(has_permission('manageProjects')){
$options            .= icon_btn('#', 'pencil-square-o', 'btn-default', array(
    'onclick' => 'edit_milestone(this,' . $aRow['id'] . '); return false',
    'data-name' => $aRow['name'],
    'data-due_date' => _d($aRow['due_date']),
    'data-order' => _d($aRow['milestone_order']),
    ));
$options .= icon_btn('projects/delete_milestone/' . $project_id . '/' . $aRow['id'], 'remove', 'btn-danger _delete');

}
$row[] = $options;
$output['aaData'][] = $row;
}
