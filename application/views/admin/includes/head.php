<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php if(get_option('favicon') != ''){ ?>
  <link href="<?php echo site_url('uploads/company/'.get_option('favicon')); ?>" rel="shortcut icon">
  <?php } ?>
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title><?php if (isset($title)){ echo $title; } else { echo get_option('companyname'); } ?></title>
  <link href="<?php echo site_url('assets/css/reset.css'); ?>" rel="stylesheet">
  <!-- Bootstrap -->
  <link href="<?php echo site_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <?php if(get_option('rtl_support_admin') == 1){ ?>
  <link href="<?php echo site_url('assets/plugins/bootstrap-arabic/css/bootstrap-arabic.min.css'); ?>" rel="stylesheet">
  <?php } ?>
  <link href='<?php echo site_url('assets/plugins/open-sans-fontface/open-sans.css'); ?>' rel='stylesheet'>
  <link href="<?php echo site_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/plugins/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/plugins/quill/quill.snow.css'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/plugins/ContentTools/content-tools.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/plugins/metisMenu/metisMenu.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/plugins/animate.css/animate.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/plugins/datatables/css/dataTables.bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/plugins/datatables-buttons/css/buttons.dataTables.scss'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/plugins/dropzone/min/basic.min.css'); ?>" rel='stylesheet'>
  <link href="<?php echo site_url('assets/plugins/dropzone/min/dropzone.min.css'); ?>" rel='stylesheet'>
  <link href='<?php echo site_url('assets/plugins/fullcalendar/fullcalendar.min.css'); ?>' rel='stylesheet' />
  <link href='<?php echo site_url('assets/plugins/jquery-comments/css/jquery-comments.css'); ?>' rel='stylesheet' />
  <link href="<?php echo site_url('assets/css/bs-overides.css'); ?>" rel="stylesheet">
  <link href='<?php echo site_url('assets/plugins/gantt/css/style.css'); ?>' rel='stylesheet' />
  <link href="<?php echo site_url('assets/css/style.css'); ?>" rel="stylesheet">
  <link href="<?php echo site_url('assets/css/custom.css'); ?>" rel="stylesheet">


  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
      <script>
        var site_url = '<?php echo site_url(''); ?>';
        var admin_url = '<?php echo admin_url(''); ?>';
        // Settings required for javascript
        var maximum_allowed_ticket_attachments = '<?php echo get_option("maximum_allowed_ticket_attachments"); ?>';
        var ticket_attachments_file_extension = '<?php echo get_option("ticket_attachments_file_extensions"); ?>';
        var use_services = '<?php echo get_option("services"); ?>';
        var tables_pagination_limit = '<?php echo get_option("tables_pagination_limit"); ?>';
        var newsfeed_upload_file_extensions = '<?php echo get_option("newsfeed_upload_file_extensions"); ?>';
        var newsfeed_maximum_files_upload = '<?php echo get_option("newsfeed_maximum_files_upload"); ?>';
        var newsfeed_maximum_file_size = '<?php echo get_option("newsfeed_maximum_file_size"); ?>';
        var date_format = '<?php echo get_option("dateformat"); ?>';

        date_format = date_format.split('|');
        date_format_calendar = date_format[0];
        date_format = date_format[1];

        var decimal_separator = '<?php echo get_option("decimal_separator"); ?>';
        var thousand_separator = '<?php echo get_option("thousand_separator"); ?>';
        var currency_placement = '<?php echo get_option("currency_placement"); ?>';
        var timezone = '<?php echo get_option("default_timezone"); ?>';
        // Datatables language
        var dt_emptyTable = '<?php echo _l("dt_empty_table"); ?>';
        var dt_info = '<?php echo _l("dt_info"); ?>';
        var dt_infoEmpty = '<?php echo _l("dt_info_empty"); ?>';
        var dt_infoFiltered = '<?php echo _l("dt_info_filtered"); ?>';
        var dt_lengthMenu = '<?php echo _l("dt_length_menu"); ?>';
        var dt_loadingRecords = '<?php echo _l("dt_loading_records"); ?>';
        var dt_search = '<?php echo _l("dt_search"); ?>';
        var dt_zeroRecords = '<?php echo _l("dt_zero_records"); ?>';
        var dt_paginate_first = '<?php echo _l("dt_paginate_first"); ?>';
        var dt_paginate_last = '<?php echo _l("dt_paginate_last"); ?>';
        var dt_paginate_next = '<?php echo _l("dt_paginate_next"); ?>';
        var dt_paginate_previous = '<?php echo _l("dt_paginate_previous"); ?>';
        var dt_sortAscending = '<?php echo _l("dt_sort_ascending"); ?>';
        var dt_sortDescending = '<?php echo _l("dt_sort_descending"); ?>';
        var dt_column_visibility_tooltip = '<?php echo _l("dt_column_visibility_tooltip"); ?>';
        var dt_length_menu_all = '<?php echo _l("dt_length_menu_all"); ?>';
        // Datatables buttons
        var dt_button_column_visibility = '<?php echo _l("dt_button_column_visibility"); ?>';
        var dt_button_reload = '<?php echo _l("dt_button_reload"); ?>';
        var dt_button_excel = '<?php echo _l("dt_button_excel"); ?>';
        var dt_button_csv = '<?php echo _l("dt_button_csv"); ?>';
        var dt_button_pdf = '<?php echo _l("dt_button_pdf"); ?>';
        var dt_button_print = '<?php echo _l("dt_button_print"); ?>';
        var dt_button_export = '<?php echo _l("dt_button_export"); ?>';
        var item_field_not_formated = '<?php echo _l("numbers_not_formated_while_editing"); ?>';
        var dt_entries = '<?php echo _l("dt_entries"); ?>';
        // Zip
        var zip_invoices = '<?php echo _l('zip_invoices'); ?>';
        var zip_estimates = '<?php echo _l('zip_estimates'); ?>';
        var zip_payments = '<?php echo _l('zip_payments'); ?>';
        var dt_new_task = '<?php echo _l('new_task'); ?>';
        // End Datatables language
        // Chart general options
        var line_chart_options = {
            scaleShowGridLines : true,
            scaleGridLineColor : "rgba(0,0,0,.05)",
            scaleGridLineWidth : 1,
            bezierCurve : true,
            pointDot : true,
            pointDotRadius : 4,
            pointDotStrokeWidth : 1,
            pointHitDetectionRadius : 20,
            datasetStroke : true,
            datasetStrokeWidth : 1,
            datasetFill : true,
            responsive: true,
        }
        var google_api = '';
        var calendarIDs = '';
        var taskid;
        var task_tracking_stats_data;
        var has_tasks_permission = '<?php echo has_permission('manageTasks'); ?>';
        var locale = '<?php echo $locale; ?>';
      </script>
    </head>
    <?php do_action('before_body_start'); ?>
    <body <?php if(get_option('rtl_support_admin') == 1){ echo 'dir="rtl"';} ?>class="<?php if(isset($bodyclass)){echo $bodyclass . ' '; } ?><?php if($this->session->has_userdata('is_mobile') && $this->session->userdata('is_mobile') == true){echo 'hide-sidebar ';} ?><?php if(get_option('rtl_support_admin') == 1){echo 'rtl';} ?>">
    <?php do_action('after_body_start'); ?>
