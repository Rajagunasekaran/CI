<?php if(isset($client)){ ?>
<?php render_datatable(array(
    _l('project_name'),
    _l('project_customer'),
    _l('project_start_date'),
    _l('project_deadline'),
    _l('project_status'),
    _l('project_billing_type'),
    _l('project_datecreated'),
    _l('options')
    ),'projects-single-client'); ?>
    <?php } ?>
