<?php if(isset($client)){ ?>
<?php if(has_permission('manageSales')){ ?>
<?php
$table_data = array(_l('estimate_dt_table_heading_number'),
   _l('estimate_dt_table_heading_amount'),
   _l('estimates_total_tax'),
   _l('estimate_dt_table_heading_client'),
   _l('estimate_dt_table_heading_date'),
   _l('estimate_dt_table_heading_expirydate'),
   _l('reference_no'),
   _l('estimate_dt_table_heading_status'));

$custom_fields = get_custom_fields('estimate',array('show_on_table'=>1));
foreach($custom_fields as $field){
    array_push($table_data,$field['name']);
}
render_datatable($table_data, 'estimates-single-client');
include_once(APPPATH . 'views/admin/clients/modals/zip_estimates.php');
?>
<?php } ?>
<?php } ?>
