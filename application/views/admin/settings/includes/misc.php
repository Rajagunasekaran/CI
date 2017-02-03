<div class="row">
    <div class="col-md-6">
        <?php echo render_input('settings[tables_pagination_limit]','settings_general_tables_limit',get_option('tables_pagination_limit'),'number'); ?>
        <hr />
        <?php render_yes_no_option('client_staff_add_edit_delete_task_comments_first_hour','settings_client_staff_add_edit_delete_task_comments_first_hour'); ?>
        <hr />
        <?php echo render_input('settings[limit_top_search_bar_results_to]','settings_limit_top_search_bar_results',get_option('limit_top_search_bar_results_to'),'number'); ?>
        <hr />
        <?php echo render_input('settings[leads_kanban_limit]','settings_leads_kanban_limit',get_option('leads_kanban_limit'),'number'); ?>
        <hr />
        <?php echo render_select('settings[default_staff_role]',$roles,array('roleid','name'),'settings_general_default_staff_role',get_option('default_staff_role'),array(),array('data-toggle'=>'tooltip','title'=>'settings_general_default_staff_role_tooltip')); ?>
        <hr />
        <?php echo render_input('settings[contract_expiration_before]','settings_reminders_contracts',get_option('contract_expiration_before'),'number',array('data-toggle'=>'tooltip','title'=>'settings_reminders_contracts_tooltip')); ?>
        <hr />

        <?php echo render_input('settings[tasks_reminder_notification_before]','tasks_reminder_notification_before',get_option('tasks_reminder_notification_before'),'number',array('data-toggle'=>'tooltip','title'=>'tasks_reminder_notification_before_help')); ?>
         <hr />
        <?php echo render_input('settings[google_api_key]','settings_google_api',get_option('google_api_key')); ?>
        <hr />
        <?php echo render_input('settings[media_max_file_size_upload]','settings_media_max_file_size_upload',get_option('media_max_file_size_upload'),'number'); ?>
        <hr />
        <?php echo render_input('settings[custom_pdf_logo_image_url]','settings_custom_pdf_logo_image_url',get_option('custom_pdf_logo_image_url'),'text',array('data-toggle'=>'tooltip','title'=>'settings_custom_pdf_logo_image_url_tooltip')); ?>
        <hr />
        <h4 class="bold"><?php echo _l('settings_group_newsfeed'); ?></a></h4>
        <hr />
        <?php echo render_input('settings[newsfeed_upload_file_extensions]','settings_newsfeed_allowed_file_extensions',get_option('newsfeed_upload_file_extensions')); ?>
        <hr />
        <?php echo render_input('settings[newsfeed_maximum_files_upload]','settings_newsfeed_max_file_upload_post',get_option('newsfeed_maximum_files_upload'),'number'); ?>
        <hr />
        <?php echo render_input('settings[newsfeed_maximum_file_size]','settings_newsfeed_max_file_size',get_option('newsfeed_maximum_file_size'),'number'); ?>



    </div>
</div>
