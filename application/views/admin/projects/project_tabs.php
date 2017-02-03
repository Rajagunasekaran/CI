<ul class="nav nav-tabs no-margin" role="tablist">
    <li class="active">
        <a data-group="project_overview" href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview'); ?>" role="tab"><i class="fa fa-th" aria-hidden="true"></i> <?php echo _l('project_overview'); ?></a>
    </li>
    <li>
        <a data-group="project_tasks" href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_tasks'); ?>" role="tab"><i class="fa fa-check-circle" aria-hidden="true"></i> <?php echo _l('tasks'); ?></a>
    </li>
    <li>
        <a data-group="project_files" href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_files'); ?>" role="tab"><i class="fa fa-files-o" aria-hidden="true"></i> <?php echo _l('project_files'); ?></a>
    </li>
    <li>
        <a data-group="project_gantt" href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_gantt'); ?>" role="tab"><i class="fa fa-line-chart" aria-hidden="true"></i> <?php echo _l('project_gant'); ?></a>
    </li>
    <li>
        <a data-group="project_milestones" href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_milestones'); ?>" role="tab"><i class="fa fa-rocket" aria-hidden="true"></i> <?php echo _l('project_milestones'); ?></a>
    </li>
    <li>
        <a data-group="project_timesheets" href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_timesheets'); ?>" role="tab"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo _l('project_timesheets'); ?></a>
    </li>
    <li>
        <a data-group="project_discussions" href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_discussions'); ?>" role="tab"><i class="fa fa-commenting" aria-hidden="true"></i> <?php echo _l('project_discussions'); ?></a>
    </li>
    <li>
        <a data-group="project_notes" href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_notes'); ?>" role="tab"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo _l('project_notes'); ?></a>
    </li>
    <li>
        <a data-group="project_activity" href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_activity'); ?>" role="tab"><i class="fa fa-exclamation" aria-hidden="true"></i> <?php echo _l('project_activity'); ?></a>
    </li>
    <?php if(has_permission( 'manageProjects')){ ?>
    <li>
        <a data-group="project_invoices" href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_invoices'); ?>" role="tab"><i class="fa fa-sun-o" aria-hidden="true"></i> <?php echo _l('project_invoices'); ?></a>
    </li>
    <?php } ?>
</ul>
