   <?php foreach($activity as $activity){
    ?>
    <div class="display-block project-activity">
     <?php if($activity['staff_id'] != 0){
        $fullname = get_staff_full_name($activity['staff_id']) . ' - ';
        ?>
        <a href="<?php echo admin_url('profile/'.$activity['staff_id']); ?>"><?php echo staff_profile_image($activity['staff_id'],array('staff-profile-image-small','pull-left mright10')); ?></a>
        <?php } else if($activity['client_id'] != 0){
            $fullname = '<span class="label label-info inline-block mbot5">'._l('is_customer_indicator') . '</span> ' . get_client_full_name($activity['client_id']) . ' - ';
            ?>
            <a href="<?php echo admin_url('clients/client/'.$activity['client_id']); ?>">
                <img src="<?php echo client_profile_image_url($activity['client_id']); ?>" class="client-profile-image-small pull-left mright10">
            </a>
            <?php } else {$fullname = '[CRON] ';} ?>
            <div class="media-body">
                <div class="pull-right">
                   <?php if($activity['visible_to_customer'] == 1){
                    $checked = 'checked';
                } else {
                    $checked = '';
                }
                ?>
                <small><?php echo _l('project_activity_visible_to_customer'); ?></small><br />
                <input type="checkbox"<?php if(!has_permission('manageProjects')){echo 'disabled';} ?> class="switch-box input-xs" data-size="mini" data-id="<?php echo $activity['id']; ?>" data-switch-url="<?php echo ADMIN_URL; ?>/projects/change_activity_visibility" <?php echo $checked; ?>>
            </div>
            <div class="display-block">
              <h5 class="bold no-margin">
              <?php echo $fullname . $activity['description']; ?>
             </h5>
             <p class="text-muted"><?php echo $activity['additional_data']; ?></p>
         </div>
         <small class="text-muted"><?php echo _dt($activity['dateadded']); ?></small>
     </div>
     <hr />
 </div>
 <?php } ?>
