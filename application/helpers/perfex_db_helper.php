<?php
/**
 * Check if field is used in table
 * @param  string  $field column
 * @param  string  $table table name to check
 * @param  integer  $id   ID used
 * @return boolean
 */
function is_reference_in_table($field, $table, $id)
{
    $CI =& get_instance();
    $CI->db->where($field, $id);
    $row = $CI->db->get($table)->row();
    if ($row) {
        return true;
    }
    return false;
}
function add_views_tracking($rel_type,$rel_id){
    $CI =& get_instance();
    if(!is_staff_logged_in()){
        $CI->db->where('rel_id',$rel_id);
        $CI->db->where('rel_type',$rel_type);
        $CI->db->order_by('id','DESC');
        $CI->db->limit(1);
        $row = $CI->db->get('tblviewstracking')->row();

        if($row){
            $dateFromDatabase = strtotime($row->date);
            $dateTwelveHoursAgo = strtotime("-1 hours");

            if ($dateFromDatabase >= $dateTwelveHoursAgo) {
                return false;
            }

        }
        $CI->db->insert('tblviewstracking',array(
            'rel_id'=>$rel_id,
            'rel_type'=>$rel_type,
            'date'=>date('Y-m-d H:i:s'),
            'view_ip'=>$CI->input->ip_address()));

    }
}
function get_views_tracking($rel_type,$rel_id){
    $CI =& get_instance();
    $CI->db->where('rel_id',$rel_id);
    $CI->db->where('rel_type',$rel_type);
    $CI->db->order_by('date','DESC');
    return $CI->db->get('tblviewstracking')->result_array();
}
/**
 * Add option in table
 * @since  Version 1.0.1
 * @param string $name  option name
 * @param string $value option value
 */
function add_option($name, $value = '')
{
    $CI =& get_instance();
    $exists = total_rows('tbloptions', array(
        'name' => $name
    ));
    if ($exists == 0) {
        $CI->db->insert('tbloptions', array(
            'name' => $name,
            'value' => $value
        ));
        $insert_id = $CI->db->insert_id();
        if ($insert_id) {
            return true;
        }
        return false;
    }
    return false;
}
/**
 * Get option value
 * @param  string $name Option name
 * @return mixed
 */
function get_option($name)
{
    $CI =& get_instance();
    $CI->load->library('perfex_base');
    return $CI->perfex_base->get_option($name);
}
/**
 * Get option value from database
 * @param  string $name Option name
 * @return mixed
 */
function update_option($name, $value)
{
    $CI =& get_instance();
    $CI->db->where('name', $name);
    $CI->db->update('tbloptions', array(
        'value' => $value
    ));
    if ($CI->db->affected_rows() > 0) {
        return true;
    }
    return false;
}
/**
 * Delete option
 * @since  Version 1.0.4
 * @param  mixed $id option id
 * @return boolean
 */
function delete_option($id)
{
    $CI =& get_instance();
    $CI->db->where('id', $id);
    $CI->db->delete('tbloptions');
    if ($CI->db->affected_rows() > 0) {
        return true;
    }
    return false;
}

/**
 * Get staff full name
 * @param  string $userid Optional
 * @return string Firstname and Lastname
 */
function get_staff_full_name($userid = '')
{
    $_userid = get_staff_user_id();
    if ($userid !== '') {
        $_userid = $userid;
    }
    $CI =& get_instance();
    $CI->db->where('staffid', $_userid);
    $staff = $CI->db->select('firstname,lastname')->from('tblstaff')->get()->row();
    return $staff->firstname . ' ' . $staff->lastname;
}
/**
 * Get client full name
 * @param  string $userid Optional
 * @return string Firstname and Lastname
 */
function get_client_full_name($userid = '')
{
    $_userid = get_client_user_id();
    if ($userid !== '') {
        $_userid = $userid;
    }
    $CI =& get_instance();
    $CI->db->where('userid', $_userid);
    $client = $CI->db->select('firstname,lastname,company')->from('tblclients')->get()->row();
    if(!empty($client->firstname) && !empty($client->lastname)){
        return $client->firstname . ' ' . $client->lastname;
    } else {
        return $client->company;
    }

}
/**
 * Get client default language
 * @param  mixed $clientid
 * @return mixed
 */
function get_client_default_language($clientid = '')
{
    if (!is_numeric($clientid)) {
        $clientid = get_client_user_id();
    }
    $CI =& get_instance();
    $CI->db->select('default_language');
    $CI->db->from('tblclients');
    $CI->db->where('userid', $clientid);
    $client = $CI->db->get()->row();
    if ($client) {
        return $client->default_language;
    }
    return '';
}
/**
 * Get staff default language
 * @param  mixed $staffid
 * @return mixed
 */
function get_staff_default_language($staffid = '')
{
    if (!is_numeric($staffid)) {
        $staffid = get_staff_user_id();
    }
    $CI =& get_instance();
    $CI->db->select('default_language');
    $CI->db->from('tblstaff');
    $CI->db->where('staffid', $staffid);
    $staff = $CI->db->get()->row();
    if ($staff) {
        return $staff->default_language;
    }
    return '';
}
/**
 * Log Activity for everything
 * @param  string $description Activity Description
 * @param  integer $staffid    Who done this activity
 */
function logActivity($description, $staffid = NULL)
{
    $CI =& get_instance();
    $log = array(
        'description' => $description,
        'date' => date('Y-m-d H:i:s')
    );
    if (!DEFINED('CRON')) {
        if ($staffid != NULL && is_numeric($staffid)) {
            $log['staffid'] = $staffid;
        } else {
            if (is_staff_logged_in()) {
                $log['staffid'] = get_staff_user_id();
            } else {
                $log['staffid'] = NULL;
            }
        }
    } else {
        // manually invoked cron
        if(is_staff_logged_in()){
            $log['staffid'] = get_staff_user_id();
        } else {
            $log['staffid'] = _l('activity_log_when_cron_job');
        }
    }

    $CI->db->insert('tblactivitylog', $log);
}
function add_main_menu_item($options = array(), $parent = '')
{
    $default_options = array(
        'name',
        'permission',
        'icon',
        'url',
        'id',
        'custom'
    );
    $data            = array();
    for ($i = 0; $i < count($default_options); $i++) {
        if (isset($options[$default_options[$i]])) {
            $data[$default_options[$i]] = $options[$default_options[$i]];
        } else {
            $data[$default_options[$i]] = '';
        }
    }
    $menu = get_option('aside_menu_active');
    $menu = json_decode($menu);
    // check if the id exists
    if ($data['id'] == '') {
        $data['id'] = slug_it($data['name']);
    }
    $total_exists = 0;
    foreach ($menu->aside_menu_active as $item) {
        if ($item->id == $data['id']) {
            $total_exists++;
        }
    }
    if ($total_exists > 0) {
        $data['id'] = $data['id'] . '-' . ($total_exists + 1);
    }
    if ($parent == '') {
        array_push($menu->aside_menu_active, $data);
    } else {
        $i = 0;
        foreach ($menu->aside_menu_active as $item) {
            if ($item->id == $parent) {
                if (!isset($item->children)) {
                    $menu->aside_menu_active[$i]->children   = array();
                    $menu->aside_menu_active[$i]->children[] = $data;
                    break;
                } else {
                    $menu->aside_menu_active[$i]->children[] = $data;
                    break;
                }
            }
            $i++;
        }
    }
    if (update_option('aside_menu_active', json_encode($menu))) {
        return true;
    }
    return false;
}
function add_setup_menu_item($options = array(), $parent = '')
{
    $default_options = array(
        'name',
        'permission',
        'icon',
        'url',
        'id',
        'custom'
    );
    $data            = array();
    for ($i = 0; $i < count($default_options); $i++) {
        if (isset($options[$default_options[$i]])) {
            $data[$default_options[$i]] = $options[$default_options[$i]];
        } else {
            $data[$default_options[$i]] = '';
        }
    }
    if ($data['id'] == '') {
        $data['id'] = slug_it($data['name']);
    }
    $menu = get_option('setup_menu_active');
    $menu = json_decode($menu);
    // check if the id exists
    if ($data['id'] == '') {
        $data['id'] = slug_it($data['name']);
    }
    $total_exists = 0;
    foreach ($menu->setup_menu_active as $item) {
        if ($item->id == $data['id']) {
            $total_exists++;
        }
    }
    if ($total_exists > 0) {
        $data['id'] = $data['id'] . '-' . ($total_exists + 1);
    }
    if ($parent == '') {
        array_push($menu->setup_menu_active, $data);
    } else {
        $i = 0;
        foreach ($menu->setup_menu_active as $item) {
            if ($item->id == $parent) {
                if (!isset($item->children)) {
                    $menu->setup_menu_active[$i]->children   = array();
                    $menu->setup_menu_active[$i]->children[] = $data;
                    break;
                } else {
                    $menu->setup_menu_active[$i]->children[] = $data;
                    break;
                }
            }
            $i++;
        }
    }
    if (update_option('setup_menu_active', json_encode($menu))) {
        return true;
    }
    return false;
}
/**
 * Add user notifications
 * @param array $values array of values [description,fromuserid,touserid,fromcompany,isread]
 */
function add_notification($values)
{
    $CI =& get_instance();
    foreach ($values as $key => $value) {
        $data[$key] = $value;
    }
    if(is_client_logged_in()){
        $data['fromuserid'] = 0;
        $data['fromclientid'] = get_client_user_id();
    } else {
        $data['fromuserid'] = get_staff_user_id();
        $data['fromclientid'] = 0;
    }

    if (isset($data['fromcompany'])) {
        unset($data['fromuserid']);
    }
    $data['date'] = date('Y-m-d H:i:s');
    $CI->db->insert('tblnotifications', $data);
}
/**
 * Count total rows on table based on params
 * @param  string $table Table from where to count
 * @param  array  $where
 * @return mixed  Total rows
 */
function total_rows($table, $where = array())
{
    $CI =& get_instance();
    if (is_array($where)) {
        if (sizeof($where) > 0) {
            $CI->db->where($where);
        }
    } else if (strlen($where) > 0) {
        $CI->db->where($where);
    }
    return $CI->db->count_all_results($table);
}
/**
 * Sum total from table
 * @param  string $table table name
 * @param  array  $attr  attributes
 * @return mixed
 */
function sum_from_table($table, $attr = array())
{
    if (!isset($attr['field'])) {
        show_error('sum_from_table(); function expect field to be passed.');
    }
    $where = '';
    if (isset($attr['where']) && is_array($attr['where'])) {
        $i = 0;
        foreach ($attr['where'] as $key => $val) {
            if ($i == 0) {
                $where .= ' WHERE ' . $key . '="' . $val . '"';
            } else {
                $where .= ' AND ' . $key . '="' . $val . '"';
            }
            $i++;
        }
    }
    $CI =& get_instance();
    $result = $CI->db->query('SELECT sum(' . $attr['field'] . ') as total FROM ' . $table . '' . $where . '')->row();
    return $result->total;
}
/**
 * General function for all datatables, performs search,additional select,join,where,orders
 * @param  array $aColumns           table columns
 * @param  mixed $sIndexColumn       main column in table for bettter performing
 * @param  string $sTable            table name
 * @param  array  $join              join other tables
 * @param  array  $where             perform where in query
 * @param  array  $additionalSelect  select additional fields
 * @param  string $orderby
 * @return array
 */
function data_tables_init($aColumns, $sIndexColumn, $sTable, $join = array(), $where = array(), $additionalSelect = array(), $orderby = '', $groupBy = '')
{
    $CI =& get_instance();
    $__post = $CI->input->post();
    /*
     * Paging
     */
    $sLimit = "";
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = "LIMIT " . intval($CI->input->post('start')) . ", " . intval($CI->input->post('length'));
    }
    $_aColumns = array();
    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (_startsWith($_column[0], 'tbl')) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
                    array_push($_aColumns, $_prefix);
                } else {
                    array_push($_aColumns, $column);
                }
            } else {
                array_push($_aColumns, $_column[0]);
            }
        } else {
            array_push($_aColumns, $column);
        }
    }
    /*
     * Ordering
     */
    $sOrder = "";
    if ($CI->input->post('order') && (!$CI->input->post('custom_sort_by') && !$CI->input->post('custom_view'))) {
        $sOrder = "ORDER BY  ";
        $sOrder .= $aColumns[intval($__post['order'][0]['column'])];
        $__order_column = $sOrder;
        if (strpos($__order_column, ' as ') !== false) {
            $sOrder = strbefore($__order_column, ' as');
        }
        $_order = strtoupper($__post['order'][0]['dir']);
        if ($_order == 'ASC') {
            $sOrder .= ' ASC';
        } else {
            $sOrder .= ' DESC';
        }
        $sOrder .= ', ';
        $sOrder = substr_replace($sOrder, "", -2);
        if ($sOrder == "ORDER BY") {
            $sOrder = "";
        }
        if ($sOrder == '' && $orderby != '') {
            $sOrder = $orderby;
        }
    } else if ($CI->input->post('custom_sort_by')) {
        $sort = $CI->input->post('custom_sort_by');
        if ($sort == 'priority') {
            $sOrder = "ORDER BY CASE Priority
            WHEN 'Urgent' THEN 1
            WHEN 'High' THEN 2
            WHEN 'Medium' THEN 3
            WHEN 'Low' THEN 4
            END";
        } else {
            $sOrder = 'ORDER BY ' . $sort . ' DESC';
        }
    } else {
        $sOrder = $orderby;
    }
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ((isset($__post['search'])) && $__post['search']['value'] != "") {
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $__search_column = $aColumns[$i];
            if (strpos($__search_column, ' as ') !== false) {
                $__search_column = strbefore($__search_column, ' as');
            }
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $sWhere .= $__search_column . " LIKE '%" . $__post['search']['value'] . "%' OR ";
            }
        }
        if (count($additionalSelect) > 0) {
            foreach ($additionalSelect as $searchAdditionalField) {
                 if (strpos($searchAdditionalField, ' as ') !== false) {
                    $searchAdditionalField = strbefore($__search_column, ' as');
                 }
                $sWhere .= $searchAdditionalField . " LIKE '%" . $__post['search']['value'] . "%' OR ";
            }
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere      = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $_search = $__post['columns'][$i]['search']['value'];
                 $__search_column = $aColumns[$i];
                 if (strpos($__search_column, ' as ') !== false) {
                    $__search_column = strbefore($__search_column, ' as');
                 }
                if ($_search != '') {
                    $valid_date = (bool) strtotime($_search);
                    if ($valid_date) {
                        $_search = to_sql_date($_search);
                    }
                    $sWhere .= $__search_column . " LIKE '%" . $_search . "%' OR ";
                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= $searchAdditionalField . " LIKE '%" . $_search . "%' OR ";
                        }
                    }
                    $searchFound++;
                }
            }
        }
        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }
    /*
     * SQL queries
     * Get data to display
     */
    $_additionalSelect = '';
    if (count($additionalSelect) > 0) {
        $_additionalSelect = ',' . implode(',', $additionalSelect);
    }
    $where = implode(' ', $where);
    if ($sWhere == '') {
        if (_startsWith($where, 'AND')) {
            $where = substr($where, 3);
            $where = 'WHERE' . $where;
        }
    } else {
        if (_startsWith($sWhere, 'WHERE') && ($where != '' && _startsWith($where, 'WHERE'))) {
            $where = substr($where, 5);
            $where = 'AND' . $where;
        }
    }
    $sQuery         = "
    SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $_aColumns)) . " " . $_additionalSelect . "
    FROM   $sTable
    " . implode(' ', $join) . "
    $sWhere
    " . $where . "
    $groupBy
    $sOrder
    $sLimit
    ";
    $rResult        = $CI->db->query($sQuery)->result_array();
    /* Data set length after filtering */
    $sQuery         = "
    SELECT FOUND_ROWS()
    ";
    $_query         = $CI->db->query($sQuery)->result_array();
    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];
    if (_startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }
    /* Total data set length */
    $sQuery = "
    SELECT COUNT(" . $sTable . '.' . $sIndexColumn . ")
    FROM $sTable " . implode(' ', $join) . ' ' . $where;
    $_query = $CI->db->query($sQuery)->result_array();
    $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
    /*
     * Output
     */
    $output = array(
        "draw" => $__post['draw'] ? intval($__post['draw']) : 0,
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );
    return array(
        'rResult' => $rResult,
        'output' => $output
    );
}
/**
 * Prefix field name with table ex. table.column
 * @param  string $table
 * @param  string $alias
 * @param  string $field field to check
 * @return string
 */
function prefixed_table_fields_wildcard($table, $alias, $field)
{
    $CI =& get_instance();
    $columns     = $CI->db->query("SHOW COLUMNS FROM $table")->result_array();
    $field_names = array();
    foreach ($columns as $column) {
        $field_names[] = $column["Field"];
    }
    $prefixed = array();
    foreach ($field_names as $field_name) {
        if ($field == $field_name) {
            $prefixed[] = "`{$alias}`.`{$field_name}` AS `{$alias}.{$field_name}`";
        }
    }
    return implode(", ", $prefixed);
}
