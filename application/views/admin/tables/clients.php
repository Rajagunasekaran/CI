<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$custom_fields = get_custom_fields('customers', array(
    'show_on_table' => 1
));

$aColumns = array(
    'company',
    'email',
    'phonenumber',
    '(SELECT GROUP_CONCAT(name) FROM tblcustomersgroups LEFT JOIN tblcustomergroups_in ON tblcustomergroups_in.groupid = tblcustomersgroups.id WHERE customer_id = tblclients.userid)',
    'tblclients.active'
);

$join = array();
$i    = 0;
foreach ($custom_fields as $field) {
    array_push($aColumns, 'ctable_' . $i . '.value as cvalue_' . $i);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $i . ' ON tblclients.userid = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}

$sIndexColumn = "userid";
$sTable       = 'tblclients';

$where = array();

if ($this->_instance->input->post('custom_view')) {
    $custom_view = $this->_instance->input->post('custom_view');
    // view by goups
    if (is_numeric($custom_view)) {
        array_push($where, 'AND userid IN (SELECT customer_id FROM tblcustomergroups_in WHERE groupid = ' . $custom_view . ')');
    }
} else if ($this->_instance->input->post('invoices_by_status')) {
    $status = $this->_instance->input->post('invoices_by_status');
    array_push($where, 'AND userid IN (SELECT clientid FROM tblinvoices WHERE status = ' . $status . ')');
} else if ($this->_instance->input->post('estimates_by_status')) {
    $status = $this->_instance->input->post('estimates_by_status');
    array_push($where, 'AND userid IN (SELECT clientid FROM tblestimates WHERE status = ' . $status . ')');
} else if ($this->_instance->input->post('contracts_by_type')) {
    $type = $this->_instance->input->post('contracts_by_type');
    array_push($where, 'AND userid IN (SELECT client FROM tblcontracts WHERE contract_type = ' . $type . ')');
}


// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'company',
    'userid'
));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        if ($i == 3) {
            if ($_data != '') {
                $groups = explode(',', $_data);
                $_data  = '';
                foreach ($groups as $group) {
                    $_data .= '<span class="label label-default pull-left mright5">' . $group . '</span>';
                }
            }
        }
        if ($aColumns[$i] == 'tblclients.active') {
            $checked = '';
            if ($aRow['tblclients.active'] == 1) {
                $checked = 'checked';
            }
            $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['userid'] . '" data-switch-url="'.ADMIN_URL.'/clients/change_client_status" ' . $checked . '>';
            // For exporting
            $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
        } else if ($aColumns[$i] == 'company') {
            $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';
        } else if ($aColumns[$i] == 'phonenumber') {
            $_data = '<a href="tel:' . $_data . '">' . $_data . '</a>';
        } else if ($aColumns[$i] == 'email') {
            $_data = '<a href="mailto:' . $_data . '">' . $_data . '</a>';
        }

        $row[] = $_data;
    }

    $options = icon_btn('clients/client/' . $aRow['userid'], 'pencil-square-o');
    $row[]   = $options .= icon_btn('clients/delete/' . $aRow['userid'], 'remove', 'btn-danger _delete', array(
        'data-toggle' => 'tooltip',
        'data-placement' => 'left',
        'title' => _l('client_delete_tooltip')
    ));

    $output['aaData'][] = $row;
}
