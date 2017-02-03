<div class="col-md-12 task-single-col no-padding">
 <div class="panel_s">
  <div class="panel-body">
   <?php if($task->finished == 1){
    echo '<div class="ribbon success"><span>'._l('task_finished').'</span></div>';
  } ?>
  <div class="row padding-5 task-info-wrapper">
    <div class="col-md-12">
     <div class="label label-default task-info pull-left">
      <h5 class="no-margin"><i class="fa pull-left fa-bolt"></i>
        <?php echo _l('task_single_priority'); ?>: <?php echo task_priority($task->priority); ?>
      </h5>
    </div>
    <div class="label <?php if(date('Y-m-d') > $task->startdate && total_rows('tbltaskstimers',array('task_id'=>$task->id)) == 0 && $task->finished != 1){echo 'label-danger';}else{echo 'label-default';} ?> mleft10 task-info pull-left">
      <h5 class="no-margin"><i class="fa pull-left fa-margin"></i>
        <?php echo _l('task_single_start_date'); ?>: <?php echo _d($task->startdate); ?>
      </h5>
    </div>
    <div class="label mleft10 task-info pull-left <?php if(!$task->finished){echo ' label-danger'; }else{echo 'label-info';} ?><?php if(!$task->duedate){ echo ' hide';} ?>">
      <h5 class="no-margin"><i class="fa pull-left fa-calendar"></i>
        <?php echo _l('task_single_due_date'); ?>: <?php echo _d($task->duedate); ?>
      </h5>
    </div>
    <?php if($task->finished == 1){ ?>
    <div class="label mleft10 pull-left task-info label-success">
      <h5 class="no-margin"><i class="fa pull-left fa-check"></i>
        <?php echo _l('task_single_finished'); ?>: <?php echo _d($task->datefinished); ?>
      </h5>
    </div>
    <?php } ?>
    <button type="button" class="close task-m-close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
  </div>
</div>
</div>
<div class="panel_s">
 <div class="panel-body">
  <div class="row">
   <div class="col-md-12">
    <div class="row">
     <div class="col-md-9">
      <div class="title-wrapper pull-left">
       <div class="row">
        <div class="col-md-12">
         <?php if($task->finished == 0){ ?>
         <a href="#" onclick="mark_complete(<?php echo $task->id; ?>); return false;" data-toggle="tooltip" title="<?php echo _l('task_single_mark_as_complete'); ?>">
           <i class="fa fa-check task-icon task-unfinished-icon"></i>
         </a>
         <?php } else if($task->finished == 1){ ?>
         <a href="#" onclick="unmark_complete(<?php echo $task->id; ?>); return false;" data-toggle="tooltip" title="<?php echo _l('task_unmark_as_complete'); ?>">
           <i class="fa fa-check task-icon task-finished-icon"></i>
         </a>
         <?php } ?>
         <h3 class="no-margin inline-block">
           <?php echo $task->name; ?>
         </h3>
       </div>
       <?php if($task->billed == 1){ ?>
       <div class="col-md-12">
         <?php  echo '<p class="text-success no-margin">'._l('task_is_billed',format_invoice_number($task->invoice_id)). '</p>'; ?>
       </div>
       <?php } ?>
     </div>
     <?php if($task->is_public == 0){ ?>
     <div class="clearfix"></div>
     <small class="text-muted no-margin<?php if($task->rel_type == 'project'){echo ' hide';} ?>">
       <span data-toggle="tooltip" data-title="<?php echo _l('task_is_private_help'); ?>">
         <?php echo _l('task_is_private'); ?></span>
         <?php if(has_permission('manageTasks')) { ?> -
         <a href="#" onclick="make_task_public(<?php echo $task->id; ?>); return false;">
           <?php echo _l('task_view_make_public'); ?>
         </a>
         <?php } ?>
       </small>
       <?php } ?>
     </div>
     <div class="clearfix"></div>
     <div class="row">
       <hr />
       <div class="col-md-12">
        <?php if(is_admin() || has_permission('manageProjects')){ ?>
        <p class="no-margin pull-left mright5">
          <a href="#" class="btn btn-default mright5" onclick="task_tracking_stats(<?php echo $task->id; ?>); return false;">
            <i class="fa fa-bar-chart"></i>
          </a>
        </p>
        <?php } ?>
        <?php if($task->billed == 0){
          $is_assigned = $this->tasks_model->is_task_assignee(get_staff_user_id(),$task->id);
          if(!$this->tasks_model->is_timer_started($task->id)) { ?>
          <p class="no-margin pull-left"<?php if(!$is_assigned){ ?> data-toggle="tooltip" data-title="<?php echo _l('task_start_timer_only_assignee'); ?>"<?php } ?>>
            <a href="#" class="mbot10 btn<?php if(!$is_assigned){echo ' disabled btn-default';}else {echo ' btn-success';} ?>" onclick="timer_action(<?php echo $task->id; ?>); return false;">
             <i class="fa fa-clock-o"></i> <?php echo _l('task_start_timer'); ?>
           </a>
         </p>
         <?php } else { ?>
         <p class="no-margin pull-left">
          <a href="#" class="btn mbot10 btn-danger<?php if(!$is_assigned){echo ' disabled';} ?>" onclick="timer_action(<?php echo $task->id; ?>,<?php echo $this->tasks_model->get_last_timer($task->id)->id; ?>); return false;">
           <i class="fa fa-clock-o"></i> <?php echo _l('task_stop_timer'); ?>
         </a>
       </p>
       <?php } ?>
       <?php } ?>
     </div>
     <div class="clearfix"></div>
     <div class="col-md-12">
      <?php if($this->tasks_model->is_task_assignee(get_staff_user_id(),$task->id) || total_rows('tbltaskstimers',array('task_id'=>$task->id,'staff_id'=>get_staff_user_id())) > 0){ ?>
      <p class="task-time-logged">
        <span class="text-muted"><?php echo _l('task_user_logged_time'); ?></span>
        <b>
         <?php echo format_seconds($this->tasks_model->calc_task_total_time($task->id,' AND staff_id='.get_staff_user_id())); ?>
       </b>
     </p>
     <?php } ?>
     <p class="task-time-logged">
      <span class="text-success"><?php echo _l('task_total_logged_time'); ?></span>
      <b>
       <?php echo format_seconds($this->tasks_model->calc_task_total_time($task->id)); ?>
     </b>
   </p>
 </div>
</div>
<div class="pull-right">
 <?php echo form_open_multipart('admin/tasks/upload_file',array('id'=>'task-attachment','class'=>'inline-block')); ?>
 <?php echo form_close(); ?>
</div>
<?php
if(!empty($task->rel_id)){
 echo '<div class="task-single-related-wrapper">';
 echo '<hr />';
 $task_rel_data = get_relation_data($task->rel_type,$task->rel_id);
 $task_rel_value = get_relation_values($task_rel_data,$task->rel_type);
 echo '<h4 class="bold text-info">'._l('task_single_related').': <a href="'.$task_rel_value['link'].'">'.$task_rel_value['name'].'</a></h4>';
 echo '</div>';
}
?>
<hr />
<div class="row mbot30">
 <div class="col-md-3 mtop5">
  <i class="fa fa-users"></i> <span class="bold"><?php echo _l('task_single_assignees'); ?></span>
</div>
<div class="col-md-9" id="assignees">
  <?php
  $_assignees = '';
  foreach ($task->assignees as $assignee) {
    $_remove_assigne = '';
    if ($task->addedfrom == get_staff_user_id() || is_admin()) {
      $_remove_assigne = ' <a href="#" class="remove-task-user" onclick="remove_assignee(' . $assignee['id'] . ',' . $task->id . '); return false;"><i class="fa fa-remove"></i></a>';
    }
    $_assignees .= '
    <span class="task-user" data-toggle="tooltip" data-title="'.get_staff_full_name($assignee['assigneeid']).'">
      <a href="' . admin_url('profile/' . $assignee['assigneeid']) . '">' . staff_profile_image($assignee['assigneeid'], array(
        'staff-profile-image-small'
        )) .'</a> ' . $_remove_assigne . '</span>
    </span>';
  }

  if ($_assignees == '') {
    $_assignees = '<div class="bold mtop5 text-danger task-connectors-no-indicator display-block">'._l('task_no_assignees').'</div>';
  }
  echo $_assignees;
  ?>
</div>
</div>
<div class="row">
 <div class="col-md-3 mtop5">
  <i class="fa fa-transgender"></i> <span class="bold"><?php echo _l('task_single_followers'); ?></span>
</div>
<div class="col-md-9" id="followers">
  <?php
  $_followers        = '';
  foreach ($task->followers as $follower) {
    $_remove_follower = '';
    if ($task->addedfrom == get_staff_user_id() || is_admin()) {
      $_remove_follower = ' <a href="#" class="remove-task-user" onclick="remove_follower(' . $follower['id'] . ',' . $task->id . '); return false;"><i class="fa fa-remove"></i></a>';
    }
    $_followers .= '
    <span class="task-user" data-toggle="tooltip" data-title="'.get_staff_full_name($follower['followerid']).'">
      <a href="' . admin_url('profile/' . $follower['followerid']) . '">' . staff_profile_image($follower['followerid'], array(
        'staff-profile-image-small'
        )) . '</a> ' . $_remove_follower . '</span>
    </span>';
  }
  if ($_followers == '') {
    $_followers = '<div class="bold mtop5 task-connectors-no-indicator display-block">'._l('task_no_followers').'</div>';
  }
  echo $_followers;
  ?>
</div>
</div>
</div>
<div class="col-md-3">
  <?php if(has_permission('manageTasks')){ ?>
  <div class="text-left">
   <select data-width="100%" class="text-muted task-action-select selectpicker<?php if(total_rows('tblstafftaskassignees',array('taskid'=>$task->id)) == 0){echo 'task-assignees-dropdown-indicator';} ?>" name="select-assignees" data-live-search="true" title='<?php echo _l('task_single_assignees_select_title'); ?>'></select>
 </div>
 <?php } ?>
 <?php if(has_permission('manageTasks')){ ?>
 <div class="text-left">
   <select data-width="100%" class="text-muted selectpicker task-action-select mtop10" name="select-followers" data-live-search="true" title='<?php echo _l('task_single_followers_select_title'); ?>'></select>
 </div>
 <?php } ?>
 <a href="#" onclick="add_task_checklist_item(); return false">
  <span class="label mtop10 label-default label-task-action new-checklist-item"><i class="fa fa-plus-circle"></i>
    <?php echo _l('add_checklist_item'); ?>
  </span>
</a>
<a href="#" class="add-task-attachments">
  <span class="label mtop10 label-default label-task-action"><i class="fa fa-paperclip"></i>
    <?php echo _l('add_task_attachments'); ?>
  </span>
</a>
<?php if(has_permission('manageTasks')) { ?>
<a href="#" class="edit_task" onclick="edit_task(<?php echo $id; ?>); return false;">
  <span class="label label-default label-task-action mtop10">
    <i class="fa fa-pencil-square"></i>
    <?php echo _l('task_single_edit'); ?>
  </span>
</a>
<a href="<?php echo admin_url('tasks/delete_task/'.$task->id); ?>">
  <span class="label label-default mtop10 label-task-action task-delete _delete">
    <i class="fa fa-remove"></i>
    <?php echo _l('task_single_delete'); ?>
  </span>
</a>
<?php } ?>
</div>
</div>
</div>
<div class="clearfix"></div>
<hr />
</div>
<?php
$custom_fields = get_custom_fields('tasks');
if(count($custom_fields) > 0){ ?>
<div class="row">
  <?php foreach($custom_fields as $field){ ?>
  <?php $value = get_custom_field_value($task->id,$field['id'],'tasks');
  if($value == ''){continue;} $custom_fields_found = true;?>
  <div class="col-md-9">
    <span class="bold"><?php echo ucfirst($field['name']); ?></span>
    <br />
    <div class="text-left">
     <?php echo $value; ?>
   </div>
 </div>
 <?php } ?>
 <?php
// Add separator if custom fields found for output
 if(isset($custom_fields_found)){?> <div class="clearfix"></div><hr /><?php } ?>
</div>
<?php } ?>
<?php if(count($task->attachments) > 0){ ?>
<div class="row">
 <div class="col-md-12" id="attachments">
  <h4 class="bold"><?php echo _l('task_view_attachments'); ?></h4>
  <ul class="list-unstyled">
   <?php foreach($task->attachments as $attachment){ ?>
   <li class="mbot10">
    <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i>
    <a href="<?php echo site_url('download/file/taskattachment/'.$attachment['id']); ?>" download>
      <?php echo $attachment['file_name']; ?>
    </a>
    <?php if($attachment['staffid'] == get_staff_user_id() || has_permission('manageTasks')){ ?>
    <a href="#" class="pull-right text-danger" onclick="remove_task_attachment(this,<?php echo $attachment['id']; ?>); return false;">
      <i class="fa fa fa-times"></i>
    </a>
    <?php } ?>
  </li>
  <?php } ?>
</ul>
</div>
<div class="clearfix"></div>
<hr />
</div>
<?php } ?>
<div class="row checklist-items-wrapper hide">

 <div class="col-md-12">
  <div id="checklist-items" class="">
  </div>
</div>
<div class="clearfix"></div>
<hr />
</div>
<div class="row">
 <div class="col-md-12 mbot30" id="description">
  <h4 class="bold"><?php echo _l('task_view_description'); ?></h4>
  <?php echo check_for_links($task->description); ?>
</div>
</div>
<div class="row tasks-comments">
 <div class="col-md-12">
  <h4 class="bold"><?php echo _l('task_comments'); ?></h4>
  <textarea name="comment" id="task_comment" rows="5" class="form-control mtop15"></textarea>
  <button type="button" class="btn btn-info mtop10 pull-right" onclick="add_task_comment();">
    <?php echo _l('task_single_add_new_comment'); ?>
  </button>
  <div class="clearfix"></div>
</div>
<div id="task-comments">

  <?php
  $comments = '';
  foreach ($task->comments as $comment) {
    $comments .= '<div class="col-md-12 mbot10 mtop10" data-commentid="' . $comment['id'] . '">';

    if($comment['staffid'] != 0){
     $comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '">' . staff_profile_image($comment['staffid'], array(
      'staff-profile-image-small',
      'media-object img-circle pull-left mright10'
      )) . '</a>';
   } else {
    $comments .= '<img src="'.client_profile_image_url($comment['clientid']).'" class="client-profile-image-small media-object img-circle pull-left mright10">';
  }
  if ($comment['staffid'] == get_staff_user_id() || is_admin()) {
    $comment_added = strtotime($comment['dateadded']);
    $minus_1_hour = strtotime('-1 hours');
    if(get_option('client_staff_add_edit_delete_task_comments_first_hour') == 0 || (get_option('client_staff_add_edit_delete_task_comments_first_hour') == 1 && $comment_added >= $minus_1_hour) || is_admin()){
      $comments .= '<span class="pull-right"><a href="#" onclick="remove_task_comment(' . $comment['id'] . '); return false;"><i class="fa fa-times text-danger"></i></span></a>';
      $comments .= '<span class="pull-right mright5"><a href="#" onclick="edit_task_comment(' . $comment['id'] . '); return false;"><i class="fa fa-pencil-square-o"></i></span></a>';
    }
  }
  $comments .= '<div class="media-body">';
  if($comment['staffid'] != 0){
    $comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '">' . get_staff_full_name($comment['staffid']) . '</a> <br />';
  } else {
    $comments .= '<span class="label label-info mtop5 inline-block">'._l('is_customer_indicator').'</span><br /><a href="' . admin_url('clients/' . $comment['clientid']) . '" class="pull-left">' . get_client_full_name($comment['clientid']) . '</a> <br />';
  }
  $comments .= '<div data-edit-comment="'.$comment['id'].'" class="hide"><textarea rows="5" class="form-control mtop10 mbot10">'.clear_textarea_breaks($comment['content']).'</textarea>
  <button type="button" class="btn btn-info pull-right" onclick="save_edited_comment('.$comment['id'].')">'._l('submit').'</button>
  <button type="button" class="btn btn-default pull-right mright5" onclick="cancel_edit_comment('.$comment['id'].')">'._l('cancel').'</button>
</div>';
$comments .= '<div class="comment-content">'.check_for_links($comment['content']) . '</div><br />';
$comments .= '<small class="mtop10 text-muted">' . _dt($comment['dateadded']) . '</small>';
$comments .= '</div>';
$comments .= '<hr />';
$comments .= '</div>';
}
echo $comments;
?>
</div>
</div>
</div>
</div>
</div>
</div>
<script>
 taskid = '<?php echo $task->id; ?>'
 init_selectpicker();
     // Multiple dropzone appending input to body fix
     $('.dz-hidden-input').remove();
     Dropzone.autoDiscover = false;
     var tasksAttachmentsDropzone = new Dropzone("#task-attachment", {
       clickable: '.add-task-attachments',
       autoProcessQueue: true,
       createImageThumbnails: false,
       addRemoveLinks: false,
       previewTemplate: '<div style="display:none"></div>',
       maxFiles: 10,
     });

     tasksAttachmentsDropzone.on("sending", function(file, xhr, formData) {
       formData.append("taskid", taskid);
     });
     // On post added success
     tasksAttachmentsDropzone.on('complete', function(files, response) {
       if (tasksAttachmentsDropzone.getUploadingFiles().length === 0 && tasksAttachmentsDropzone.getQueuedFiles().length === 0) {
         init_task_modal(taskid);
       }
     });
   </script>
