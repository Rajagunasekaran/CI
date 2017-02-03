<?php if(isset($client)){ ?>
<?php if(has_permission('manageContracts')){ ?>
 <?php render_datatable(array( _l( 'contract_list_client'), _l( 'contract_list_subject'), _l('contract_types_list_name'),_l( 'contract_list_start_date'), _l( 'contract_list_end_date'), _l( 'options'), ), 'contracts-single-client'); ?>

<?php } ?>
<?php } ?>
