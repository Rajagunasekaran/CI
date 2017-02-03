<?php if(isset($client)){ ?>
<?php if(has_permission('manageSales')){ ?>
<?php render_datatable(array( _l( 'payments_table_number_heading'), _l( 'payments_table_invoicenumber_heading'), _l( 'payments_table_mode_heading'), _l( 'payments_table_client_heading'), _l( 'payments_table_amount_heading'), _l( 'payments_table_date_heading'), _l( 'options') ), 'payments-single-client');
include_once(APPPATH . 'views/admin/clients/modals/zip_payments.php');
?>
<?php } ?>
<?php } ?>
