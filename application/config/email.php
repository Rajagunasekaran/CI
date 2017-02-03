<?php
$CI =& get_instance();
if ($CI->config->item('installed') == true) {
    $config['useragent']    = "CodeIgniter";
    $config['mailpath']     = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
    $config['wordwrap']     = TRUE;
    $config['mailtype']     = 'html';
    $config['charset']      = strtoupper(get_option('smtp_email_charset'));
    $config['newline']      = "\r\n";
    $config['crlf']         = "\r\n";
    $config['protocol']     = 'smtp';
    $config['smtp_host']    = get_option('smtp_host');
    $config['smtp_port']    = get_option('smtp_port');
    $config['smtp_timeout'] = '30';
    $config['smtp_user']    = get_option('smtp_email');
    $config['smtp_pass']    = get_option('smtp_password');
    // set tls if you need to use tls
    $config['smtp_crypto'] = '';
}
