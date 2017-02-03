<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
    <?php if (isset($title)){
      echo $title;
    }
    ?>
  </title>
  <link href="<?php echo site_url('assets/css/reset.css'); ?>" rel="stylesheet">
  <?php if(get_option('favicon') != ''){ ?>
  <link href="<?php echo site_url('uploads/company/'.get_option('favicon')); ?>" rel="shortcut icon">
  <?php } ?>
  <script src="<?php echo site_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
  <!-- Bootstrap -->
  <link href="<?php echo site_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <?php if(get_option('rtl_support_client') == 1){ ?>
  <link rel="stylesheet" href="<?php echo site_url('assets/plugins/bootstrap-arabic/css/bootstrap-arabic.min.css'); ?>">
  <?php } ?>
  <link href='<?php echo site_url('assets/plugins/open-sans-fontface/open-sans.css'); ?>' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/css/bs-overides.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/plugins/animate.css/animate.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/plugins/datatables/css/dataTables.bootstrap.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/plugins/animate.css/animate.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo site_url('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css'); ?>">
  <?php if(is_client_logged_in()){ ?>
  <link href="<?php echo site_url('assets/plugins/dropzone/min/basic.min.css'); ?>" rel='stylesheet'>
  <link href="<?php echo site_url('assets/plugins/dropzone/min/dropzone.min.css'); ?>" rel='stylesheet'>
  <link href='<?php echo site_url('assets/plugins/gantt/css/style.css'); ?>' rel='stylesheet' />
  <link href='<?php echo site_url('assets/plugins/jquery-comments/css/jquery-comments.css'); ?>' rel='stylesheet' />
  <?php } ?>
  <link href="<?php echo template_assets_url(); ?>css/style.css" rel="stylesheet" type='text/css'>
  <link href="<?php echo site_url('assets/css/custom.css'); ?>" rel="stylesheet" type='text/css'>

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
      <script>
        var site_url = '<?php echo site_url(''); ?>';
        // Settings required for javascript
        var maximum_allowed_ticket_attachments = '<?php echo get_option("maximum_allowed_ticket_attachments"); ?>';
        var ticket_attachments_file_extension = '<?php echo get_option("ticket_attachments_file_extensions"); ?>';
        var use_services = '<?php echo get_option("services"); ?>';
        var tables_pagination_limit = '<?php echo get_option("tables_pagination_limit"); ?>';
        var date_format = '<?php echo get_option("dateformat"); ?>';
        date_format = date_format.split('|');
        date_format = date_format[1];
       // Datatables language
       var dt_emptyTable = '<?php echo _l("clients_dt_empty_table"); ?>';
       var dt_info = '<?php echo _l("clients_dt_info"); ?>';
       var dt_infoEmpty = '<?php echo _l("clients_dt_info_empty"); ?>';
       var dt_infoFiltered = '<?php echo _l("clients_dt_info_filtered"); ?>';
       var dt_lengthMenu = '<?php echo _l("clients_dt_length_menu"); ?>';
       var dt_loadingRecords = '<?php echo _l("clients_dt_loading_records"); ?>';
       var dt_search = '<?php echo _l("clients_dt_search"); ?>';
       var dt_zeroRecords = '<?php echo _l("clients_dt_zero_records"); ?>';
       var dt_paginate_first = '<?php echo _l("clients_dt_paginate_first"); ?>';
       var dt_paginate_last = '<?php echo _l("clients_dt_paginate_last"); ?>';
       var dt_paginate_next = '<?php echo _l("clients_dt_paginate_next"); ?>';
       var dt_paginate_previous = '<?php echo _l("clients_dt_paginate_previous"); ?>';
       var dt_sortAscending = '<?php echo _l("clients_dt_sort_ascending"); ?>';
       var dt_sortDescending = '<?php echo _l("clients_dt_sort_descending"); ?>';
       var dt_entries = '<?php echo _l("dt_entries"); ?>';
        // End Datatables language
        //
        // Discussions
        var discussion_add_comment = '<?php echo _l('discussion_add_comment'); ?>';
        var discussion_newest = '<?php echo _l('discussion_newest'); ?>';
        var discussion_oldest = '<?php echo _l('discussion_oldest'); ?>';
        var discussion_attachments = '<?php echo _l('discussion_attachments'); ?>';
        var discussion_send = '<?php echo _l('discussion_send'); ?>';
        var discussion_reply = '<?php echo _l('discussion_reply'); ?>';
        var discussion_edit = '<?php echo _l('discussion_edit'); ?>';
        var discussion_edited = '<?php echo _l('discussion_edited'); ?>';
        var discussion_you = '<?php echo _l('discussion_you'); ?>';
        var discussion_save = '<?php echo _l('discussion_save'); ?>';
        var discussion_delete = '<?php echo _l('discussion_delete'); ?>';
        var discussion_view_all_replies = '<?php echo _l('discussion_view_all_replies') . ' (__replyCount__)'; ?>';
        var discussion_hide_replies = '<?php echo _l('discussion_hide_replies'); ?>';
        var discussion_no_comments = '<?php echo _l('discussion_no_comments'); ?>';
        var discussion_no_attachments = '<?php echo _l('discussion_no_attachments'); ?>';
        var discussion_attachments_drop = '<?php echo _l('discussion_attachments_drop'); ?>';
      </script>
    </head>
    <body class="<?php if(isset($bodyclass)){echo $bodyclass; } ?>" <?php if(get_option('rtl_support_client') == 1){ echo 'dir="rtl"';} ?>>

