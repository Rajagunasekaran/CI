<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Perfex_Base
{
    private $options = array();
    // Quick actions aide
    private $quick_actions = array();
    // Instance CI
    private $_instance;
    // Dynamic options
    private $dynamic_options = array('next_invoice_number', 'next_estimate_number');
    function __construct()
    {
        $this->_instance =& get_instance();
        if ($this->_instance->config->item('installed') == true) {
            $options = $this->_instance->db->get('tbloptions')->result_array();
            foreach ($options as $option) {
                $this->options[$option['name']] = $option['value'];
            }
        }
    }
    public function get_table_data($table, $params = array())
    {
        $_td = do_action('before_render_table_data', array(
            'table' => $table,
            'params' => $params
        ));
        foreach ($_td['params'] as $key => $val) {
            $$key = $val;
        }
        $table = $_td['table'];
        if (file_exists(VIEWPATH . 'admin/tables/my_' . $table . '.php')) {
            include_once(VIEWPATH . 'admin/tables/my_' . $table . '.php');
        } else {
            include_once(VIEWPATH . 'admin/tables/' . $table . '.php');
        }
        echo json_encode($output);
        die;
    }
    public function get_options()
    {
        return $this->options;
    }
    public function get_option($name)
    {
        if (isset($this->options[$name])) {
            if (in_array($name, $this->dynamic_options)) {
                $this->_instance->db->where('name', $name);
                return $this->_instance->db->get('tbloptions')->row()->value;
            } else {
                return $this->options[$name];
            }
        }
        return '';
    }
    public function add_quick_actions_link($item = array())
    {
        $this->quick_actions[] = $item;
    }
    public function remove_quick_actions_link($name)
    {
        $i = 0;
        foreach ($this->quick_actions as $item) {
            if ($item['name'] == $name) {
                unset($this->quick_actions[$i]);
                break;
            }
            $i++;
        }
    }
    public function get_quick_actions_links()
    {
        $this->quick_actions = do_action('before_build_quick_actions_links', $this->quick_actions);
        return $this->quick_actions;
    }
    public function get_customer_permissions()
    {
        $permissions = array(
            array(
                'id' => 1,
                'name' => _l('customer_permission_invoice'),
                'short_name' => 'invoices'
            ),
            array(
                'id' => 2,
                'name' => _l('customer_permission_estimate'),
                'short_name' => 'estimates'
            ),
            array(
                'id' => 3,
                'name' => _l('customer_permission_contract'),
                'short_name' => 'contracts'
            ),
            array(
                'id' => 4,
                'name' => _l('customer_permission_proposal'),
                'short_name' => 'proposals'
            ),
            array(
                'id' => 5,
                'name' => _l('customer_permission_support'),
                'short_name' => 'support'
            ),
            array(
                'id'=>6,
                'name'=>_l('customer_permission_projects'),
                'short_name'=>'projects'
                )

        );
        return do_action('get_customer_permissions', $permissions);
    }
}

