<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<?php include_once(APPPATH . 'views/admin/invoices/invoices_top_stats.php'); ?>
			<?php $this->load->view('admin/invoices/list_template'); ?>
		</div>
	</div>
</div>
<?php $this->load->view('admin/invoices/invoice_attach_file'); ?>
<script>var hidden_columns = [2,5];</script>
<?php init_tail(); ?>
<script>
	$(document).ready(function(){
		init_invoice();
	});
</script>
</body>
</html>


