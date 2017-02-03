<div id="newsfeed" class="animated fadeIn hide" <?php if($this->session->flashdata('newsfeed_auto')){echo 'data-newsfeed-auto';} ?>>

</div>

<div class="modal fade task-modal-single" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body"><div class="row data"></div></div>
    </div>
  </div>
</div>
<div class="modal fade lead-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>
<div class="modal fade timers-modal-logout" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 text-center">
           <h4 class="bold"><?php echo _l('timers_started_confirm_logout'); ?></h4>
         </div>
       </div>
     </div>
     <div class="modal-footer">
      <a href="<?php echo site_url('authentication/logout'); ?>" class="btn btn-danger"><?php echo _l('confirm_logout'); ?></a>
    </div>
  </div>
</div>
</div>
<div id="tacking-stats">
</div>
<!-- add/edit task modal will be appended here -->
<div id="_task"></div>
<?php do_action('before_js_scripts_render'); ?>
<script src="<?php echo site_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/jquery-ui/jquery-ui.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/jquery-validation/jquery.validate.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/metisMenu/metisMenu.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/datatables/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/datatables/js/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/datatables-buttons/js/dataTables.buttons.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/datatables-buttons/js/buttons.colVis.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/datatables-buttons/js/buttons.html5.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/datatables-buttons/js/buttons.print.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/jszip/jszip.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/pdfmake/pdfmake.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/pdfmake/vfs_fonts.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/bootstrap-select/js/bootstrap-select.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/remarkable-bootstrap-notify/bootstrap-notify.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/moment/moment.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/moment-timezone/moment-timezone-with-data.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/moment-duration-format/moment-duration-format.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/Chart.js/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo site_url('assets/plugins/dropzone/min/dropzone.min.js'); ?>"></script>
<!-- are you sure safari fix -->
<script src="<?php echo site_url('assets/plugins/jquery.are-you-sure/ays-beforeunload-shim.js'); ?>"></script>
<!-- are you sure safari fix -->
<script src="<?php echo site_url('assets/plugins/jquery.are-you-sure/jquery.are-you-sure.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/jquery-comments/js/jquery-comments.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/quill/quill.js'); ?>" type="text/javascript"></script>
<script src="<?php echo site_url('assets/js/editor.js'); ?>" type="text/javascript"></script>
<script src="<?php echo site_url('assets/plugins/jquery-circle-progress/circle-progress.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/accounting.js/accounting.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/fullcalendar/fullcalendar.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/fullcalendar/lang-all.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/fullcalendar/gcal.js'); ?>"></script>
<script src="<?php echo site_url('assets/plugins/gantt/js/jquery.fn.gantt.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/newsfeed.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/leads.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/calendar.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/sales.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/tasks.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/main.js'); ?>"></script>
<?php do_action('after_js_scripts_render'); ?>
