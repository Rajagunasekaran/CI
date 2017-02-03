<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if(has_permission('manageTasks')){ ?>
                        <a href="#" onclick="new_task(); return false;" class="btn btn-info pull-left new"><?php echo _l('new_task'); ?></a>
                        <?php } ?>
                        <div class="btn-group pull-right">
                            <?php $this->load->view('admin/tasks/tasks_filter_by',array('view_table_name'=>'.table-tasks')); ?>
                        </div>
                    </div>
                </div>
                <div class="panel_s animated fadeIn">
                    <div class="panel-body">
                        <?php echo form_hidden('tasks_sort_by'); ?>
                        <?php echo form_hidden('custom_view',$custom_view); ?>

                        <div class="clearfix"></div>
                        <?php
                        $table_data = array(_l('tasks_dt_name'),_l('tasks_dt_datestart'),_l('task_duedate'),_l('task_relation'),_l('task_billable'),_l('task_billed'),_l('tasks_dt_finished'));
                        $custom_fields = get_custom_fields('tasks',array('show_on_table'=>1));
                        foreach($custom_fields as $field){
                            array_push($table_data,$field['name']);
                        }
                        array_push($table_data, _l('options'));
                        render_datatable($table_data,'tasks'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    taskid = '<?php echo $taskid; ?>';
</script>
<?php init_tail(); ?>
</body>
</html>
