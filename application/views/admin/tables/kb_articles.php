<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns         = array(
    'subject',
    'articlegroup'
    );
$sIndexColumn     = "articleid";
$sTable           = 'tblknowledgebase';
$additionalSelect = array(
    'name',
    'groupid',
    'articleid',

    );
$join             = array(
    'LEFT JOIN tblknowledgebasegroups ON tblknowledgebasegroups.groupid = tblknowledgebase.articlegroup'
    );

$where = array();

            // Sort groups only
if($this->_instance->input->post('custom_view')){
    array_push($where,'AND articlegroup='.$this->_instance->input->post('custom_view'));
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join,$where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'articlegroup') {
            $_data = '<a href="' . admin_url('knowledge_base/group/' . $aRow['groupid']) . '">' . $aRow['name'] . '</a>';
        } else if($aColumns[$i] == 'subject'){
            $_data = '<a href="'.admin_url('knowledge_base/article/'.$aRow['articleid']).'">'.$_data.'</a>';
        }

        $row[] = $_data;
    }

    $options = icon_btn('knowledge_base/article/' . $aRow['articleid'], 'pencil-square-o');
    $options .= icon_btn('knowledge_base/delete_article/' . $aRow['articleid'], 'remove', 'btn-danger _delete');

    $row[] = $options;

    $output['aaData'][] = $row;
}
