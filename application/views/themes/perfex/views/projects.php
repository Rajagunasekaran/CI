    <div class="panel_s">
      <div class="panel-body">
        <h4 class="bold no-margin"><?php echo _l('clients_my_projects'); ?></h4>
      </div>
    </div>
    <div class="panel_s">
     <div class="panel-body">
      <div class="table-responsive">
        <table class="table dt-table">
         <thead>
          <tr>
            <th><?php echo _l('project_name'); ?></th>
            <th><?php echo _l('project_start_date'); ?></th>
            <th><?php echo _l('project_deadline'); ?></th>
            <th><?php echo _l('project_billing_type'); ?></th>
            <th><?php echo _l('project_status'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($projects as $project){ ?>
          <tr>
            <td><a href="<?php echo site_url('clients/project/'.$project['id']); ?>"><?php echo $project['name']; ?></a></td>
            <td><?php echo _d($project['start_date']); ?></td>
            <td><?php echo _d($project['deadline']); ?></td>
            <td>  <?php
              if($project['billing_type'] == 1){
                $type_name = 'project_billing_type_fixed_cost';
              } else if($project['billing_type'] == 2){
                $type_name = 'project_billing_type_project_hours';
              } else {
                $type_name = 'project_billing_type_project_task_hours';
              }
              echo _l($type_name);
              ?></td>
              <td>
                <?php
                if($project['status'] == 1){
                  $label = 'default';
                } else if($project['status'] == 2){
                  $label = 'info';
                } else if($project['status'] == 3){
                 $label = 'warning';
               } else {
                 $label = 'success';
               }
               echo '<span class="label label-'.$label.' pull-left">'._l('project_status_'.$project['status']).'</span>';
               ?>
             </td>
           </tr>
           <?php } ?>
         </tbody>
       </table>
     </div>
   </div>
 </div>
