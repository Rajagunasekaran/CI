                 <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-list" data-toggle="tooltip" data-title="<?php echo _l('tasks_filter_by'); ?>"></i>
                </button>
                <ul class="dropdown-menu width220">
                    <li>
                        <a href="#" onclick="dt_custom_view('','<?php echo $view_table_name; ?>'); return false;">
                            <?php echo _l('task_list_all'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="dt_custom_view('my_tasks','<?php echo $view_table_name; ?>'); return false;">
                            <?php echo _l('tasks_view_assigned_to_user'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="dt_custom_view('my_following_tasks','<?php echo $view_table_name; ?>'); return false;">
                            <?php echo _l('tasks_view_follower_by_user'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="dt_custom_view('finished','<?php echo $view_table_name; ?>'); return false;">
                            <?php echo _l('task_list_finished'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="dt_custom_view('unfinished','<?php echo $view_table_name; ?>'); return false;">
                            <?php echo _l('task_list_unfinished'); ?>
                        </a>
                    </li>
                    <?php if(is_admin() || has_permission('manageProjects')){ ?>
                    <li>
                        <a href="#" onclick="dt_custom_view('billable','<?php echo $view_table_name; ?>'); return false;">
                            <?php echo _l('task_billable'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="dt_custom_view('billed','<?php echo $view_table_name; ?>'); return false;">
                            <?php echo _l('task_billed'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="dt_custom_view('not_billed','<?php echo $view_table_name; ?>'); return false;">
                            <?php echo _l('task_billed_no'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="dt_custom_view('not_assigned','<?php echo $view_table_name; ?>'); return false;">
                            <?php echo _l('task_list_not_assigned'); ?>
                        </a>
                    </li>
                    <?php } ?>
                    <li>
                        <a href="#" onclick="dt_custom_view('due_date_passed','<?php echo $view_table_name; ?>'); return false;">
                            <?php echo _l('task_list_duedate_passed'); ?>
                        </a>
                    </li>
                </ul>
