  <a href="#" class="close_newsfeed" data-close="true"><i class="fa fa-remove"></i></a>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="panel_s">
       <div class="panel-body">
        <?php echo form_open_multipart('admin/newsfeed/add_post',array('class'=>'dropzone','id'=>'new-post-form')); ?>
        <a href="<?php echo admin_url('profile'); ?>">
          <?php echo staff_profile_image($_staff->staffid,array('staff-profile-image-small')); ?>
          <?php echo $_staff->firstname . ' ' . $_staff->lastname ;?></a>
          <textarea name="content" id="post" rows="5" class="form-control" placeholder="<?php echo _l('whats_on_your_mind'); ?>"></textarea>
          <hr />
          <button type="submit" class="btn btn-info pull-right"><?php echo _l('new_post'); ?></button>
          <a href="#" class="btn btn-default add-post-attachments"><i data-toggle="tooltip" title="<?php echo _l('newsfeed_upload_tooltip'); ?>" class="fa fa-files-o"></i></a>
          <select id="post-visibility" class="selectpicker" multiple name="visibility[]" data-width="60%">
           <option value="all" selected><?php echo _l('newsfeed_all_departments'); ?></option>
           <?php foreach($departments as $department){ ?>
           <option value="<?php echo $department['departmentid']; ?>"><?php echo $department['name']; ?></option>
           <?php } ?>
         </select>
         <div class="dz-message" data-dz-message><span></span></div>
         <div class="dropzone-previews"></div>
         <?php echo form_close(); ?>
       </div>
     </div>
     <?php echo form_hidden('total_pages_newsfeed',$total_pages_newsfeed); ?>
     <div id="newsfeed_data"></div>
   </div>
   <?php include_once(APPPATH . 'views/admin/includes/modals/post_likes.php'); ?>
   <?php include_once(APPPATH . 'views/admin/includes/modals/newsfeed_images.php'); ?>
   <?php include_once(APPPATH . 'views/admin/includes/modals/post_comment_likes.php'); ?>
 </div>
