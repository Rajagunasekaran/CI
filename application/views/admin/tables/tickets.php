<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns         = array(
    'tbltickets.ticketid',
    'subject',
    'tbldepartments.name',
    'company',
    'tblticketstatus.name',
    'tblpriorities.name',
    'lastreply',
    'tbltickets.date'
    );
$additionalSelect = array(
    'adminread',
    'tbltickets.userid',
    'tblticketassignments.staffid',
    'statuscolor',
    'tbltickets.name',
    'tbltickets.email',
    'tbltickets.userid'
    );

if (get_option('services') == 1) {
    array_splice($aColumns, 3, 0, 'tblservices.name');
}

$join = array(
    'LEFT JOIN tblservices ON tblservices.serviceid = tbltickets.service',
    'LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbltickets.department',
    'LEFT JOIN tblticketstatus ON tblticketstatus.ticketstatusid = tbltickets.status',
    'LEFT JOIN tblclients ON tblclients.userid = tbltickets.userid',
    'LEFT JOIN tblticketassignments ON tblticketassignments.ticketid = tbltickets.ticketid',
    'LEFT JOIN tblstaff ON tblstaff.staffid = tblticketassignments.staffid',
    'LEFT JOIN tblpriorities ON tblpriorities.priorityid = tbltickets.priority'
    );

$where = array();
if (isset($status) && is_numeric($status)) {
    array_push($where, 'AND status = ' . $status);
}

if (isset($userid)) {
    array_push($where, 'AND tbltickets.userid = ' . $userid);
} else if(isset($by_email)){
    array_push($where, 'AND tbltickets.email = "'.$by_email.'"');
}

if (isset($where_not_ticket_id)) {
    array_push($where, 'AND tbltickets.ticketid != ' . $where_not_ticket_id);
}

// If userid is set, the the view is in client profile, should be shown all tickets
if (!is_admin() && !isset($userid)) {
    if (get_option('staff_access_only_assigned_departments') == 1) {
        $this->_instance->load->model('departments_model');
        $staff_deparments_ids = $this->_instance->departments_model->get_staff_departments(get_staff_user_id(), true);
        $departments_ids = array();
        if (count($staff_deparments_ids) == 0) {
            $departments = $this->_instance->departments_model->get();
            foreach($departments as $department){
                array_push($departments_ids,$department['departmentid']);
            }
        } else {
           $departments_ids = $staff_deparments_ids;
       }
       if(count($departments_ids) > 0){
        array_push($where, 'AND department IN (SELECT departmentid FROM tblstaffdepartments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="'.get_staff_user_id().'")');
    }
}
}

$sIndexColumn = 'ticketid';
$sTable       = 'tbltickets';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'lastreply') {
            if ($aRow[$aColumns[$i]] == NULL) {
                $_data = _l('ticket_no_reply_yet');
            } else {
                $_data = time_ago_specific($aRow[$aColumns[$i]]);
            }
        } else if ($aColumns[$i] == 'subject' || $aColumns[$i] == 'tbltickets.ticketid') {
            $_data = '<a href="' . admin_url('tickets/ticket/' . $aRow['tbltickets.ticketid']) . '">' . $_data . '</a>';
                // Ticket is assigned
            if ($aRow['staffid'] !== NULL) {
                if ($aColumns[$i] != 'tbltickets.ticketid') {
                    $_data .= '<br /><a href="' . admin_url('profile/' . $aRow['staffid']) . '" data-toggle="tooltip" title="' . get_staff_full_name($aRow['staffid']) . '" class="mtop10 inline-block">' . staff_profile_image($aRow['staffid'], array(
                        'staff-profile-image-small'
                        )) . '</a>';
                }
            }

        } else if ($aColumns[$i] == 'company') {
            if ($aRow['userid'] != 0) {
                $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';
            } else {
                $_data = $aRow['name'];
            }
        } else if ($aColumns[$i] == 'tblticketstatus.name') {
            $_data = '<span class="label pull-left" style="border:1px solid ' . $aRow["statuscolor"] . '; color:' . $aRow['statuscolor'] . '">' . $_data . '</span>';
        } else if ($aColumns[$i] == 'tbltickets.date') {
            $_data = _dt($_data);
        }

        $row[] = $_data;

        if ($aRow['adminread'] == 0) {
            $row['DT_RowClass'] = 'text-danger bold';
        }
    }

    $options = icon_btn('tickets/ticket/' . $aRow['tbltickets.ticketid'], 'pencil-square-o');
    $row[]   = $options .= icon_btn('tickets/delete/' . $aRow['tbltickets.ticketid'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
