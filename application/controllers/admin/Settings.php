<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Settings extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        if (!has_permission('editSettings')) {
            access_denied('editSettings');
        }
        $this->load->model('settings_model');
    }
    /* View all settings */
    public function index()
    {
        if ($this->input->post()) {

            handle_company_logo_upload();
            handle_favicon_upload();
            $post_data = $this->input->post();
            $success   = $this->settings_model->update($post_data);
            if ($success > 0) {
                set_alert('success', _l('settings_updated'));
            }

            redirect(admin_url('settings?'.$_SERVER['QUERY_STRING']));
        }

        $this->load->model('taxes_model');
        $this->load->model('tickets_model');
        $data['taxes']             = $this->taxes_model->get();
        $data['ticket_priorities'] = $this->tickets_model->get_priority();
        $data['roles']             = $this->roles_model->get();
        $data['title']             = _l('options');

        if(!$this->input->get('group')){
            $view = 'general';
        } else {
            if($this->input->get('group') == 'online_payment_modes' && get_staff_user_id() != 1){
                redirect(admin_url());
            } else {
                $view = $this->input->get('group');
            }
        }

        $data['group'] =  $this->input->get('group');
        $data['group_view'] = $this->load->view('admin/settings/includes/'.$view,$data,true);
        $this->load->view('admin/settings/all', $data);
    }
    /* Remove company logo from settings / ajax */
    public function remove_company_logo()
    {
        if (file_exists(COMPANY_FILES_FOLDER . '/' . get_option('company_logo'))) {
            unlink(COMPANY_FILES_FOLDER . '/' . get_option('company_logo'));
        }
        update_option('company_logo', '');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function remove_favicon()
    {
        if (file_exists(COMPANY_FILES_FOLDER . '/' . get_option('favicon'))) {
            unlink(COMPANY_FILES_FOLDER . '/' . get_option('favicon'));
        }
        update_option('favicon', '');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function new_pdf_company_field()
    {
        if ($this->input->post()) {
            $success = $this->settings_model->add_new_company_pdf_field($this->input->post());
            if ($success === true) {
                set_alert('success', _l('added_successfuly', _l('new_company_field_name')));
                echo 'true';
            } else {
                echo 'false';
            }
        }
    }
    public function delete_option($id)
    {
        echo json_encode(array(
            'success' => delete_option($id)
            ));
    }
}
