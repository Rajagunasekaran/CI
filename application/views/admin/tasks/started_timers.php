<?php
if(count($timers)==0){
    echo '<li class="text-center">'._l('no_timers_found').'</li>';
}
foreach($timers as $timer){
    $task = $this->tasks_model->get($timer['task_id']);
    $data = '';
    if($task){
        $data .= '<li class="timer"><a href="#" class="_timer" onclick="init_task_modal('.$timer['task_id'].');return false;">'.$task->name.'</a>';
        $data .= _l('timer_top_started', strftime(get_strftime_format().' %H:%M', $timer['start_time']));
        $data .= '<p class="mtop10"><a href="#" class="label label-danger" onclick="timer_action('.$task->id.','.$this->tasks_model->get_last_timer($task->id)->id.'); return false;"><i class="fa fa-clock-o"></i> '._l('task_stop_timer').'</a></p>';
        $data .= '</li>';
    }
    echo $data;
}
