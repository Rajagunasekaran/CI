<?php if(has_permission('manageProjects')){ ?>
 <a href="#" class="btn btn-info" onclick="new_milestone();return false;"><?php echo _l('new_milestone'); ?></a>
<?php } ?>
 <?php render_datatable(array(
    _l('milestone_name'),
    _l('milestone_due_date'),
    _l('options')
    ),'milestones'); ?>
