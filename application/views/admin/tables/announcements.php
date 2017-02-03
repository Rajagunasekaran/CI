<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name'
    );
$sIndexColumn = "announcementid";
$sTable       = 'tblannouncements';
$where = array();
$is_admin = is_admin();
if(!is_admin()){
    $where = array('AND showtostaff=1');
}
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array(
    'announcementid'
    ));
$output       = $result['output'];
$rResult      = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        $row[] = $_data;
    }
    $options = '';
    $options .= icon_btn('announcements/view/' . $aRow['announcementid'], 'eye','btn btn-info');
    if(total_rows('tbldismissedannouncements',array('announcementid'=>$aRow['announcementid'],'staff'=>1,'userid'=>get_staff_user_id())) == 0){
        $options .= icon_btn('#' . $aRow['announcementid'], 'check', 'btn-success dismiss_announcement',array('data-id'=>$aRow['announcementid'],'data-toggle'=>'tooltip','data-title'=>_l('dismiss_announcement')));
    }
    if(is_admin()){
        $options            .= icon_btn('announcements/announcement/' . $aRow['announcementid'], 'pencil-square-o');
        $options              .= icon_btn('announcements/delete/' . $aRow['announcementid'], 'remove', 'btn-danger _delete');
    }
    $row[] = $options;
    $output['aaData'][] = $row;
}
