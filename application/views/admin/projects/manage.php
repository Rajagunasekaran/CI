<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if(has_permission('manageProjects')){ ?>
                        <a href="<?php echo admin_url('projects/project'); ?>" class="btn btn-info pull-left display-block">
                            <?php echo _l('new_project'); ?>
                        </a>
                        <?php } ?>
                        <div class="btn-group pull-right mleft4" data-toggle="tooltip" title="<?php echo _l('expenses_list_tooltip'); ?>">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-list"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="#" onclick="dt_custom_view('','.table-projects'); return false;">
                                        <?php echo _l('expenses_list_all'); ?>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="dt_custom_view('1','.table-projects'); return false;">
                                        <?php echo _l('project_status_1'); ?> (<?php echo total_rows('tblprojects',array('status'=>1)); ?>)
                                    </a>
                                </li>
                                   <li>
                                    <a href="#" onclick="dt_custom_view('2','.table-projects'); return false;">
                                        <?php echo _l('project_status_2'); ?> (<?php echo total_rows('tblprojects',array('status'=>2)); ?>)
                                    </a>
                                </li>
                                  <li>
                                    <a href="#" onclick="dt_custom_view('3','.table-projects'); return false;">
                                        <?php echo _l('project_status_3'); ?> (<?php echo total_rows('tblprojects',array('status'=>3)); ?>)
                                    </a>
                                </li>
                                  <li>
                                    <a href="#" onclick="dt_custom_view('4','.table-projects'); return false;">
                                        <?php echo _l('project_status_4'); ?> (<?php echo total_rows('tblprojects',array('status'=>4)); ?>)
                                    </a>
                                </li>
                                <li class="divider"></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php echo form_hidden('custom_view'); ?>
                        <?php render_datatable(array(
                            _l('project_name'),
                            _l('project_customer'),
                            _l('project_start_date'),
                            _l('project_deadline'),
                            _l('project_status'),
                            _l('project_billing_type'),
                            _l('project_datecreated'),
                            _l('options')
                            ),'projects'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
</body>
</html>
