<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
         <div class="col-md-9">
            <div class="row">
               <div class="col-md-4">
                  <div class="panel_s">
                     <div class="panel-body">
                        <div class="row home-summary">
                         <?php if(has_permission('manageSales')){ ?>
                         <div class="col-md-12">
                           <p class="text-muted mleft5"><?php echo _l('home_invoice_overview'); ?>
                              <br /><a href="#"  class="mtop3 home-weekly-payment-sc"><?php echo _l('home_stats_see_weekly_payments'); ?></a>
                           </p>
                           <hr />
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblinvoices',array('status'=>4)); ?>  </h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=4'); ?>">
                              <span class="bold text-danger mbot15 pull-left"><i class="fa fa-clock-o"></i> <?php echo _l('home_invoice_overdue'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblinvoices',array('sent'=>0,'status !='=>2)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('invoices/list_invoices?custom_view=not_sent'); ?>">
                              <span class="bold text-muted inline-block mbot15 pull-left"><i class="fa fa-envelope"></i> <?php echo _l('home_invoice_not_sent'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblinvoices',array('status'=>3)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=3'); ?>">
                              <span class="bold text-info mbot15 pull-left"><i class="fa fa-balance-scale"></i> <?php echo _l('home_invoice_partialy_paid'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblinvoices',array('status'=>2)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>">
                              <span class="bold text-success mbot15 pull-left"><i class="fa fa-check"></i> <?php echo _l('home_invoice_paid'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblinvoicepaymentrecords',array('DATE(date)'=>date('Y-m-d'))); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('payments?custom_view=today'); ?>">
                              <span class="bold text-success mbot15 pull-left"><i class="fa fa-calendar-check-o"></i> <?php echo _l('home_payments_received_today'); ?></span>
                           </a>
                        </div>
                        <div class="clearfix">  </div>
                        <hr class="home-summary-separator"/>
                        <div class="col-md-12">
                           <p class="text-muted mleft5"><?php echo _l('home_estimate_overview'); ?></p>
                           <hr />
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblestimates',array('status'=>5)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('estimates/list_estimates?status=5'); ?>">
                              <span class="bold text-warning mbot15 pull-left"><i class="fa fa-clock-o"></i> <?php echo _l('home_expired_estimates'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblestimates',array('status'=>3)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('estimates/list_estimates?status=3'); ?>">
                              <span class="bold mbot15 pull-left text-danger"><i class="fa fa-times"></i> <?php echo _l('home_estimate_declined'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblestimates',array('status'=>2)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('estimates/list_estimates?status=2'); ?>">
                              <span class="bold text-info mbot15 pull-left"><i class="fa fa-envelope-o"></i> <?php echo _l('home_estimate_sent'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblestimates',array('status'=>4)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('estimates/list_estimates?status=4'); ?>">
                              <span class="bold text-success mbot15 pull-left"><i class="fa fa-check"></i> <?php echo _l('home_estimate_accepted'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblstafftasks',array('finished'=>0)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('tasks/list_tasks?custom_view=unfinished'); ?>">
                              <span class="bold text-warning mbot15 pull-left"><i class="fa fa-times"></i> <?php echo _l('home_unfinished_tasks'); ?></span>
                           </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="home-summary-separator"/>
                        <div class="col-md-12">
                           <p class="text-muted mleft5"><?php echo _l('home_proposal_overview'); ?></p>
                           <hr />
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblproposals',array('status'=>1)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('proposals/list_proposals?custom_view=1'); ?>">
                              <span class="bold text-default mbot15 pull-left"><i class="fa fa-calendar"></i> <?php echo _l('proposal_status_open'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblproposals',array('status'=>2)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('proposals/list_proposals?custom_view=2'); ?>">
                              <span class="bold text-warning mbot15 pull-left"><i class="fa fa-remove"></i> <?php echo _l('proposal_status_declined'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblproposals',array('status'=>3)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('proposals/list_proposals?custom_view=3'); ?>">
                              <span class="bold text-success mbot15 pull-left"><i class="fa fa-check"></i> <?php echo _l('proposal_status_accepted'); ?></span>
                           </a>
                        </div>
                        <div class="col-md-2 col-xs-12 col-total-home-stats text-center">
                           <h3 class="bold no-margin"><?php echo total_rows('tblproposals',array('status'=>4)); ?></h3>
                        </div>
                        <div class="col-md-10">
                           <a href="<?php echo admin_url('proposals/list_proposals?custom_view=4'); ?>">
                              <span class="bold text-info mbot15 pull-left"><i class="fa fa-envelope"></i> <?php echo _l('proposal_status_sent'); ?></span>
                           </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="home-summary-separator"/>
                        <?php } ?>
                        <div class="col-md-12">
                           <p class="text-muted mleft5"><?php echo _l('home_lead_overview'); ?></p>
                           <hr />
                        </div>
                        <div class="col-md-12">
                           <canvas class="chart" id="leads_status_stats"></canvas>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-8 right-height-fix-col">
               <div class="panel_s">
                  <div class="panel-body home-activity">
                     <ul class="nav nav-tabs" role="tablist">
                        <?php if(is_admin()){ ?>
                        <li role="presentation" class="active">
                           <a href="#home_tab_activity" aria-controls="home_tab_activity" role="tab" data-toggle="tab">
                              <?php echo _l('home_latest_activity'); ?>
                           </a>
                        </li>
                        <?php } ?>
                        <li role="presentation" class="<?php if(!is_admin()){ echo 'active'; } ?>">
                           <a href="#home_tab_tasks" aria-controls="home_tab_tasks" role="tab" data-toggle="tab">
                              <?php echo _l('home_my_tasks'); ?>
                           </a>
                        </li>
                        <li role="presentation">
                           <a href="#home_my_projects" aria-controls="home_my_projects" role="tab" data-toggle="tab">
                              <?php echo _l('home_my_projects'); ?>

                           </a>
                        </li>
                        <li role="presentation">
                           <a href="#home_tab_tickets" aria-controls="home_tab_tickets" role="tab" data-toggle="tab">
                              <?php echo _l('home_tickets'); ?>
                           </a>
                        </li>
                        <li role="presentation">
                           <a href="#home_announcements" aria-controls="home_announcements" role="tab" data-toggle="tab">
                              <?php echo _l('home_announcements'); ?>
                              <?php
                              $total_staff_announcements = count($staff_announcements);
                              $total_dismissed_announcements = 0;
                              foreach($staff_announcements as $announcement){
                                if(total_rows('tbldismissedannouncements',array('announcementid'=>$announcement['announcementid'],'userid'=>get_staff_user_id(),'staff'=>1)) > 0){
                                 $total_dismissed_announcements++;
                              }
                           }
                           if($total_dismissed_announcements != $total_staff_announcements){
                              $total_left = $total_staff_announcements - $total_dismissed_announcements;
                              echo '<span class="badge">'.$total_left.'</span>';
                           }
                           ?>

                        </a>
                     </li>
                  </ul>
                  <div class="tab-content home-activity-wrap">
                     <?php if(is_admin()){ ?>
                     <div role="tabpanel" class="tab-pane active" id="home_tab_activity">
                        <a href="<?php echo admin_url('utilities/activity_log'); ?>" class="btn btn-info btn-sm"><?php echo _l('home_widget_view_all'); ?></a>
                        <div class="clearfix"></div>
                        <hr />
                        <ul class="latest-activity">
                           <?php
                           $this->db->limit(50);
                           $this->db->order_by('date','desc');
                           $activity_log = $this->db->get('tblactivitylog')->result_array();
                           foreach($activity_log as $log){   ?>
                           <div class="media">
                              <?php if($log['staffid'] != 0){ ?>
                              <div class="media-left">
                                 <a href="<?php echo admin_url('profile/'.$log["staffid"]); ?>">
                                    <?php echo staff_profile_image($log['staffid'],array('staff-profile-image-small','media-object'),'small',array('data-toggle'=>'tooltip','data-title'=>get_staff_full_name($log['staffid']),'data-placement'=>'right')); ?>&nbsp;
                                 </a>
                              </div>
                              <?php } ?>
                              <div class="media-body text-purple">
                                 <?php echo $log['description']; ?><small class="text-muted display-block"><?php echo _dt($log['date']); ?></small>
                                 <hr />
                              </div>
                           </div>
                           <?php } ?>
                        </ul>
                     </div>
                     <?php } ?>
                     <div role="tabpanel" class="tab-pane <?php if(!is_admin()){ echo 'active'; } ?>" id="home_tab_tasks">
                        <a href="<?php echo admin_url('tasks/list_tasks'); ?>" class="btn btn-info btn-sm"><?php echo _l('home_widget_view_all'); ?></a>
                        <div class="clearfix"></div>
                        <hr />
                        <?php
                        $this->db->where('(id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . ') OR id IN (SELECT taskid FROM tblstafftasksfollowers WHERE staffid = ' . get_staff_user_id() . ') OR addedfrom=' . get_staff_user_id() . ')');
                        $this->db->where('finished',0);
                        $this->db->limit(15);

                        $tasks = $this->db->get('tblstafftasks')->result_array();
                        if(count($tasks) == 0){
                         echo '<p class="bold text-success text-center">'. _l('no_tasks_found') . '</p>';
                      }
                      foreach($tasks as $task){ ?>
                      <div class="widget-task" data-widget-task-id="<?php echo $task['id']; ?>">
                        <div class="row">
                           <div class="col-md-12">
                              <a href="#" class="pull-left" onclick="mark_complete(<?php echo $task['id']; ?>); return false;" data-placement="right" data-toggle="tooltip" title="<?php echo _l('task_single_mark_as_complete'); ?>"><i class="fa fa-check task-icon task-unfinished-icon"></i></a>
                              <a href="#" onclick="init_task_modal(<?php echo $task['id']; ?>); return false;"><?php echo $task['name']; ?></a>
                              <div class="clearfix mtop10"></div>
                              <?php echo substr(strip_tags($task['description']),0,150) . '...' ?>
                           </div>
                           <div class="col-md-12 mtop10">
                              <span class="label <?php if(total_rows('tbltaskchecklists',array('finished'=>0,'taskid'=>$task['id'])) == 0){echo 'label-default-light';}else{echo 'label-danger';} ?> pull-left mright5">
                                 <i class="fa fa-th-list"></i> <?php echo total_rows('tbltaskchecklists',array('finished'=>0,'taskid'=>$task['id'])); ?>
                              </span>
                              <span class="label label-default-light pull-left mright5">
                                 <i class="fa fa-paperclip"></i> <?php echo total_rows('tblstafftasksattachments',array('taskid'=>$task['id'])); ?>
                              </span>
                              <span class="label label-default-light pull-left">
                                 <i class="fa fa-comments"></i> <?php echo total_rows('tblstafftaskcomments',array('taskid'=>$task['id'])); ?>
                              </span>
                           </div>
                        </div>
                        <hr />
                     </div>
                     <?php } ?>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="home_tab_tickets">
                     <?php echo AdminTicketsTableStructure('tickets-table-home'); ?>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="home_my_projects">
                    <a href="<?php echo admin_url('projects'); ?>" class="btn btn-info btn-sm"><?php echo _l('home_widget_view_all'); ?></a>
                    <div class="clearfix"></div>
                    <div class="table-responsive">
                     <?php render_datatable(array(
                        _l('project_name'),
                        _l('project_start_date'),
                        _l('project_deadline'),
                        _l('project_status'),
                        ),'staff-projects'); ?>
                     </div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="home_announcements">
                     <?php if(is_admin()){ ?>
                     <a href="<?php echo admin_url('announcements'); ?>" class="btn btn-info btn-sm"><?php echo _l('home_widget_view_all'); ?></a>
                     <div class="clearfix"></div>
                     <?php } ?>
                     <div class="table-responsive">
                        <?php render_datatable(array(_l('name'),_l('options')),'announcements'); ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="panel_s">
            <div class="panel-body">
               <div class="row">
                  <div class="row">
                     <div class="col-md-6">
                        <p class="text-muted text-center"><?php echo _l('home_tickets_awaiting_reply_by_department'); ?></p>
                        <canvas class="chart" id="tickets-awaiting-reply-by-department"></canvas>
                     </div>
                     <div class="col-md-6">
                        <p class="text-muted text-center"> <?php echo _l('home_tickets_awaiting_reply_by_status'); ?></p>
                        <canvas class="chart" id="tickets-awaiting-reply-by-status"></canvas>
                     </div>
                  </div>
               </div>
               <hr />
               <div class="row">
                  <div class="col-md-12">
                     <p class="text-muted text-center mbot5"><?php echo _l('home_stats_by_project_status'); ?></p>
                  </div>
                  <div class="col-md-8 col-md-offset-2">
                     <canvas class="chart" id="projects_status_stats"></canvas>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="row right-height-fix-col">
      <div class="col-md-12">
         <div class="row">
            <div class="col-md-12">
               <div class="panel_s">
                  <div class="panel-body">
                     <div class="dt-loader hide"></div>
                     <div id="calendar"></div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php if(has_permission('manageSales')){ ?>
   <div class="row" id="weekly_payments">
      <div class="col-md-12 right-height-fix-col">
         <div class="panel_s">
            <div class="panel-body">
               <div class="col-md-12">
                  <p class="text-muted pull-left"><?php echo _l('home_weekly_payment_records'); ?></p>
                  <?php if(has_permission('watchReports')){ ?>
                  <a href="<?php echo admin_url('reports/sales'); ?>" class="pull-right"><?php echo _l('home_stats_full_report'); ?></a>
                  <div class="clearfix"></div>
                  <?php } ?>
                  <div class="clearfix"></div>
                  <?php if (is_using_multiple_currencies()) { ?>
                  <select class="selectpicker pull-left mbot15" name="currency">
                     <?php foreach($currencies as $currency){
                        $selected = '';
                        if($currency['isdefault'] == 1){
                         $selected = 'selected';
                      }
                      ?>
                      <option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?> data-subtext="<?php echo $currency['name']; ?>"><?php echo $currency['symbol']; ?></option>
                      <?php } ?>
                   </select>
                   <?php } ?>
                   <canvas class="chart" id="weekly-payment-statistics" style="height:300px;"></canvas>
                </div>
             </div>
          </div>
       </div>
    </div>
    <?php } ?>
 </div>
 <div class="col-md-3">
   <?php
   if(count($upcoming_events) > 0){ ?>
   <div class="row">
      <div class="col-md-12">
         <div class="panel_s events animated fadeIn ">
            <div class="panel-heading-bg">
               <?php echo _l('home_this_week_events'); ?>
            </div>
            <div class="panel-body">
               <?php foreach($upcoming_events as $event){ ?>
               <p class="event">
                  <span class="label label-primary pull-left"><i class="fa fa-calendar"></i> <?php echo _d($event['start']); ?></span>
                  <?php if($event['public'] == 1) { ?>
                  <span class="label label-warning pull-right"><?php echo _l('home_public_event'); ?></span>
                  <?php } ?>
                  <span class="event-title bold"><?php echo $event['title']; ?></span>
                  <?php if($event['userid'] != get_staff_user_id()){ ?>
                  <small class="display-block valign"><?php echo _l('home_event_added_by'); ?> <a href="<?php echo admin_url('profile/'.$event['userid']); ?>"><?php echo staff_profile_image($event['userid'],array('staff-profile-xs-image')); ?> <?php echo get_staff_full_name($event['userid']); ?></a></small>
                  <?php } ?>
               </p>
               <?php } ?>
            </div>
            <div class="panel-footer">
               <?php echo _l('home_upcoming_events_next_week'); ?> : <?php echo $upcoming_events_next_week; ?>
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
   <div class="panel_s todo-panel">
      <div class="panel-body">
         <p class="text-muted pull-left">
            <?php echo _l('home_my_todo_items'); ?>
         </p>
         <a href="<?php echo admin_url('todo'); ?>" class="btn btn-info btn-sm pull-right mbot20"><?php echo _l('home_widget_view_all'); ?></a>
         <div class="clearfix"></div>
         <div class="todo-body">
            <?php $total_todos = count($todos); ?>
            <h4 class="todo-title text-warning"><i class="fa fa-warning"></i> <?php echo _l('home_latest_todos'); ?></h4>
            <ul class="list-unstyled todo unfinished-todos todos-sortable sortable">
               <?php foreach($todos as $todo) { ?>
               <li>
                  <?php echo form_hidden('todo_order',$todo['item_order']); ?>
                  <?php echo form_hidden('finished',0); ?>
                  <div class="checkbox checkbox-default todo-checkbox">
                     <input type="checkbox" name="todo_id" value="<?php echo $todo['todoid']; ?>">
                     <label></label>
                  </div>
                  <span class="todo-description"><?php echo $todo['description']; ?><a href="#" onclick="delete_todo_item(this,<?php echo $todo['todoid']; ?>)" class="pull-right todo-delete"><i class="fa fa-remove"></i></a></span>
                  <small class="todo-date"><?php echo $todo['dateadded']; ?></small>
               </li>
               <?php } ?>
               <li class="padding no-todos ui-state-disabled <?php if($total_todos > 0){echo 'hide';} ?>"><?php echo _l('home_no_latest_todos'); ?></li>
            </ul>
            <?php $total_finished_todos = count($todos_finished); ?>
            <h4 class="todo-title text-success"><i class="fa fa-check"></i> <?php echo _l('home_latest_finished_todos'); ?></h4>
            <ul class="list-unstyled todo finished-todos todos-sortable sortable" >
               <?php foreach($todos_finished as $todo_finished){ ?>
               <li>
                  <?php echo form_hidden('todo_order',$todo_finished['item_order']); ?>
                  <?php echo form_hidden('finished',1); ?>
                  <div class="checkbox checkbox-default todo-checkbox">
                     <input type="checkbox" value="<?php echo $todo_finished['todoid']; ?>" name="todo_id" checked>
                     <label></label>
                  </div>
                  <span class="todo-description todo-finished"><?php echo $todo_finished['description']; ?><a href="#" onclick="delete_todo_item(this,<?php echo $todo_finished['todoid']; ?>)" class="pull-right todo-delete"><i class="fa fa-remove"></i></a></span>
                  <small class="todo-date todo-date-finished"><?php echo $todo_finished['datefinished']; ?></small>
               </li>
               <?php } ?>
               <li class="padding no-todos ui-state-disabled <?php if($total_finished_todos > 0){echo 'hide';} ?>"><?php echo _l('home_no_finished_todos_found'); ?></li>
            </ul>
         </div>
      </div>
   </div>
   <div class="panel_s projects-activity">
      <div class="panel-body">
        <p class="text-muted"><?php echo _l('home_project_activity'); ?></p>
        <hr />
        <?php
        if(count($projects_activity) == 0){ ?>
        <p><?php echo _l('home_project_activity_not_found'); ?></p>
        <?php }
        foreach($projects_activity as $activity){
           ?>
           <div class="display-block project-activity">
             <?php if($activity['staff_id'] != 0){
                $fullname = get_staff_full_name($activity['staff_id']) . ' - ';
                ?>
                <a href="<?php echo admin_url('profile/'.$activity['staff_id']); ?>"><?php echo staff_profile_image($activity['staff_id'],array('staff-profile-image-small','pull-left mright10')); ?></a>
                <?php } else if($activity['client_id'] != 0){
                  $fullname = '<span class="label label-info inline-block mbot5">'._l('is_customer_indicator') . '</span> ' . get_client_full_name($activity['client_id']) . ' - ';
                  ?>
                  <a href="<?php echo admin_url('clients/client/'.$activity['client_id']); ?>">
                    <img src="<?php echo client_profile_image_url($activity['client_id']); ?>" class="client-profile-image-small pull-left mright10">
                 </a>
                 <?php } else {$fullname = '[CRON] ';} ?>
                 <div class="media-body">
                   <div class="display-block">
                      <h5 class="bold no-margin">
                         <?php echo $fullname . '<span class="text-info">'.$activity['description'].'</span>'; ?>
                      </h5>
                       <div class="mtop10">
                          <?php echo _l('project_name'); ?>: <a href="<?php echo admin_url('projects/view/'.$activity['project_id']); ?>" class="label label-success inline-block"><?php echo $activity['project_name']; ?></a>
                       </div>
                      <p class="text-muted"><?php echo $activity['additional_data']; ?></p>
                   </div>
                   <small class="text-muted"><?php echo _dt($activity['dateadded']); ?></small>
                </div>
                <hr />
             </div>
             <?php } ?>
          </div>
       </div>

    </div>
 </div>
</div>
</div>
<script>
   google_api = '<?php echo $google_api_key; ?>';
   calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';
</script>
<?php init_tail(); ?>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<script>
   var weekly_payments_statistics;
   $(window).bind("resize", function() {
     window.resizeEvt;
     $(window).resize(function()
     {
       clearTimeout(window.resizeEvt);
       window.resizeEvt = setTimeout(function()
       {
        fix_dashboard_height();
     }, 250);
    })
  });
   $(document).ready(function() {
      fix_dashboard_height();
        $('.home-weekly-payment-sc').on('click',function(e){
          e.preventDefault();
          $('body,html').animate({
            scrollTop: $('#weekly_payments').offset().top
         });
        });
         // Tickets awaiting reply by department chart
         var ctx_tickets_departments = $('#tickets-awaiting-reply-by-department').get(0).getContext('2d');
         var tickets_dep_chart = new Chart(ctx_tickets_departments).Doughnut(<?php echo $tickets_awaiting_reply_by_department; ?>);
         // Tickets awaiting reply by department chart
         var ctx_tickets_status = $('#tickets-awaiting-reply-by-status').get(0).getContext('2d');
         new Chart(ctx_tickets_status).Doughnut(<?php echo $tickets_reply_by_status; ?>);
          // Projects statuses
          var ctx_projects_status = $('#projects_status_stats').get(0).getContext('2d');
          new Chart(ctx_projects_status).Doughnut(<?php echo $projects_status_stats; ?>);
          // Leads overview status
          var ctx_leads_status_stats = $('#leads_status_stats').get(0).getContext('2d');
          new Chart(ctx_leads_status_stats).Doughnut(<?php echo $leads_status_stats; ?>);
         // Payments statistics
         init_weekly_payment_statistics();
         $('select[name="currency"]').on('change', function() {
          init_weekly_payment_statistics();
       });
      });

   function init_weekly_payment_statistics() {
    if ($('#weekly-payment-statistics').length > 0) {
      if (typeof(weekly_payments_statistics) !== 'undefined') {
        weekly_payments_statistics.destroy();
     }
     var currency = $('select[name="currency"]').val();
     $.get(admin_url + 'home/weekly_payments_statistics/' + currency, function(response) {
        var ctx = $('#weekly-payment-statistics').get(0).getContext('2d');
        weekly_payments_statistics = new Chart(ctx).Line(response, line_chart_options);
     }, 'json');
  }
}
function fix_dashboard_height(){
    var todo_height = $('.todo-panel').outerHeight(true);
    var events_height = $('.events').outerHeight(true);
    var minus = (todo_height + events_height);
      var total_center_height = 0;
      var center_fix_columns = $('.right-height-fix-col .panel_s');
      $.each(center_fix_columns,function(){
         total_center_height += $(this).outerHeight(true);
      });
      $('.projects-activity').height(((total_center_height - minus) - 15)+'px');
}

</script>
</body>
</html>
