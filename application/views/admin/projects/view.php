<?php init_head(); ?>
<div id="wrapper">
  <?php echo form_hidden('project_id',$project->id) ?>
  <div class="content">
    <div class="row">
      <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
      <div class="col-md-12">
        <?php if($this->projects_model->timers_started_for_project($project->id) && has_permission('manageProjects') && $project->status == 1){ ?>
        <div class="alert alert-warning">
          <?php echo _l('project_not_started_status_tasks_timers_found'); ?>
        </div>
        <?php } ?>
        <?php if(!has_customer_permission('projects',$project->clientid)){ ?>
        <div class="alert alert-warning">
          <?php echo _l('project_customer_permission_warning'); ?>
        </div>
        <?php } ?>
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-8 project-heading">
                <h2 class="no-margin bold text-info">
                  <?php echo $project->name; ?>
                </h2>
                <h4 class="no-margin"><a href="<?php echo admin_url(); ?>clients/client/<?php echo $project->clientid; ?>"><?php echo $project->client_data->company; ?></a></h4>
              </div>
              <div class="col-md-4 text-right">
               <?php if(has_permission('manageProjects')){ ?>
               <a href="<?php echo admin_url('projects/project/'.$project->id); ?>" data-toggle="tooltip" data-title="<?php echo _l('edit_project'); ?>" class="btn btn-default"><i class="fa fa-pencil-square-o"></i></a>
               <a href="#" onclick="copy_project(); return false;" data-toggle="tooltip" data-title="<?php echo _l('copy_project'); ?>" class="btn btn-default"><i class="fa fa-clone"></i></a>
               <a href="<?php echo admin_url('projects/delete/'.$project->id); ?>" data-toggle="tooltip" data-title="<?php echo _l('delete_project'); ?>" class="btn btn-danger _delete"><i class="fa fa-remove"></i></a>
               <?php
               $invoice_func = 'pre_invoice_project';
               ?>
               <a href="#" onclick="<?php echo $invoice_func; ?>(<?php echo $project->id; ?>); return false;" class="btn btn-info"><?php echo _l('invoice_project'); ?></a>

               <?php } ?>
             </div>
           </div>
         </div>
       </div>
       <div class="panel_s">
        <div class="panel-body">
          <?php
          if($project->status == 1){
            $label = 'default';
          } else if($project->status == 2){
            $label = 'info';
          } else if($project->status == 3){
           $label = 'warning';
         } else {
           $label = 'success';
         }
         ?>
         <?php echo '<div class="ribbon '.$label.'"><span>'._l('project_status_'.$project->status).'</span></div>'; ?>
         <?php $this->load->view('admin/projects/project_tabs'); ?>
       </div>
     </div>
     <div class="panel_s">
      <div class="panel-body">
        <?php echo $group_view; ?>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
</div>
<?php if(isset($discussion)){
  echo form_hidden('discussion_id',$discussion->id);
  echo form_hidden('discussion_user_profile_image_url',$discussion_user_profile_image_url);
  echo form_hidden('current_user_is_admin',$current_user_is_admin);
}
?>
<div id="invoice_project"></div>
<div id="pre_invoice_project"></div>
<?php $this->load->view('admin/projects/milestone'); ?>
<?php $this->load->view('admin/projects/timesheet'); ?>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php init_tail(); ?>
<script src="<?php echo site_url('assets/js/projects.js'); ?>"></script>
<!-- For invoices table -->
<script>
  taskid = '<?php echo $this->input->get('taskid'); ?>';
  var hidden_columns = [2,5];</script>
  <script>
   var gantt_data = <?php echo json_encode($gantt_data); ?>;
   var discussion_id = $('input[name="discussion_id"]').val();
   var discussion_user_profile_image_url = $('input[name="discussion_user_profile_image_url"]').val();
   var current_user_is_admin = $('input[name="current_user_is_admin"]').val();
   var project_id = $('input[name="project_id"]').val();


   $(document).ready(function() {

    var circle = $('.project-progress').circleProgress({
     value: '<?php echo $percent_circle; ?>',
     size: 180,
     fill: {
       gradient: ["#D8EDA3", "#D8EDA3"]
     }
   }).on('circle-animation-progress', function(event, progress, stepValue) {
     $(this).find('strong.project-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
   });

   $('#discussion-comments').comments({
    roundProfilePictures: true,
    textareaRows: 2,
    textareaRowsOnFocus: 5,
    profilePictureURL:discussion_user_profile_image_url,
    enableUpvoting: false,
    enableAttachments:true,
    popularText:'',
    enableDeletingCommentWithReplies:true,
    textareaPlaceholderText:'<?php echo _l('discussion_add_comment'); ?>',
    newestText:'<?php echo _l('discussion_newest'); ?>',
    oldestText:'<?php echo _l('discussion_oldest'); ?>',
    attachmentsText:'<?php echo _l('discussion_attachments'); ?>',
    sendText:'<?php echo _l('discussion_send'); ?>',
    replyText:'<?php echo _l('discussion_reply'); ?>',
    editText:'<?php echo _l('discussion_edit'); ?>',
    editedText:'<?php echo _l('discussion_edited'); ?>',
    youText:'<?php echo _l('discussion_you'); ?>',
    saveText:'<?php echo _l('discussion_save'); ?>',
    deleteText:'<?php echo _l('discussion_delete'); ?>',
    viewAllRepliesText:'<?php echo _l('discussion_view_all_replies') . ' (__replyCount__)'; ?>',
    hideRepliesText:'<?php echo _l('discussion_hide_replies'); ?>',
    noCommentsText:'<?php echo _l('discussion_no_comments'); ?>',
    noAttachmentsText:'<?php echo _l('discussion_no_attachments'); ?>',
    attachmentDropText:'<?php echo _l('discussion_attachments_drop'); ?>',
    currentUserIsAdmin:current_user_is_admin,
    getComments: function(success, error) {
      $.get(admin_url + 'projects/get_discussion_comments/'+discussion_id,function(response){
        success(response);
      },'json');
    },
    postComment: function(commentJSON, success, error) {
      $.ajax({
        type: 'post',
        url: admin_url + 'projects/add_discussion_comment/'+discussion_id,
        data: commentJSON,
        success: function(comment) {
          comment = $.parseJSON(comment);
          success(comment)
        },
        error: error
      });
    },
    putComment: function(commentJSON, success, error) {
      $.ajax({
        type: 'post',
        url: admin_url + 'projects/update_discussion_comment',
        data: commentJSON,
        success: function(comment) {
          comment = $.parseJSON(comment);
          success(comment)
        },
        error: error
      });
    },
    deleteComment: function(commentJSON, success, error) {
      $.ajax({
        type: 'post',
        url: admin_url + 'projects/delete_discussion_comment/'+commentJSON.id,
        success: success,
        error: error
      });
    },
    timeFormatter: function(time) {
      return moment(time).fromNow();
    },
    uploadAttachments: function(commentArray, success, error) {
      var responses = 0;
      var successfulUploads = [];

      var serverResponded = function() {
        responses++;
            // Check if all requests have finished
            if(responses == commentArray.length) {
                // Case: all failed
                if(successfulUploads.length == 0) {
                  error();
                // Case: some succeeded
              } else {
                successfulUploads = $.parseJSON(successfulUploads);
                success(successfulUploads)
              }
            }
          }
          $(commentArray).each(function(index, commentJSON) {
            // Create form data
            var formData = new FormData();
            $(Object.keys(commentJSON)).each(function(index, key) {
              var value = commentJSON[key];
              if(value) formData.append(key, value);
            });
            $.ajax({
              url: admin_url + 'projects/add_discussion_comment/'+discussion_id,
              type: 'POST',
              data: formData,
              cache: false,
              contentType: false,
              processData: false,
              success: function(commentJSON) {
                successfulUploads.push(commentJSON);
                serverResponded();
              },
              error: function(data) {
                serverResponded();
              },
            });
          });
        }
      });
 });
</script>
</body>
</html>




