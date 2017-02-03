           <div class="row">
            <div class="col-md-6 project-overview-column">
              <div class="panel-heading project-info-bg no-radius"><?php echo _l('project_overview'); ?>
                <?php
                if(has_permission('manageProjects')){ ?>
                <a href="<?php echo admin_url('projects/view_project_as_client/'.$project->id .'/'.$project->clientid); ?>" target="_blank" class="pull-right"><?php echo _l('project_view_as_client'); ?></a>
                <?php } ?>
              </div>
              <div class="panel-body no-radius">
                <div class="row">
                  <div class="col-md-6">
                    <table class="table table-borded no-margin">
                      <tbody>
                        <tr>
                          <td class="bold"><?php echo _l('project_customer'); ?></td>
                          <td><a href="<?php echo admin_url(); ?>clients/client/<?php echo $project->clientid; ?>"><?php echo $project->client_data->company; ?></a>
                          </td>
                        </tr>
                        <tr>
                          <td class="bold"><?php echo _l('project_billing_type'); ?></td>
                          <td>
                            <?php
                            if($project->billing_type == 1){
                              $type_name = 'project_billing_type_fixed_cost';
                            } else if($project->billing_type == 2){
                              $type_name = 'project_billing_type_project_hours';
                            } else {
                              $type_name = 'project_billing_type_project_task_hours';
                            }
                            echo _l($type_name);
                            ?>
                          </td>
                          <?php if(has_permission('manageProjects')) { ?>
                          <?php if($project->billing_type == 1 || $project->billing_type == 2){
                            echo '<tr>';
                            if($project->billing_type == 1){
                              echo '<td class="bold">'._l('project_total_cost').'</td>';
                              echo '<td>'.format_money($project->project_cost,$currency->symbol).'</td>';
                            } else {
                              echo '<td class="bold">'._l('project_rate_per_hour').'</td>';
                              echo '<td>'.format_money($project->project_rate_per_hour,$currency->symbol).'</td>';
                            }
                            echo '<tr>';
                          }
                        }
                        ?>
                        <tr>
                          <td class="bold"><?php echo _l('project_status'); ?></td>
                          <td><?php echo _l('project_status_'.$project->status); ?></td>
                        </tr>
                        <tr>
                          <td class="bold"><?php echo _l('project_datecreated'); ?></td>
                          <td><?php echo _d($project->project_created); ?></td>
                        </tr>
                        <tr>
                          <td class="bold"><?php echo _l('project_start_date'); ?></td>
                          <td><?php echo _d($project->start_date); ?></td>
                        </tr>
                        <tr>
                          <td class="bold"><?php echo _l('project_deadline'); ?></td>
                          <td><?php echo _d($project->deadline); ?></td>
                        </tr>
                        <?php if($project->billing_type == 1){ ?>
                        <tr>
                          <td class="bold"><?php echo _l('project_overview_total_logged_hours'); ?></td>
                          <td><?php echo format_seconds($this->projects_model->total_logged_time($project->id)); ?></td>
                        </tr>
                        <?php } ?>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-md-6 text-center">
                  <div class="project-progress relative mtop15" data-thickness="24" data-reverse="true">
                    <strong class="project-percent"></strong>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
          </div>

          <div class="col-md-6 project-overview-column">
            <?php if(has_permission('manageProjects')) { ?>
            <div class="row">
             <div class="col-md-12">
               <h4 class="bold no-margin"><?php echo _l('project_tasks_overview'); ?></h4>

               <?php if($project->billing_type == 2){
                echo ' <hr />';
                $seconds = $this->projects_model->total_logged_time($project->id);
                $total = $this->projects_model->calculate_total_by_project_hourly_rate($seconds,$project->project_rate_per_hour);
                $hours = $total['hours'];
                $total_money = $total['total_money'];
                ?>
                <div class="row">
                 <div class="col-md-3">
                   <p class="text-uppercase text-muted">Logged Hours: <span class="bold"><?php echo $hours; ?></span></p>
                   <p class="bold font-medium"><?php echo format_money($total_money,$currency->symbol); ?></p>
                 </div>
                 <div class="col-md-3">
                  <?php
                  $billable_tasks = $this->projects_model->get_tasks($project->id,array('billable'=>1));
                  $seconds = 0;
                  foreach($billable_tasks as $task){
                    $seconds += $this->tasks_model->calc_task_total_time($task['id']);
                  }
                  $total = $this->projects_model->calculate_total_by_project_hourly_rate($seconds,$project->project_rate_per_hour);
                  $hours = $total['hours'];
                  $total_money = $total['total_money'];
                  ?>
                  <p class="text-uppercase text-info">Billable Hours: <span class="bold"><?php echo $hours; ?></span></p>
                  <p class="bold font-medium"><?php echo format_money($total_money,$currency->symbol); ?></p>
                </div>
                <div class="col-md-3">
                  <?php
                  $billed_tasks = $this->projects_model->get_tasks($project->id,array('billable'=>1,'billed'=>1));
                  $seconds = 0;
                  foreach($billed_tasks as $task){
                    $seconds += $this->tasks_model->calc_task_total_time($task['id']);
                  }
                  $total = $this->projects_model->calculate_total_by_project_hourly_rate($seconds,$project->project_rate_per_hour);
                  $hours = $total['hours'];
                  $total_money = $total['total_money'];
                  ?>
                  <p class="text-uppercase text-success">Billed Hours: <span class="bold"><?php echo $hours; ?></span></p>
                  <p class="bold font-medium"><?php echo format_money($total_money,$currency->symbol); ?></p>

                </div>
                <div class="col-md-3">
                  <?php
                  $billed_tasks = $this->projects_model->get_tasks($project->id,array('billable'=>1,'billed'=>0));
                  $seconds = 0;
                  foreach($billed_tasks as $task){
                    $seconds += $this->tasks_model->calc_task_total_time($task['id']);
                  }
                  $total = $this->projects_model->calculate_total_by_project_hourly_rate($seconds,$project->project_rate_per_hour);
                  $hours = $total['hours'];
                  $total_money = $total['total_money'];
                  ?>
                  <p class="text-uppercase text-danger">Unbilled Hours: <span class="bold"><?php echo $hours; ?></span></p>
                  <p class="bold font-medium"><?php echo format_money($total_money,$currency->symbol); ?></p>

                </div>
              </div>

              <?php } else if($project->billing_type == 3){
                   echo ' <hr />';
                ?>

                <div class="row">
                  <div class="col-md-3">
                    <?php
                    $total = $this->projects_model->calculate_total_by_task_hourly_rate($tasks);
                    $total_seconds = $total['total_seconds'];
                    $total_money = $total['total_money'];
                    ?>
                    <p class="text-uppercase text-muted">Logged Hours: <span class="bold"><?php echo sec2qty($total_seconds); ?></span></p>
                    <p class="bold font-medium"><?php echo format_money($total_money,$currency->symbol); ?></p>

                  </div>
                  <div class="col-md-3">
                   <?php
                   $tasks = $this->projects_model->get_tasks($project->id,array('billable'=>1));
                   $total = $this->projects_model->calculate_total_by_task_hourly_rate($tasks);
                   $total_seconds = $total['total_seconds'];
                   $total_money = $total['total_money'];
                   ?>
                   <p class="text-uppercase text-info">Billable Hours: <span class="bold"><?php echo sec2qty($total_seconds); ?></span></p>
                   <p class="bold font-medium"><?php echo format_money($total_money,$currency->symbol); ?></p>
                 </div>
                 <div class="col-md-3">
                   <?php
                   $tasks = $this->projects_model->get_tasks($project->id,array('billable'=>1,'billed'=>1));
                   $total = $this->projects_model->calculate_total_by_task_hourly_rate($tasks);
                   $total_seconds = $total['total_seconds'];
                   $total_money = $total['total_money'];
                   ?>
                   <p class="text-uppercase text-success">Billed Hours: <span class="bold"><?php echo sec2qty($total_seconds); ?></span></p>
                   <p class="bold font-medium"><?php echo format_money($total_money,$currency->symbol); ?></p>
                 </div>
                 <div class="col-md-3">
                   <?php
                   $tasks = $this->projects_model->get_tasks($project->id,array('billable'=>1,'billed'=>0));
                   $total = $this->projects_model->calculate_total_by_task_hourly_rate($tasks);
                   $total_seconds = $total['total_seconds'];
                   $total_money = $total['total_money'];
                   ?>
                   <p class="text-uppercase text-danger">Unbilled Hours: <span class="bold"><?php echo sec2qty($total_seconds); ?></span></p>
                   <p class="bold font-medium"><?php echo format_money($total_money,$currency->symbol); ?></p>
                 </div>
               </div>
               <?php } ?>
             </div>
           </div>
           <hr />
           <?php } ?>
           <div class="row">
            <div class="col-md-6">
              <div class="progress no-margin project-overview-progress-bar">
                <div class="progress-bar not-dynamic progress-bar-warning no-percent-text" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $project_time_left_percent; ?>">
                  <span class="project-progress-number"><?php echo $project_days_left; ?> / <?php echo $project_total_days; ?> <?php echo _l('project_days_left'); ?></span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="progress no-margin project-overview-progress-bar">
                <div class="progress-bar not-dynamic bg-light-green no-percent-text" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $tasks_not_completed_progress; ?>">
                  <span class="project-progress-number"><?php echo $tasks_not_completed; ?> / <?php echo $total_tasks; ?> <?php echo _l('project_open_tasks'); ?></span>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>
      <hr />
      <div class="row">
       <div class="col-md-6">
        <div class="panel-heading project-info-bg no-radius"><?php echo _l('project_description'); ?></div>
        <div class="panel-body no-radius">
          <?php echo $project->description; ?>
        </div>
      </div>
      <div class="col-md-6 team-members">
        <div class="panel-heading project-info-bg no-radius"><?php echo _l('project_members'); ?></div>
        <div class="panel-body no-radius">
          <?php
          foreach($members as $member){
            $member_tasks_assigned = $this->tasks_model->get_tasks_by_staff_id($member['staff_id'],array('rel_id'=>$project->id,'rel_type'=>'project'));

            $seconds = 0;
            foreach($member_tasks_assigned as $member_task){
              $seconds += $this->tasks_model->calc_task_total_time($member_task['id'],' AND staff_id='.$member['staff_id']);
            }

            ?>
            <div class="media">
             <div class="media-left">
              <a href="<?php echo admin_url('profile/'.$member["staff_id"]); ?>">
                <?php echo staff_profile_image($member['staff_id'],array('staff-profile-image-small','media-object')); ?>
              </a>
            </div>
            <div class="media-body">
              <?php if(has_permission('manageProjects')){ ?>
              <a href="<?php echo admin_url('projects/remove_team_member/'.$project->id.'/'.$member['staff_id']); ?>" class="pull-right text-danger"><i class="fa fa fa-times"></i></a>
              <?php } ?>
              <h5 class="media-heading mtop5"><a href="<?php echo admin_url('profile/'.$member["staff_id"]); ?>"><?php echo get_staff_full_name($member['staff_id']); ?></a><br /><small class="text-muted"><?php echo _l('total_logged_hours_by_staff') .': '.format_seconds($seconds); ?></small></h5>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
