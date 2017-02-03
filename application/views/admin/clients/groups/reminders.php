  <?php if(isset($client)){ ?>
  <a href="#" data-toggle="modal" data-target=".reminder-modal"><i class="fa fa-bell-o"> <?php echo _l('set_reminder'); ?></i></a>
  <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified'), _l( 'options'), ), 'reminders');
include_once(APPPATH . 'views/admin/clients/modals/add_reminder.php');
  ?>
  <?php } ?>
