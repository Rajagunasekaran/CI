       <?php if($project->settings->view_tasks == 1){ ?>
       <!-- Project Tasks -->
       <?php if($project->settings->view_milestones == 1 && !isset($view_task)){ ?>
       <a href="#" class="btn btn-default" onclick="taskTable(); return false;"><i class="fa fa-th-list"></i></a>
       <div class="tasks-phases">
        <?php
        $milestones = $this->projects_model->get_milestones($project->id);
        ?>
        <div class="row">
          <?php
          $uncategorized = $this->projects_model->get_tasks($project->id,array('milestone'=>0));
          if(count($uncategorized) > 0){ ?>
          <div class="col-md-4 mtop25">
           <div class="panel-heading info-bg">
            <?php echo _l('milestones_uncategorized'); ?>
            <?php echo '<br /><small class="bold">' . _l('milestone_total_logged_time') . ': ' . format_seconds($this->projects_model->calc_milestone_logged_time($project->id,0)). '</small>';
            ?>
          </div>
          <div class="panel-body">
            <?php
            foreach($uncategorized as $task){ ?>
            <div class="media">
              <div class="media-body">
              <a href="<?php echo site_url('clients/project/'.$project->id.'?group=project_tasks&taskid='.$task['id']); ?>" class="task_milestone pull-left mtop5<?php if($task['finished'] ==1){echo ' line-throught text-muted';} ?>"><?php echo $task['name']; ?></a>
                <small class="pull-right"><?php if($task['finished'] == 1){echo _l('task_finished'); } ?></small>
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
              <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent; ?>">
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
       <div class="col-md-4 mtop25">
        <div class="panel-heading task-phase">
          <span class="bold"><?php echo $milestone['name']; ?></span>
          <?php echo '<br /><small>' . _l('milestone_total_logged_time') . ': ' . format_seconds($this->projects_model->calc_milestone_logged_time($project->id,$milestone['id'])). '</small>';
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
            <div class="media-body">
              <a href="<?php echo site_url('clients/project/'.$project->id.'?group=project_tasks&taskid='.$task['id']); ?>" class="task_milestone pull-left mtop5<?php if($task['finished'] ==1){echo ' line-throught text-muted';} ?>"><?php echo $task['name']; ?></a>
              <br />
              <small class="pull-right text-success"><?php if($task['finished'] == 1){echo _l('task_finished'); } ?></small>
            </div>
          </div>
          <hr class="no-margin"/>
          <?php } ?>
        </div>
        <div class="panel-footer">
          <div class="progress no-margin progress-bg-dark">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent; ?>">
            </div>
          </div>
        </div>

      </div>
      <?php } ?>
    </div>
  </div>
  <?php } ?>
  <?php if(!isset($view_task)){ ?>
  <div class="tasks-table <?php if($project->settings->view_milestones == 1){echo 'hide';} ?>">
    <?php echo form_hidden('custom_view'); ?>
    <div class="btn-group mtop5">
     <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fa fa-list" data-toggle="tooltip" data-title="<?php echo _l('tasks_filter_by'); ?>"></i>
    </button>
    <ul class="dropdown-menu width220">
      <li>
        <a href="#" onclick="dt_custom_view('.table-tasks',2,''); return false;">
          <?php echo _l('task_list_all'); ?>
        </a>
      </li>
      <li>
        <a href="#" onclick="dt_custom_view('.table-tasks',2,'<?php echo _l('task_table_is_finished_indicator'); ?>'); return false;">
          <?php echo _l('task_list_finished'); ?>
        </a>
      </li>
      <li>
        <a href="#" onclick="dt_custom_view('.table-tasks',2,'<?php echo _l('task_table_is_not_finished_indicator'); ?>'); return false;">
          <?php echo _l('task_list_unfinished'); ?>
        </a>
      </li>
    </ul>
  </div>
  <div class="table-responsive">
    <table class="table dt-table table-tasks">
      <thead>
        <tr>
          <td><?php echo _l('tasks_dt_name'); ?></td>
          <td><?php echo _l('tasks_dt_datestart'); ?></td>
          <td><?php echo _l('tasks_dt_finished'); ?></td>
          <td><?php echo _l('task_billable'); ?></td>
          <td><?php echo _l('task_billed'); ?></td>
          <?php
          $custom_fields = get_custom_fields('tasks',array('show_on_client_portal'=>1));
          foreach($custom_fields as $field){ ?>
          <th><?php echo $field['name']; ?></th>
          <?php } ?>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach($project_tasks as $task){ ?>
        <tr class="<?php if($task['finished'] == 0){echo 'task-unfinished-table';} ?>">
          <td><a href="<?php echo site_url('clients/project/'.$project->id.'?group=project_tasks&taskid='.$task['id']); ?>"><?php echo $task['name']; ?></a></td>
          <td><?php echo _d($task['startdate']); ?></td>
          <td>
            <?php
            if($task['finished'] == 1){
              $finished = _l('task_table_is_finished_indicator');
            } else {
              $finished = _l('task_table_is_not_finished_indicator');
            }
            echo $finished;
            ?>
          </td>
          <td>
            <?php
            if($task['billable'] == 1){
              $billable = _l("task_billable_yes");
            } else {
              $billable = _l("task_billable_no");
            }
            echo $billable;
            ?>
          </td>
          <td>
            <?php
            if($task['billed'] == 1){
              $billed = '<span class="label label-success pull-left">'._l('task_billed_yes').'</span>';
            } else {
              $billed = '<span class="label label-danger pull-left">'._l('task_billed_no').'</span>';
            }
            echo $billed;
            ?>
          </td>
          <?php foreach($custom_fields as $field){ ?>
          <td><?php echo get_custom_field_value($task['id'],$field['id'],'tasks'); ?></td>
          <?php } ?>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
<?php } else {
  get_template_part('projects/project_task');
}
}

?>

