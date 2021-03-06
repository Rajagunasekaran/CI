var project_id = $('input[name="project_id"]').val();
var discussion_user_profile_image_url = $('input[name="discussion_user_profile_image_url"]').val();
var discussion_id = $('input[name="discussion_id"]').val();
$(document).ready(function() {
    initDataTable();
    var tab_group = get_url_param('group');
    if (tab_group) {
        $('body').find('.nav-tabs li').removeClass('active');
        $('body').find('.nav-tabs [data-group="' + tab_group + '"]').parents('li').addClass('active');
    }

    for (var i = -10; i < $('.task-phase').length / 2; i++) {
        var r = 120;
        var g = 169;
        var b = 56;
        $('.task-phase:eq(' + (i + 10) + ')').css('background', color(r - (i * 13), g - (i * 13), b - (i * 13))).css('border', '1px solid ' + color(r - (i * 13), g - (i * 13), b - (i * 13)));
    };
    fix_phases_height();


    var circle = $('.project-progress').circleProgress({
        value: $('input[name="percent_progress"]').val(),
        size: 180,
        fill: {
            gradient: ["#D8EDA3", "#D8EDA3"]
        }
    }).on('circle-animation-progress', function(event, progress, stepValue) {
        $(this).find('strong.project-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
    });

    $('#discussion-comments').comments({
        roundProfilePictures: true,
        textareaRows: 2,
        enableDeleting: false,
        textareaRowsOnFocus: 5,
        profilePictureURL: discussion_user_profile_image_url,
        enableUpvoting: false,
        enableAttachments: true,
        popularText: '',
        enableDeletingCommentWithReplies: true,
        textareaPlaceholderText: discussion_add_comment,
        newestText: discussion_newest,
        oldestText: discussion_oldest,
        attachmentsText: discussion_attachments,
        sendText: discussion_send,
        replyText: discussion_reply,
        editText: discussion_edit,
        editedText: discussion_edited,
        youText: discussion_you,
        saveText: discussion_save,
        deleteText: discussion_delete,
        viewAllRepliesText: discussion_view_all_replies + ' (__replyCount__)',
        hideRepliesText: discussion_hide_replies,
        noCommentsText: discussion_no_comments,
        noAttachmentsText: discussion_no_attachments,
        attachmentDropText: discussion_attachments_drop,
        getComments: function(success, error) {
            $.post(site_url + 'clients/project/' + project_id, {
                action: 'discussion_comments',
                discussion_id: discussion_id
            }).success(function(response) {
                response = $.parseJSON(response);
                success(response);
            })


        },
        postComment: function(commentJSON, success, error) {
            commentJSON.action = 'new_discussion_comment';
            commentJSON.discussion_id = discussion_id;
            $.ajax({
                type: 'post',
                url: site_url + 'clients/project/' + project_id,
                data: commentJSON,
                success: function(comment) {
                    comment = $.parseJSON(comment);
                    success(comment)
                },
                error: error
            });
        },
        putComment: function(commentJSON, success, error) {
            commentJSON.action = 'update_discussion_comment';
            $.ajax({
                type: 'post',
                url: site_url + 'clients/project/' + project_id,
                data: commentJSON,
                success: function(comment) {
                    comment = $.parseJSON(comment);
                    success(comment)
                },
                error: error
            });
        },
        deleteComment: function(commentJSON, success, error) {
            $.ajax({
                type: 'post',
                url: site_url + 'clients/project/' + project_id,
                success: success,
                error: error,
                data: {
                    id: commentJSON.id,
                    action: 'delete_discussion_comment'
                }
            });
        },
        timeFormatter: function(time) {
            return moment(time).fromNow();
        },
        uploadAttachments: function(commentArray, success, error) {
            var responses = 0;
            var successfulUploads = [];

            var serverResponded = function() {
                responses++;
                // Check if all requests have finished
                if (responses == commentArray.length) {
                    // Case: all failed
                    if (successfulUploads.length == 0) {
                        error();
                        // Case: some succeeded
                    } else {
                        successfulUploads = $.parseJSON(successfulUploads);
                        success(successfulUploads)
                    }
                }
            }
            $(commentArray).each(function(index, commentJSON) {
                // Create form data
                var formData = new FormData();
                $(Object.keys(commentJSON)).each(function(index, key) {
                    var value = commentJSON[key];
                    if (value) formData.append(key, value);
                });

                formData.append('action', 'new_discussion_comment');
                formData.append('discussion_id', discussion_id);

                $.ajax({
                    url: site_url + 'clients/project/' + project_id,
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(commentJSON) {
                        successfulUploads.push(commentJSON);
                        serverResponded();
                    },
                    error: function(data) {
                        serverResponded();
                    },
                });
            });
        }
    });


        Dropzone.options.taskFileUpload = {
         paramName: "file",
         addRemoveLinks: true,
         accept: function(file, done) {
           done();
        },
        sending: function(file, xhr, formData) {
            formData.append("action", 'upload_task_file');
            formData.append("task_id", $('input[name="task_id"]').val());
        },
        success: function(file, response) {
          window.location.reload();
        }
        };

        Dropzone.options.projectFilesUpload = {
         paramName: "file",
         addRemoveLinks: true,
         accept: function(file, done) {
           done();
       },
       sending: function(file, xhr, formData) {
        formData.append("action", 'upload_file');
            },
            success: function(file, response) {
              window.location.reload();
          }
        };


   $.validator.setDefaults({
    highlight: function(element) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    errorElement: 'span',
    errorClass: 'text-danger',
    errorPlacement: function(error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

   $('.open-new-ticket-form').validate({
    rules: {
        subject: 'required',
        department: 'required',
        "attachments[]": {
            extension: ticket_attachments_file_extension
        }
    }
});

   $('#ticket-reply').validate({
    rules: {
        "attachments[]": {
            extension: ticket_attachments_file_extension
        }
    }
});

   $('.add_more_attachments').on('click', function() {
    var total_attachments = $('input[name="attachments[]"]').length;
    if (total_attachments >= maximum_allowed_ticket_attachments) {
        return false;
    }
    var newattachment = $('.attachments').find('.attachment').eq(0).clone().appendTo('.attachments');
    $(newattachment).find('input').val('');
    $(newattachment).find('i').removeClass('fa-plus').addClass('fa-minus');
    $(newattachment).find('button').removeClass('add_more_attachments').addClass('remove_attachment').removeClass('btn-success').addClass('btn-danger')
});

   $('body').on('click', '.remove_attachment', function() {
    $(this).parents('.attachment').remove();
});

   $('.single-ticket-add-reply').on('click', function(e) {
    e.preventDefault()
    var reply_area = $('.single-ticket-reply-area');
    reply_area.slideToggle();
});

    /*initDataTable('.tickets-table',window.location.href,'tickets','undefined','undefined',[4,'DESC']);
  initDataTable('.table-invoices',window.location.href,'invoices');
    initDataTable('.table-contracts',window.location.href,'contracts');
    initDataTable('.table-estimates',window.location.href,'estimates','undefined','undefined',[0,'DESC']);
    initDataTable('.table-proposals',window.location.href,'proposals','undefined','undefined',[3,'DESC']);*/

    $('.dismiss_announcement').on('click', function() {
        var announcementid = $(this).data('id');
        $.post(site_url + 'clients/dismiss_announcement', {
            announcementid: announcementid
        });
    });

    // User cant add more money then the invoice total remaining
    $('body.viewinvoice input[name="amount"]').on('keyup', function() {
        var original_total = $(this).data('total');
        var val = $(this).val();
        var form_group = $(this).parents('.form-group');
        if (val > original_total) {
            form_group.addClass('has-error');
            if (form_group.find('p.text-danger').length == 0) {
                form_group.append('<p class="text-danger">Maximum pay value passed</p>');
                $(this).parents('form').find('input[name="make_payment"]').attr('disabled', true);
            }
        } else {
            form_group.removeClass('has-error');
            form_group.find('p.text-danger').remove();
            $(this).parents('form').find('input[name="make_payment"]').attr('disabled', false);
        }
    });

    $('#discussion_form').validate({
        rules: {
            subject: 'required',
        }
    });


    $('#discussion').on('hidden.bs.modal', function(event) {
        $('#discussion input[name="subject"]').val('');
        $('#discussion textarea[name="description"]').val('');
        $('#discussion .add-title').removeClass('hide');
        $('#discussion .edit-title').removeClass('hide');
    });

});

function new_discussion() {
    $('#discussion').modal('show');
    $('#discussion .edit-title').addClass('hide');
}

function manage_discussion(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function(response) {
        response = $.parseJSON(response);
        if (response.success == true) {
            alert_float('success', response.message);
        }
        $('.table-project-discussions').DataTable().ajax.reload();
        $('#discussion').modal('hide');
    });
    return false;
}
function remove_task_comment(commentid) {
    $.get(site_url + 'clients/remove_task_comment/' + commentid, function(response) {
        if (response.success == true) {
            $('[data-commentid="' + commentid + '"]').remove();
        }
    }, 'json');
}
function edit_task_comment(id){
    var edit_wrapper = $('[data-edit-comment="'+id+'"]');
    edit_wrapper.removeClass('hide');
    $('[data-comment-content="'+id+'"]').addClass('hide');
}
function cancel_edit_comment(id){
    var edit_wrapper = $('[data-edit-comment="'+id+'"]');
    edit_wrapper.addClass('hide');
    $('[data-comment-content="'+id+'"]').removeClass('hide');
}
function save_edited_comment(id){
    var data = {};
    data.id = id;
    data.content = $('[data-edit-comment="'+id+'"]').find('textarea').val();
    $.post(site_url + 'clients/edit_comment',data).success(function(response){
        response = $.parseJSON(response);
        if(response.success == true){
            window.location.reload();
        } else {
            cancel_edit_comment(id);
        }
    });
}

function initDataTable() {
    $('.dt-table').DataTable({
        "language": {
            "emptyTable": dt_emptyTable.format(dt_entries),
            "info": dt_info.format(dt_entries),
            "infoEmpty": dt_infoEmpty.format(dt_entries),
            "infoFiltered": dt_infoFiltered.format(dt_entries),
            "lengthMenu": dt_lengthMenu.format(dt_entries),
            "loadingRecords": dt_loadingRecords,
            "search": dt_search,
            "zeroRecords": dt_zeroRecords,
            "paginate": {
                "first": dt_paginate_first,
                "last": dt_paginate_last,
                "next": dt_paginate_next,
                "previous": dt_paginate_previous
            },
            "aria": {
                "sortAscending": ": " + dt_sortAscending,
                "sortDescending": ": " + dt_sortDescending
            }
        },
        'paginate': true,
        'responsive': true,
        "bLengthChange": false,
        "pageLength": tables_pagination_limit,
        "order": [
        [0, 'DESC']
        ],
    });
}

function get_url_param(param) {
    var vars = {};
    window.location.href.replace(location.hash, '').replace(
        /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
        function(m, key, value) { // callback
            vars[key] = value !== undefined ? value : '';
        }
        );
    if (param) {
        return vars[param] ? vars[param] : null;
    }
    return vars;
}

function fix_phases_height() {
    var maxPhaseHeight = Math.max.apply(null, $("div.tasks-phases .panel-body").map(function() {
        return $(this).outerHeight();
    }).get());
    $('div.tasks-phases .panel-body').css('min-height', maxPhaseHeight + 'px');
}

function color(r, g, b) {
    return 'rgb(' + r + ',' + g + ',' + b + ')';
}

function taskTable() {
    $('.tasks-table').toggleClass('hide');
    $('.tasks-phases').toggleClass('hide');
}
function dt_custom_view(table,column,val){
    var tableApi = $(table).DataTable();
    tableApi.column(column).search(val).draw();
}
