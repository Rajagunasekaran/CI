<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if (is_numeric($id)) {
    $aColumns = array(
        'email',
        'dateadded'
        );
    if (count($data['custom_fields']) > 0) {
        foreach ($data['custom_fields'] as $field) {
            array_push($aColumns, '(SELECT value FROM tblmaillistscustomfieldvalues LEFT JOIN tblmaillistscustomfields ON tblmaillistscustomfields.customfieldid = ' . $field['customfieldid'] . ' WHERE tblmaillistscustomfieldvalues.customfieldid ="' . $field['customfieldid'] . '" AND (tblmaillistscustomfieldvalues.emailid = tbllistemails.emailid))');
        }
    }
    $sIndexColumn = "emailid";
    $sTable       = 'tbllistemails';
    $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), array(
        'WHERE listid =' . $id
        ), array(
        'emailid'
        ));
    $output       = $result['output'];
    $rResult      = $result['rResult'];
    foreach ($rResult as $aRow) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            $_data = $aRow[$aColumns[$i]];
            if ($aColumns[$i] == 'dateadded') {
                $_data = _dt($_data);
            }
            $row[] = $_data;
        }
        $row[]              = icon_btn('surveys/delete_mail_list/' . $aRow['emailid'], 'remove', 'btn-danger', array(
            'onclick' => 'remove_email_from_mail_list(this,' . $aRow['emailid'] . '); return false;'
            ));
        $output['aaData'][] = $row;
    }
} else if ($id == 'clients' || $id == 'staff') {
    $aColumns     = array(
        'email',
        'datecreated'
        );
    $sIndexColumn = "userid";
    $sTable       = 'tblclients';
    if ($id == 'staff') {
        $sIndexColumn = 'staffid';
        $sTable       = 'tblstaff';
    }
    $result  = data_tables_init($aColumns, $sIndexColumn, $sTable);
    $output  = $result['output'];
    $rResult = $result['rResult'];
    foreach ($rResult as $aRow) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            $_data = $aRow[$aColumns[$i]];
            if ($aColumns[$i] == 'datecreated') {
                $_data = _dt($_data);
            }
                        // No delete option
            $row[] = $_data;
        }
        $row[]              = '';
        $output['aaData'][] = $row;
    }
}
