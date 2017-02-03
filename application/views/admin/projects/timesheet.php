<!-- Miles Stones -->
<div class="modal fade" id="timesheet" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('projects/timesheet'),array('id'=>'timesheet_form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="add-title"><?php echo _l('record_timesheet'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo form_hidden('project_id',$project->id); ?>
                        <?php echo form_hidden('timer_id'); ?>
                        <div id="additional"></div>
                        <div class="row">
                            <div class="col-md-2">
                                <span class="bold">Start Time</span>
                            </div>
                            <div class="col-md-2">
                             <div class="form-group">
                                 <select name="start_day" id="start_day" class="selectpicker" data-width="100%">
                                    <?php for($i = 1;$i<=31;$i++){
                                        $selected = '';
                                        if(date('d') == $i){
                                            $selected = ' selected';
                                        }
                                        echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select name="start_month" id="start_month" class="selectpicker" data-width="100%">
                                    <?php for ($m = 1; $m <= 12; $m++) {
                                        $selected = '';
                                        if(date('m') == $m){
                                            $selected = ' selected';
                                        }
                                        echo '<option value="'.$m.'"'.$selected.'>'._l(date('F', mktime(0, 0, 0, $m, 1))).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select name="start_year" id="start_year" class="selectpicker" data-width="100%">
                                    <?php for ($y = (date('Y') - 5); $y <= date('Y'); $y++) {
                                        $selected = '';
                                        if(date('Y') == $y){
                                            $selected = ' selected';
                                        }
                                        echo '<option value="'.$y.'"'.$selected.'>'.$y.'</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                           <span class="pull-left timesheet-time-sep"> - </span>
                           <div class="form-group">
                            <select name="start_hour" id="start_hour" class="selectpicker pull-left" data-width="90%">
                               <?php for ($t = 0; $t <= 23; $t++) {
                                 $hour = sprintf("%02d", $t);
                                 $selected = '';
                                 if(date('H') == $t){
                                    $selected = ' selected';
                                }
                                echo '<option value="'.$t.'"'.$selected.'>'.$hour.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                  <span class="pull-left timesheet-time-sep"> : </span>
                  <div class="form-group">
                    <select name="start_minutes" id="start_minutes" class="selectpicker pull-left" data-width="90%">
                        <?php for ($t = 0; $t < 60; $t++) {
                            $hour = sprintf("%02d", $t);
                            $selected = '';
                            if(date('i') == $t){
                                $selected = ' selected';
                            }
                            echo '<option value="'.$t.'"'.$selected.'>'.$hour.'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <!-- End Time -->
        <div class="row">
            <div class="col-md-2">
             <span class="bold">End Time</span>
         </div>
         <div class="col-md-2">
             <div class="form-group">
                <select name="end_day" id="end_day" class="selectpicker" data-width="100%">
                    <?php for($i = 1;$i<=31;$i++){
                        $selected = '';
                        if(date('d') == $i){
                            $selected = ' selected';
                        }
                        echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <select name="end_month" id="end_month" class="selectpicker" data-width="100%">
                    <?php for ($m = 1; $m <= 12; $m++) {
                        $selected = '';
                        if(date('m') == $m){
                            $selected = ' selected';
                        }
                        echo '<option value="'.$m.'"'.$selected.'>'._l(date('F', mktime(0, 0, 0, $m, 1))).'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <select name="end_year" id="end_year" class="selectpicker" data-width="100%">
                    <?php for ($y = (date('Y') - 5); $y <= date('Y'); $y++) {
                        $selected = '';
                        if(date('Y') == $y){
                            $selected = ' selected';
                        }
                        echo '<option value="'.$y.'"'.$selected.'>'.$y.'</option>';
                    } ?>
                </select>
            </div>
        </div>
        <div class="col-md-2">
           <span class="pull-left timesheet-time-sep"> - </span>
           <div class="form-group">
            <select name="end_hour" id="end_hour" class="selectpicker pull-left" data-width="90%">
               <?php for ($t = 0; $t <= 23; $t++) {
                 $hour = sprintf("%02d", $t);
                 $selected = '';
                 if(date('H') == $t){
                    $selected = ' selected';
                }
                echo '<option value="'.$t.'"'.$selected.'>'.$hour.'</option>';
            }
            ?>
        </select>
    </div>
</div>
<div class="col-md-2">
  <span class="pull-left timesheet-time-sep"> : </span>
  <div class="form-group">
    <select name="end_minutes" id="end_minutes" class="selectpicker pull-left" data-width="90%">
        <?php for ($t = 0; $t < 60; $t++) {
            $hour = sprintf("%02d", $t);
            $selected = '';

                if(date('i') == $t){
                    $selected = ' selected';
                }


            echo '<option value="'.$t.'"'.$selected.'>'.$hour.'</option>';
        }
        ?>
    </select>
</div>
</div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-2">
                <label for="timesheet_task_id"><?php echo _l('project_timesheet_task'); ?></label>
            </div>
            <div class="col-md-10">
              <select name="timesheet_task_id" id="timesheet_task_id" class="selectpicker" data-live-search="true" data-width="100%">
                <option value=""></option>
                <?php foreach($tasks as $task){
                    if($task['billed'] == 1){continue;}
                    echo '<option value="'.$task['id'].'">'.$task['name'].'</option>';
                }
                ?>
            </select>
        </div>
    </div>
</div>
</div>
<div class="row mtop15">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-2">
                <label for="timesheet_task_id"><?php echo _l('project_timesheet_user'); ?></label>
            </div>
            <div class="col-md-10">
              <select name="timesheet_staff_id" id="timesheet_staff_id" class="selectpicker" data-live-search="true" data-width="100%">
                <option value=""></option>
            </select>
        </div>
    </div>
</div>
</div>

</div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
</div>
</div><!-- /.modal-content -->
<?php echo form_close(); ?>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Mile stones end -->
