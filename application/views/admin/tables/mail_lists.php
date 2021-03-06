<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns         = array(
    'listid',
    'name',
    'tblemaillists.datecreated'
    );
$additionalSelect = array(
    'firstname',
    'lastname',
    'staffid'
    );
$join             = array(
    'LEFT JOIN tblstaff ON tblstaff.staffid = tblemaillists.creator'
    );
$sIndexColumn     = "listid";
$sTable           = 'tblemaillists';
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, array(), $additionalSelect);
$output           = $result['output'];
$rResult          = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="' . admin_url('surveys/mail_list_view/' . $aRow['listid']) . '">' . $_data . '</a>';
            $_data .= '<p>Total emails: ' . total_rows('tbllistemails', 'listid=' . $aRow['listid']) . '</p>';
        } else if ($aColumns[$i] == 'tblemaillists.datecreated') {
            $_data = _dt($_data);
        }
        $row[] = $_data;
    }
    $options = icon_btn('surveys/mail_list_view/' . $aRow['listid'], 'eye');
    $options .= icon_btn('surveys/mail_list/' . $aRow['listid'], 'pencil-square-o');
    $row[] = $options .= icon_btn('surveys/delete_mail_list/' . $aRow['listid'], 'remove', 'btn-danger _delete');
    array_splice($row, 3, 0, '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>');
    $output['aaData'][] = $row;
}
$staff_mail_list_row   = array(
    '--',
    '<a href="' . site_url("admin/surveys/mail_list_view/staff") . '" data-toggle="tooltip" title="' . _l('cant_edit_mail_list') . '">Staff</a>',
    '--',
    '--',
    '<a href="' . site_url("admin/surveys/mail_list_view/staff") . '" class="btn btn-default btn-icon" ><i class="fa fa-eye"></i>'
    );
$clients_mail_list_row = array(
    '--',
    '<a href="' . site_url("admin/surveys/mail_list_view/clients") . '" data-toggle="tooltip" title="' . _l('cant_edit_mail_list') . '">Clients</a>',
    '--',
    '--',
    '<a href="' . site_url("admin/surveys/mail_list_view/clients") . '" class="btn btn-default btn-icon" ><i class="fa fa-eye"></i>'
    );
            // Add clients and staff mail lists to top always
array_unshift($output['aaData'], $staff_mail_list_row);
array_unshift($output['aaData'], $clients_mail_list_row);
