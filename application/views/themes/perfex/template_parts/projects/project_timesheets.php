<div class="table-responsive">
    <table class="table dt-table">
        <thead>
            <tr>
                <th><?php echo _l('project_timesheet_user'); ?></th>
                <th><?php echo _l('project_timesheet_task'); ?></th>
                <th><?php echo _l('project_timesheet_start_time'); ?></th>
                <th><?php echo _l('project_timesheet_end_time'); ?></th>
                <th><?php echo _l('project_timesheet_time_spend'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($timesheets as $timesheet){ ?>
            <tr>
                <td><?php echo staff_profile_image($timesheet['staff_id'],array('staff-profile-image-small')) .' ' .  $timesheet['staff_name']; ?></td>
                <td><a href="<?php echo site_url('clients/project/'.$project->id.'?group=project_tasks&taskid='.$timesheet['task_data']->id); ?>"><?php echo $timesheet['task_data']->name; ?></a></td>
                <td><?php echo strftime(get_strftime_format().' %H:%M', $timesheet['start_time']); ?></td>
                <td><?php if(!is_null($timesheet['end_time'])){echo strftime(get_strftime_format().' %H:%M', $timesheet['end_time']);} ?></td>
                <td>
                    <?php
                    if($timesheet['total_spent'] != ''){
                        echo format_seconds($timesheet['total_spent']);
                    }
                    ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
