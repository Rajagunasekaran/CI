<script src="<?php echo site_url('assets/plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/jquery-validation/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/datatables/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/datatables/js/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/Chart.js/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo template_assets_url(); ?>js/global.js"></script>
<?php if(is_client_logged_in()){ ?>
<script src="<?php echo site_url('assets/plugins/dropzone/min/dropzone.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/moment/moment.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/jquery-comments/js/jquery-comments.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/jquery-circle-progress/circle-progress.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/gantt/js/jquery.fn.gantt.min.js'); ?>"></script>
<script src="<?php echo template_assets_url(); ?>js/clients.js"></script>
<script src="<?php echo template_assets_url(); ?>js/client-report.js"></script>
<?php } ?>

