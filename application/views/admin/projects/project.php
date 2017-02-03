<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <?php echo form_open($this->uri->uri_string()); ?>
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?php echo $title; ?>
                    </div>
                    <div class="panel-body">
                        <?php
                        $disable_type_edit = '';
                        if(isset($project)){
                            if($project->billing_type != 1){
                                if(total_rows('tblstafftasks',array('rel_id'=>$project->id,'rel_type'=>'project','billable'=>1,'billed'=>1)) > 0){
                                    $disable_type_edit = 'disabled';
                                }
                            }
                        }
                        ?>
                        <?php $value = (isset($project) ? $project->name : ''); ?>
                        <?php echo render_input('name','project_name',$value); ?>

                        <?php $selected = (isset($project) ? $project->clientid : ''); ?>
                        <?php echo render_select('clientid',$customers,array('userid',array('company')),'project_customer',$selected); ?>
                        <div class="row">
                            <div class="col-md-6">
                               <div class="form-group">
                                <label for="billing_type"><?php echo _l('project_billing_type'); ?></label>
                                <div class="clearfix"></div>
                                <select name="billing_type" class="selectpicker" id="billing_type" data-width="100%" <?php echo $disable_type_edit ; ?>>
                                    <option value=""></option>
                                    <option value="1" <?php if(isset($project) && $project->billing_type == 1){echo 'selected'; } ?>><?php echo _l('project_billing_type_fixed_cost'); ?></option>
                                    <option value="2" <?php if(isset($project) && $project->billing_type == 2){echo 'selected'; } ?>><?php echo _l('project_billing_type_project_hours'); ?></option>
                                    <option value="3" data-subtext="<?php echo _l('project_billing_type_project_task_hours_hourly_rate'); ?>" <?php if(isset($project) && $project->billing_type == 3){echo 'selected'; } ?>><?php echo _l('project_billing_type_project_task_hours'); ?></option>
                                </select>
                                <?php if($disable_type_edit != ''){
                                    echo '<p class="text-danger">'._l('cant_change_billing_type_billed_tasks_found').'</p>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status"><?php echo _l('project_status'); ?></label>
                                <div class="clearfix"></div>
                                <select name="status" id="status" class="selectpicker" data-width="100%">
                                    <option value="1" <?php if(!isset($project) || (isset($project) && $project->status == 1)){echo 'selected';} ?>><?php echo _l('project_status_1'); ?></option>
                                    <option value="2" <?php if(isset($project) && $project->status == 2){echo 'selected';} ?>><?php echo _l('project_status_2'); ?></option>
                                    <option value="3" <?php if(isset($project) && $project->status == 3){echo 'selected';} ?>><?php echo _l('project_status_3'); ?></option>
                                    <option value="4" <?php if(isset($project) && $project->status == 4){echo 'selected';} ?>><?php echo _l('project_status_4'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php if(isset($project)){ ?>
                    <div class="form-group mark_all_tasks_as_completed hide">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="mark_all_tasks_as_completed">
                            <label for="mark_all_tasks_as_completed"><?php echo _l('project_mark_all_tasks_as_completed'); ?></label>
                        </div>
                    </div>
                    <?php } ?>
                    <div id="project_cost" class="<?php if(!isset($project) || (isset($project) && $project->billing_type != 1)){echo 'hide';} ?>">
                        <?php $value = (isset($project) ? $project->project_cost : ''); ?>
                        <?php echo render_input('project_cost','project_total_cost',$value); ?>
                    </div>
                    <div id="project_rate_per_hour" class="<?php if(!isset($project) || (isset($project) && $project->billing_type != 2)){echo 'hide';} ?>">
                        <?php $value = (isset($project) ? $project->project_rate_per_hour : ''); ?>
                        <?php
                        $input_disable = array();
                        if($disable_type_edit != ''){
                            $input_disable['disabled'] = true;
                        }
                        ?>
                        <?php echo render_input('project_rate_per_hour','project_rate_per_hour',$value,'number',$input_disable); ?>
                    </div>
                    <?php
                    $selected = array();
                    if(isset($project_members)){
                        foreach($project_members as $member){
                            array_push($selected,$member['staff_id']);
                        }
                    }
                    echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true));
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                           <?php $value = (isset($project) ? $project->start_date : ''); ?>
                           <?php echo render_date_input('start_date','project_start_date',$value); ?>
                       </div>
                       <div class="col-md-6">
                        <?php $value = (isset($project) ? $project->deadline : ''); ?>
                        <?php echo render_date_input('deadline','project_deadline',$value); ?>
                    </div>
                </div>
                <p class="bold"><?php echo _l('project_description'); ?></p>
                <?php $contents = ''; if(isset($project)){$contents = $project->description;} ?>
                <?php $this->load->view('admin/editor/template',array('name'=>'description','contents'=>$contents)); ?>
                <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
            </div>
        </div>
    </div>
    <div class="col-md-5">
     <div class="panel_s">
        <div class="panel-heading">
            <?php echo _l('project_settings'); ?>
        </div>
        <div class="panel-body">
            <?php foreach($settings as $setting){ ?>
            <div class="checkbox checkbox-primary">
                <?php
                $checked = ' checked';
                if(isset($project)){
                    if($project->settings->{$setting} == 0){
                        $checked = '';
                    }
                } else {
                    foreach($last_project_settings as $_l_setting) {
                        if($setting == $_l_setting['name']){
                            if($_l_setting['value'] == 0){
                                $checked = '';
                            }
                        }
                    }
                } ?>
                <input type="checkbox" name="settings[<?php echo $setting; ?>]" <?php echo $checked; ?>>
                <label for=""><?php echo _l('project_allow_client_to',_l('project_setting_'.$setting)); ?></label>
            </div>
            <hr class="no-margin" />
            <?php } ?>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function(){
        $('select[name="billing_type"]').on('change',function(){
            var type = $(this).val();
            if(type == 1){
                $('#project_cost').removeClass('hide');
                $('#project_rate_per_hour').addClass('hide');
            } else if(type == 2){
                $('#project_cost').addClass('hide');
                $('#project_rate_per_hour').removeClass('hide');
            } else {
                $('#project_cost').addClass('hide');
                $('#project_rate_per_hour').addClass('hide');
            }
        });
        _validate_form($('form'),{name:'required',clientid:'required',start_date:'required',deadline:'required',billing_type:'required'});
        $('select[name="status"]').on('change',function(){
            var status = $(this).val();
            if(status == 4){
                $('.mark_all_tasks_as_completed').removeClass('hide');
            } else {
                $('.mark_all_tasks_as_completed').addClass('hide');
                $('.mark_all_tasks_as_completed input').prop('checked',false);
            }
        })
        $('form').on('submit',function(){
            $('select[name="billing_type"]').prop('disabled',false);
            $('input[name="project_rate_per_hour"]').prop('disabled',false);
        })
    });
</script>
</body>
</html>
