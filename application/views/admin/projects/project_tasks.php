            <!-- Project Tasks -->
            <a href="#" class="btn btn-default" onclick="taskTable(); return false;"><i class="fa fa-th-list"></i></a>
            <?php if(has_permission('manageTasks')){ ?>
            <a href="#" class="btn btn-info new-task-phase hide" onclick="new_task_from_relation('.table-rel-tasks'); return false;"><?php echo _l('new_task'); ?></a>
            <?php } ?>
            <div class="tasks-phases hide">
              <?php
              $milestones = $this->projects_model->get_milestones($project->id);
              ?>
              <div class="row">
                <?php
                $uncategorized = $this->projects_model->get_tasks($project->id,array('milestone'=>0));
                if(count($uncategorized) > 0){ ?>
                <div class="col-md-3 mtop25">
                 <div class="panel-heading info-bg">
                  <?php echo _l('milestones_uncategorized'); ?>
                  <?php echo '<br /><small class="bold">' . _l('milestone_total_logged_time') . ': ' . format_seconds($this->projects_model->calc_milestone_logged_time($project->id,0)). '</small>';
                  ?>
                </div>
                <div class="panel-body">
                  <?php
                  foreach($uncategorized as $task){ ?>
                  <div class="media">
                   <?php if($this->tasks_model->is_task_assignee(get_staff_user_id(),$task['id'])){ ?>
                   <div class="media-left">
                    <?php echo staff_profile_image(get_staff_user_id(),array('staff-profile-image-small pull-left'),'small',array('data-toggle'=>'tooltip','data-title'=>_l('project_task_assigned_to_user'))); ?>
                  </div>
                  <?php } ?>
                  <div class="media-body">
                    <a href="#" class="task_milestone pull-left mtop5<?php if($task['finished'] ==1){echo ' line-throught text-muted';} ?>" onclick="init_task_modal(<?php echo $task['id']; ?>); return false;"><?php echo $task['name']; ?></a>
                    <small class="pull-right text-success"><?php if($task['finished'] == 1){echo _l('task_finished'); } ?></small>
                    <small class="text-muted"><?php echo _l('task_total_logged_time'); ?>
                      <b>
                       <?php echo format_seconds($this->tasks_model->calc_task_total_time($task['id'])); ?>
                     </b>
                   </small>
                 </div>
               </div>
               <hr class="no-margin"/>
               <?php } ?>
             </div>
             <?php
             $total_project_tasks  = total_rows('tblstafftasks', array(
              'rel_type' => 'project',
              'rel_id' => $project->id,
              'milestone'=>0,
              ));
             $total_finished_tasks = total_rows('tblstafftasks', array(
              'rel_type' => 'project',
              'rel_id' => $project->id,
              'finished' => 1,
              'milestone'=>0,
              ));
             $percent              = 0;
             if ($total_finished_tasks >= floatval($total_project_tasks)) {
              $percent = 100;
            } else {
              if ($total_project_tasks !== 0) {
                $percent = number_format(($total_finished_tasks * 100) / $total_project_tasks, 2);
              }
            }
            ?>
            <div class="panel-footer">
              <div class="progress no-margin progress-bg-dark">
                <div class="progress-bar not-dynamic progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent; ?>">
                </div>
              </div>
            </div>
          </div>
          <?php } ?>
          <?php foreach($milestones as $milestone){
           $total_project_tasks  = total_rows('tblstafftasks', array(
            'rel_type' => 'project',
            'rel_id' => $project->id,
            'milestone'=>$milestone['id'],
            ));
           $total_finished_tasks = total_rows('tblstafftasks', array(
            'rel_type' => 'project',
            'rel_id' => $project->id,
            'finished' => 1,
            'milestone'=>$milestone['id'],
            ));
           $percent              = 0;
           if ($total_finished_tasks >= floatval($total_project_tasks)) {
            $percent = 100;
          } else {
            if ($total_project_tasks !== 0) {
             $percent = number_format(($total_finished_tasks * 100) / $total_project_tasks, 2);
           }
         }
         ?>
         <div class="col-md-3 mtop25">
          <div class="panel-heading task-phase">
            <span class="bold"><?php echo $milestone['name']; ?></span>
            <?php echo '<br /><small class="phase-logged-time">' . _l('milestone_total_logged_time') . ': ' . format_seconds($this->projects_model->calc_milestone_logged_time($project->id,$milestone['id'])). '</small>';
            ?>
          </div>
          <div class="panel-body">
            <?php
            $tasks = $this->projects_model->get_tasks($project->id,array('milestone'=>$milestone['id']));
            if(count($tasks) == 0){
              echo _l('milestone_no_tasks_found');
            }
            foreach($tasks as $task){ ?>
            <div class="media">
             <?php if($this->tasks_model->is_task_assignee(get_staff_user_id(),$task['id'])){ ?>
             <div class="media-left">
              <?php echo staff_profile_image(get_staff_user_id(),array('staff-profile-image-small pull-left'),'small',array('data-toggle'=>'tooltip','data-title'=>_l('project_task_assigned_to_user'))); ?>
            </div>
            <?php } ?>
            <div class="media-body">
              <a href="#" class="task_milestone pull-left mtop5<?php if($task['finished'] ==1){echo ' line-throught text-muted';} ?>" onclick="init_task_modal(<?php echo $task['id']; ?>); return false;"><?php echo $task['name']; ?></a>
              <small class="pull-right text-success"><?php if($task['finished'] == 1){echo _l('task_finished'); } ?></small>
              <small class="text-muted"><?php echo _l('task_total_logged_time'); ?>
                <b>
                 <?php echo format_seconds($this->tasks_model->calc_task_total_time($task['id'])); ?>
               </b>
             </small>
           </div>
         </div>
         <hr class="no-margin"/>
         <?php } ?>
       </div>
       <div class="panel-footer">
        <div class="progress no-margin progress-bg-dark">
          <div class="progress-bar not-dynamic progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent; ?>">
          </div>
        </div>
      </div>

    </div>
    <?php } ?>
  </div>
</div>
<div class="tasks-table">
  <?php
  $cview = '';
  // Show only tasks assigned to user if is not admin or dont have permission manage projects
  if(!has_permission('manageProjects') || !is_admin()){
    $cview = 'my_tasks';
  }
  echo form_hidden('custom_view',$cview); ?>
  <div class="btn-group mtop5">
    <?php $this->load->view('admin/tasks/tasks_filter_by',array('view_table_name'=>'.table-rel-tasks')); ?>
  </div>
  <?php init_relation_tasks_table(array( 'data-new-rel-id'=>$project->id,'data-new-rel-type'=>'project')); ?>
</div>

