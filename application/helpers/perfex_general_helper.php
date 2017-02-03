<?php
header('Content-Type: text/html; charset=utf-8');
/**
 * Update the config variable to installed / used in update and install
 * @since  Version 1.0.2
 * @param  string $config_path config path
 * @return boolean
 */
function update_config_installed()
{
    $CI =& get_instance();
    $config_path = APPPATH . 'config/config.php';
    $CI->load->helper('file');
    @chmod($config_path, FILE_WRITE_MODE);
    $config_file = read_file($config_path);
    $config_file = trim($config_file);
    $prefix      = "http://";
    if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        $prefix = 'https://';
    }
    $base_url = $prefix . $_SERVER['HTTP_HOST'];
    $base_url .= preg_replace('@/+$@', '', dirname($_SERVER['SCRIPT_NAME'])) . '/';
    $config_file = str_replace("\$config['installed'] = false;", "\$config['installed'] = true;", $config_file);
    $config_file = str_replace("\$config['base_url'] = '';", "\$config['base_url'] = '" . $base_url . "';", $config_file);
    if (!$fp = fopen($config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return FALSE;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $config_file, strlen($config_file));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($config_path, FILE_READ_MODE);
    return TRUE;
}
function get_strftime_format(){
    $format = get_current_date_format();
    if(strpos($format,'/') !== false){
        $sep = '/';
    } else if(strpos($format,'-') !== false){
        $sep = '-';
    } else if(strpos($format,'.') !== false){
        $sep = '.';
    } else {
        return $format;
    }

    return '%'.str_replace($sep,$sep.'%',$format);
}
/**
 * Get current date format from options
 * @return string
 */
function get_current_date_format()
{
    $format = get_option('dateformat');
    $format = explode('|', $format);
    return $format[0];
}
/**
 * Check if current user is admin
 * @param  mixed $staffid
 * @return boolean if user is not admin
 */
function is_admin($staffid = '')
{
    $_staffid = get_staff_user_id();
    if (is_numeric($staffid)) {
        $_staffid = $staffid;
    }
    $CI =& get_instance();
    $CI->db->select('1');
    $CI->db->where('admin', 1);
    $CI->db->where('staffid', $_staffid);
    $admin = $CI->db->get('tblstaff')->row();
    if($admin){
        return true;
    }
    return false;
    ;
}
/**
 * Is user logged in
 * @return boolean
 */
function is_logged_in()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('client_logged_in') && !$CI->session->has_userdata('staff_logged_in')) {
        return false;
    }
    return true;
}
/**
 * Is client logged in
 * @return boolean
 */
function is_client_logged_in()
{
    $CI =& get_instance();
    if ($CI->session->has_userdata('client_logged_in') && $CI->session->get_userdata('client_logged_in') != false) {
        return true;
    }
    return false;
}
/**
 * Is staff logged in
 * @return boolean
 */
function is_staff_logged_in()
{
    $CI =& get_instance();
    if ($CI->session->has_userdata('staff_logged_in')) {
        return true;
    }
    return false;
}
/**
 * Return logged staff User ID from session
 * @return mixed
 */
function get_staff_user_id()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('staff_logged_in')) {
        return false;
    }
    return $CI->session->userdata('staff_user_id');
}
/**
 * Return logged client User ID from session
 * @return mixed
 */
function get_client_user_id()
{
    $CI =& get_instance();
    if (!$CI->session->has_userdata('client_logged_in')) {
        return false;
    }
    return $CI->session->userdata('client_user_id');
}
/**
 * Get admin url
 * @param string url to append (Optional)
 * @return string admin url
 */
function admin_url($url = '')
{
    if ($url == '') {
        return site_url(ADMIN_URL) . '/';
    } else {
        return site_url(ADMIN_URL . '/' . $url);
    }
}
/**
 * Outputs language string based on passed line
 * @since  Version 1.0.1
 * @param  string $line  language line string
 * @param  string $label sprint_f label
 * @return string        formated language
 */
function _l($line, $label = '')
{
    $CI =& get_instance();
    if (is_array($label)) {
        $_line = vsprintf($CI->lang->line($line), $label);
    } else {
        $_line = sprintf($CI->lang->line($line), $label);
    }
    if ($_line !== '') {
        return $_line;
    }
    // dont change this line
    return 'translate_not_found_' . $line;
}
/**
 * Format date to selected dateformat
 * @param  date $date Valid date
 * @return date/string
 */
function _d($date)
{
    if (!is_date($date)) {
        return $date;
    }
    $format = get_current_date_format();
    if (strpos($date, ' ') === true) {
        $_date = new DateTime($date);
        $_date = $_date->format($format . ' H:i:s');
        if (is_date($_date)) {
            return $_date;
        }
        return $date;
    }
    $_date = new DateTime($date);
    $_date = $_date->format($format);
    if (is_date($_date)) {
        return $_date;
    }
    return $date;
}
/**
 * Format datetime to selected datetime format
 * @param  datetime $date datetime date
 * @return datetime/string
 */
function _dt($date)
{
    if (!is_date($date)) {
        return $date;
    }
    $_date = new DateTime($date);
    $_date = $_date->format(get_current_date_format() . ' H:i:s');
    if (is_date($_date)) {
        return $_date;
    }
    return $date;
}
/**
 * Convert string to sql date based on current date format from options
 * @param  string $date date string
 * @return mixed
 */
function to_sql_date($date)
{
    if ($date == '') {
        return;
    }
    return DateTime::createFromFormat(get_current_date_format(), $date)->format('Y-m-d');
}
/**
 * Check if passed string is valid date
 * @param  string  $date
 * @return boolean
 */
function is_date($date)
{
    return (bool) strtotime($date);
}
/**
 * Get locale key by system language
 * @param  string $language language name from (application/languages) folder name
 * @return string
 */
function get_locale_key($language = 'english'){
    if($language == ''){return 'en';}
    $locales = get_locales();

    if(isset($locales[$language])){
        return $locales[$language];
    } else if(isset($locales[ucfirst($language)])){
        return $locales[ucfirst($language)];
    } else {
        foreach($locales as $key => $val){
            $val = strtolower($val);
            $language = strtolower($language);
            if(strpos($val,$language) !== false){
                return $key;
            }
        }
    }

    return 'en';
}
/**
 * Check if staff user has permission
 * @param  string  $permission permission shortname
 * @param  mixed  $staffid if you want to check for particular staff
 * @return boolean
 */
function has_permission($permission, $staffid = '')
{
    $CI =& get_instance();
    // check for passed is_admin function
    if (function_exists($permission) && is_callable($permission)) {
        return call_user_func($permission, $staffid);
    }
    if (is_admin($staffid)) {
        return true;
    }
    $_userid = get_staff_user_id();
    if ($staffid != '') {
        $_userid = $staffid;
    }
    $CI->db->select('permissionid');
    $CI->db->where('shortname', $permission);
    $permission = $CI->db->get('tblpermissions')->row();
    $CI->db->select('1');
    $CI->db->from('tblstaffpermissions');
    $CI->db->where('permissionid', $permission->permissionid);
    $CI->db->where('staffid', $_userid);
    $perm = $CI->db->get()->row();
    if ($perm) {
        return true;
    }
    return false;
}
function has_customer_permission($permission, $userid = '')
{
    $CI =& get_instance();
    $CI->load->library('perfex_base');
    $permissions = $CI->perfex_base->get_customer_permissions();
    $_userid     = get_client_user_id();
    if ($userid != '') {
        $_userid = $userid;
    }
    foreach ($permissions as $_permission) {
        if ($_permission['short_name'] == $permission) {
            if (total_rows('tblcustomerpermissions', array(
                'permission_id' => $_permission['id'],
                'userid' => $_userid
            )) > 0) {
                return true;
            }
        }
    }
    return false;
}
