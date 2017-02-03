$(document).ready(function() {
    // Init single task data
    if (typeof(taskid) !== 'undefined' && taskid !== '') {
        init_task_modal(taskid);
    }

    $('body').on('change', 'input[name="checklist-box"]', function() {
        var checked = $(this).prop('checked');
        if (checked == true) {
            val = 1;
        } else {
            val = 0;
        }
        var listid = $(this).parents('.checklist').data('checklist-id');
        $.get(admin_url + 'tasks/checkbox_action/' + listid + '/' + val);
        recalculate_checklist_items_progress();
    });

    // Focus on subject when adding new task
    $('body').on('shown.bs.modal','#_task_modal', function() {
        if (typeof(taskid) == 'undefined' || taskid == '') {
            $('body').find('#_task_modal input[name="name"]').focus();
        }
    });
     // Focus on subject when adding new task
    $('body').on('hidden.bs.modal','.task-modal-single', function() {
        taskid = '';
    });

    // when click close modal task tracking stats fix to do not close both modals
    $('body').on('click','.close-task-stats',function(){
        $('#task-tracking-stats-modal').modal('hide');
    });

    $('body').on('click','.close-edit-comment',function(){
        $('#comment-edit-message').modal('hide');
    });

    // FIx for scroll and both modal close
    $('body').on('hidden.bs.modal','#task-tracking-stats-modal,#_task_modal', function(event) {
        var task_single_open = $('.task-modal-single:visible').length;
        // Fix for scroll
        task_single_open && $('body').addClass('modal-open');
        // Fix for both modal close
       // !task_single_open && $('body').find('.modal-backdrop').eq(0).remove();
    });

    $('body').on('shown.bs.modal','#task-tracking-stats-modal', function(event) {

        var ctx_task_tracking,tracking_chart;
        ctx_task_tracking = $('body').find('#task-tracking-stats-chart').get(0).getContext('2d');

        line_chart_options.tooltipTemplate = function(v){
           return moment.duration(v.value, "minutes").format({template:"hh:mm"},{trim:false}) +' ' + v.datasetLabel;
        }
        var data = task_tracking_stats_data;
         var maxValue = false;
           for (datasetIndex = 0; datasetIndex < data.datasets.length; ++datasetIndex) {
               var setMax = Math.max.apply(null, data.datasets[datasetIndex].data);
               if (maxValue === false || setMax > maxValue) maxValue = setMax;
           }
        var _maxValue = maxValue/60;
        var steps = Math.ceil(_maxValue);
        steps = new Number(steps);

        line_chart_options.scaleOverride = true;
        line_chart_options.scaleSteps = steps;
        line_chart_options.scaleStepWidth = Math.ceil(maxValue / steps)
        line_chart_options.scaleStartValue = 0;
        line_chart_options.scaleLabel = function (valuePayload) {

            var hours = valuePayload.value / 60;
            return Math.ceil(hours)
        }
        tracking_chart = new Chart(ctx_task_tracking).Line(task_tracking_stats_data,line_chart_options);
     });

    $('body').on('blur', 'textarea[name="checklist-description"]', function() {
        var description = $(this).val();
        var listid = $(this).parents('.checklist').data('checklist-id');
        $.post(admin_url + 'tasks/update_checklist_item', {
            description: description,
            listid: listid
        });
    });
    // Assign task to staff member
    $('body').on('change', 'select[name="select-assignees"]', function() {
        $('body').append('<div class="dt-loader"></div>');
        var data = {};
        data.assignee = $('select[name="select-assignees"]').val();
        data.taskid = taskid;
        $.post(admin_url + 'tasks/add_task_assignees', data).success(function(response) {
            response = $.parseJSON(response);
            reload_tasks_tables();
            $('body').find('.dt-loader').remove();
            init_task_modal(taskid);
        });
    });
    // Add follower to task
    $('body').on('change', 'select[name="select-followers"]', function() {
        var data = {};
        data.follower = $('select[name="select-followers"]').val();
        data.taskid = taskid;
        $('body').append('<div class="dt-loader"></div>');
        $.post(admin_url + 'tasks/add_task_followers', data).success(function(response) {
            response = $.parseJSON(response);
            $('body').find('.dt-loader').remove();
            init_task_modal(taskid);
        });
    });
});

function recalculate_checklist_items_progress() {
    var total_finished = $('input[name="checklist-box"]:checked').length;
    var total_checklist_items = $('input[name="checklist-box"]').length;

    var percent = 0;
     if(total_checklist_items == 0){
        // remove the heading for checklist items
        $('body').find('.chk-heading').remove();
        $('.checklist-items-wrapper').addClass('hide');
    } else {
        $('.checklist-items-wrapper').removeClass('hide');
    }
    if (total_checklist_items > 2) {
        percent = (total_finished * 100) / total_checklist_items;
    } else {
        $('.task-progress-bar').parents('.progress').addClass('hide');
        return false;
    }
    $('.task-progress-bar').css('width', percent.toFixed(2) + '%');
    $('.task-progress-bar').text(percent.toFixed(2) + '%');
}

function delete_checklist_item(id, field) {
    $.get(admin_url + 'tasks/delete_checklist_item/' + id, function(response) {
        if (response.success == true) {
            $(field).parents('.checklist').remove();
            recalculate_checklist_items_progress();
        }
    }, 'json');
}

function add_task_checklist_item() {
    $.post(admin_url + 'tasks/add_checklist_item', {
        taskid: taskid
    }).success(function() {
        init_tasks_checklist_items(true);
    });
}
function init_tasks_checklist_items(is_new) {
    $.post(admin_url + 'tasks/init_checklist_items', {
        taskid: taskid
    }).success(function(data) {
        $('#checklist-items').html(data);
        if (typeof(is_new) != 'undefined') {
            $('body').find('.checklist textarea').eq(0).focus();
        }
        update_checklist_order();
        var total_checklist_items = $('body').find('input[name="checklist-box"]').length;
         if(total_checklist_items == 0){
            $('.checklist-items-wrapper').addClass('hide');
        } else {
            $('.checklist-items-wrapper').removeClass('hide');
        }
    });
}
function remove_task_attachment(link, id) {
    $.get(admin_url + 'tasks/remove_task_attachment/' + id, function(response) {
        if (response.success == true) {
            $(link).parents('li').remove();
        }
    }, 'json');
}
function add_task_comment() {
    var comment = $('#task_comment').val();
    if (comment == '') {
        return;
    }
    var data = {};
    data.content = comment;
    data.taskid = taskid;
    $('body').append('<div class="dt-loader"></div>');
    $.post(admin_url + 'tasks/add_task_comment', data).success(function(response) {
        response = $.parseJSON(response);
        $('body').find('.dt-loader').remove();
        if (response.success == true) {
            init_task_modal(taskid);
        }
    });
}

// Reload followers select field and removes the already added follower from the select field
function reload_followers_select() {
    $.get(admin_url + 'tasks/reload_followers_select/' + taskid, function(response) {
        $('select[name="select-followers"]').html(response);
        $('select[name="select-followers"]').selectpicker('refresh');
    });
}
// Reload assignes select field and removes the already added assignees from the select field
function reload_assignees_select() {
    $.get(admin_url + 'tasks/reload_assignees_select/' + taskid, function(response) {
        $('select[name="select-assignees"]').html(response);
        $('select[name="select-assignees"]').selectpicker('refresh');
    });
}
// Deletes task comment from database
function remove_task_comment(commentid) {
    $.get(admin_url + 'tasks/remove_comment/' + commentid, function(response) {
        if (response.success == true) {
            $('[data-commentid="' + commentid + '"]').remove();
        }
    }, 'json');
}
// Remove task assignee
function remove_assignee(id, taskid) {
    $.get(admin_url + 'tasks/remove_assignee/' + id + '/' + taskid, function(response) {
        if (response.success == true) {
            alert_float('success', response.message);
        } else {
            alert_float('warning', response.message);
        }
        init_task_modal(taskid);
    }, 'json');
}
// Remove task follower
function remove_follower(id, taskid) {
    $.get(admin_url + 'tasks/remove_follower/' + id + '/' + taskid, function(response) {
        if (response.success == true) {
            alert_float('success', response.message);
            init_task_modal(taskid);
        }
    }, 'json');
}
// Marking task as complete
function mark_complete(task_id) {
    $('body').append('<div class="dt-loader"></div>');
    $.get(admin_url + 'tasks/mark_complete/' + task_id, function(response) {
        $('body').find('.dt-loader').remove();
        if (response.success == true) {
            reload_tasks_tables();
            if ($('.task-modal-single').is(':visible')) {
                init_task_modal(task_id);
            }
            alert_float('success', response.message);
            if($('body').hasClass('home')){
                $('body').find('[data-widget-task-id="'+task_id+'"]').remove();
            }
        }
    }, 'json');
}
function reload_tasks_tables(){
    if ($.fn.DataTable.isDataTable('.table-tasks')) {
        $('.table-tasks').DataTable().ajax.reload();
    }
    if ($.fn.DataTable.isDataTable('.table-rel-tasks')) {
        $('.table-rel-tasks').DataTable().ajax.reload();
    }
    if ($.fn.DataTable.isDataTable('.table-rel-tasks-leads')) {
        $('.table-rel-tasks-leads').DataTable().ajax.reload();
    }
}
// Marking task as complete
function unmark_complete(task_id) {
    $('body').append('<div class="dt-loader"></div>');
    $.get(admin_url + 'tasks/unmark_complete/' + task_id, function(response) {
        $('body').find('.dt-loader').remove();
        if (response.success == true) {
            reload_tasks_tables();
            if ($('.task-modal-single').is(':visible')) {
                init_task_modal(task_id);
            }
            alert_float('success', response.message);
        }
    }, 'json');
}

function make_task_public(task_id) {
    $.get(admin_url + 'tasks/make_public/' + task_id, function(response) {
        if (response.success == true) {
            reload_tasks_tables();
            init_task_modal(task_id);
        }
    }, 'json');
}
function new_task(url){
    var _url = admin_url + 'tasks/task';
    if(typeof(url) != 'undefined'){
        _url = url;
    }
    $.get(_url,function(response){
        $('#_task').html(response)
        $('body').find('#_task_modal').modal('show');
    });
}

function new_task_from_relation(table) {
    var rel_id = $(table).data('new-rel-id');
    var rel_type = $(table).data('new-rel-type');
    var url = admin_url + 'tasks/task?rel_id=' + rel_id + '&rel_type=' + rel_type;
    new_task(url);
}
// Go to edit view
function edit_task(_task_id) {
    if(typeof(_task_id) != 'undefined'){
       taskid = _task_id;
    }

    $.get(admin_url + 'tasks/task/'+taskid,function(response){
        $('#_task').html(response)
        $('body').find('#_task_modal').modal('show');
    });
}
function task_form_handler(form){
    var data = $(form).serialize();
    var url = form.action;
    $('#_task button[type="submit"]').addClass('disabled');
    $.post(url, data).success(function(response) {
        $('#_task button[type="submit"]').removeClass('disabled');
        response = $.parseJSON(response);
        if(response.success == true){
            alert_float('success',response.message);
        }
        if(!$('body').hasClass('project')){
            $('#_task_modal').modal('hide');
             reload_tasks_tables();
             if(response.id){
               init_task_modal(response.id);
               taskid = response.id;
             } else {
               init_task_modal(taskid);
             }
         } else {
            $('#_task_modal').modal('hide');
            var _r_task_id;
            if(response.id){
                _r_task_id = response.id;
            } else {
                _r_task_id = taskid;
            }
            // reload page on project area
            var location = window.location.href;
            location = location.split('?');
            var red = '';
            var group = get_url_param('group');
            if(group){
                red += 'group='+group+'&taskid='+_r_task_id;
            } else {
                red += 'taskid='+_r_task_id;
            }
            window.location.href = location[0]+'?'+red;
         }
    });
   return false;
}

function timer_action(task_id,timer_id){
    if(typeof(timer_id) == 'undefined'){
        timer_id = '';
    }
    $.get(admin_url +'tasks/timer_tracking/'+task_id+'/'+timer_id,function(response){
        if ($('.task-modal-single').is(':visible')) {
            init_task_modal(task_id);
        }
        init_timers();
        reload_tasks_tables();
    },'json');
}

function init_task_modal(taskid){

    $.post(admin_url + 'tasks/get_task_data/', {
        taskid: taskid
    }).success(function(response) {

        if (response == 'false') {
            alert_float('warning', 'Failed to load task');
            setTimeout(function() {
                window.location.href = admin_url + 'tasks/list_tasks';
            }, 4000);
        }
        taskid = taskid;

        $('.task-modal-single .modal-content .data').html(response);
        reload_followers_select();
        reload_assignees_select();
        init_tasks_checklist_items();
        $('.task-modal-single').modal('show');

    });
}

function task_tracking_stats(id){
    $.get(admin_url + 'tasks/task_tracking_stats/'+id,function(response){
        $('body').find('#tacking-stats').html(response);
        $('#task-tracking-stats-modal').modal('show');
    });
}
function init_timers(){
    $.get(admin_url + 'tasks/get_staff_started_timers',function(response){
        if(response.timers_found){
            $('.top-timers').addClass('text-warning');
        } else {
            $('.top-timers').removeClass('text-warning');
        }
        $('.started-timers-top').html(response.html);
    },'json');
}

function edit_task_comment(id){
    var edit_wrapper = $('[data-edit-comment="'+id+'"]');
    edit_wrapper.removeClass('hide');
    edit_wrapper.next().addClass('hide');
}
function cancel_edit_comment(id){
    var edit_wrapper = $('[data-edit-comment="'+id+'"]');
    edit_wrapper.addClass('hide');
    edit_wrapper.next().removeClass('hide');
}
function save_edited_comment(id){
    var data = {};
    data.id = id;
    data.content = $('[data-edit-comment="'+id+'"]').find('textarea').val();
    $.post(admin_url + 'tasks/edit_comment',data).success(function(response){
        response = $.parseJSON(response);
        if(response.success == true){
            alert_float('success',response.message);
            init_task_modal(taskid);
        } else {
            cancel_edit_comment(id);
        }
    });
}
