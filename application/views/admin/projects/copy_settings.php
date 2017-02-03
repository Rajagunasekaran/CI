<!-- Copy Project -->
<div class="modal fade" id="copy_project" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('projects/copy/'.$project->id),array('id'=>'copy_form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                   <?php echo _l('copy_project'); ?>
               </h4>
           </div>
           <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" class="copy" name="tasks" checked>
                        <label for=""><?php echo _l('tasks'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary mleft10">
                        <input type="checkbox" name="tasks_include_checklist_items" checked>
                        <label for=""><small><?php echo _l('copy_project_task_include_check_list_items'); ?></small></label>
                    </div>
                    <div class="checkbox checkbox-primary mleft10">
                        <input type="checkbox" name="task_include_assignees" checked>
                        <label for=""><small><?php echo _l('copy_project_task_include_assignees'); ?></small></label>
                    </div>
                     <div class="checkbox checkbox-primary mleft10">
                        <input type="checkbox" name="task_include_followers" checked>
                        <label for=""><small><?php echo _l('copy_project_task_include_followers'); ?></small></label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="milestones" checked>
                        <label for=""><?php echo _l('project_milestones'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" name="members" class="copy" checked>
                        <label for=""><?php echo _l('project_members'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary">
                      <input type="checkbox" name="discussions">
                      <label for=""><?php echo _l('project_discussions'); ?> <small><?php echo _l('copy_project_discussions_not_included'); ?></small>
                      </label>
                  </div>
                    <div class="row">
                            <div class="col-md-6">
                             <?php echo render_date_input('start_date','project_start_date',_d(date('Y-m-d'))); ?>
                         </div>
                         <div class="col-md-6">
                            <?php echo render_date_input('deadline','project_deadline'); ?>
                        </div>
                    </div>
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info"><?php echo _l('copy_project'); ?></button>
    </div>
</div><!-- /.modal-content -->
<?php echo form_close(); ?>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Copy Project end -->
