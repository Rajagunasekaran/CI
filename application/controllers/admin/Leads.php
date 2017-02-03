<?php
header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') OR exit('No direct script access allowed');
class Leads extends Admin_controller
{
    private $not_importable_leads_fields = array('id', 'source', 'assigned', 'status', 'dateadded', 'last_status_change', 'addedfrom', 'leadorder', 'date_converted', 'lost', 'junk', 'is_imported_from_email_integration', 'email_integration_uid', 'is_public');
    function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
    }
    /* List all leads canban and table */
    public function index($id = '')
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->get('canban')) {
                $data['statuses'] = $this->leads_model->get_status();
                echo $this->load->view('admin/leads/kan-ban', $data, true);
                die();
            } else if ($this->input->get('table_leads')) {
                $this->perfex_base->get_table_data('leads');
            }
        }
        $this->load->model('staff_model');
        $data['staff']     = $this->staff_model->get('', 1);
        $data['bodyclass'] = 'kan-ban-body';
        $data['statuses']  = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
        $data['title']     = _l('leads');
        // in case accesed the url leads/index/ directly with id - used in search
        $data['leadid']    = $id;
        $this->load->view('admin/leads/manage_leads', $data);
    }
    /* Add or update lead */
    public function lead($id = '')
    {
        $lead = null;
        if (is_numeric($id)) {
            $lead = $this->leads_model->get($id);
            if (!is_admin()) {
                if (($lead->assigned != get_staff_user_id() && $lead->addedfrom != get_staff_user_id() && $lead->is_public != 1)) {
                    access_denied('Access not connected lead');
                }
            }
        }
        if ($this->input->post()) {
            if ($id == '') {
                $id      = $this->leads_model->add($this->input->post());
                $_id     = false;
                $success = false;
                $message = '';
                if ($id) {
                    $success = true;
                    $_id     = $id;
                    $message = _l('added_successfuly', _l('lead'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'id' => $_id,
                    'message' => $message
                ));
            } else {
                $success = $this->leads_model->update($this->input->post(), $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfuly', _l('lead'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
            die;
        }

        if ($lead == null && is_numeric($id)) {
            echo _l('lead_not_found');
            die;
        } else {
            $data['lead']          = $lead;
            $data['mail_activity'] = $this->leads_model->get_mail_activity($id);
            $data['notes']         = $this->leads_model->get_lead_notes($id);
            $data['activity_log']  = $this->leads_model->get_lead_activity_log($id);
        }

        $data['members']  = $this->staff_model->get();
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
        $this->load->model('staff_model');
        $this->load->view('admin/leads/lead', $data);
    }
    /* Delete lead from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('sources'));
        }
        if (!is_lead_creator($id) && !is_admin()) {
            die;
        }
        $response = $this->leads_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lead_lowercase')));
        } else if ($response === true) {
            set_alert('success', _l('deleted', _l('lead')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_lowercase')));
        }

        redirect($_SERVER['HTTP_REFERER']);
    }
    public function mark_as_lost($id)
    {
        $message = '';
        $success = $this->leads_model->mark_as_lost($id);
        if ($success) {
            $message = _l('lead_marked_as_lost');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    public function unmark_as_lost($id)
    {
        $message = '';
        $success = $this->leads_model->unmark_as_lost($id);
        if ($success) {
            $message = _l('lead_unmarked_as_lost');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    public function mark_as_junk($id)
    {
        $message = '';
        $success = $this->leads_model->mark_as_junk($id);
        if ($success) {
            $message = _l('lead_marked_as_junk');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    public function unmark_as_junk($id)
    {
        $message = '';
        $success = $this->leads_model->unmark_as_junk($id);
        if ($success) {
            $message = _l('lead_unmarked_as_junk');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    public function add_lead_attachment()
    {
        $leadid = $this->input->post('leadid');
        echo json_encode(handle_lead_attachments($leadid));
    }
    public function get_lead_attachments($id)
    {
        $data['attachments'] = $this->leads_model->get_lead_attachments($id);
        $this->load->view('admin/leads/leads_attachments_template', $data);
    }
    public function delete_attachment($id)
    {
        echo json_encode(array(
            'success' => $this->leads_model->delete_lead_attachment($id)
        ));
    }
    public function delete_lead_note($id)
    {
        echo json_encode(array(
            'success' => $this->leads_model->delete_lead_note($id)
        ));
    }
    // Sources
    /* Manage leads sources */
    public function sources()
    {
        if (!is_admin()) {
            access_denied('Leads Sources');
        }
        $data['sources'] = $this->leads_model->get_source();
        $data['title']   = 'Leads sources';
        $this->load->view('admin/leads/manage_sources', $data);
    }
    /* Add or update leads sources */
    public function source()
    {
        if (!is_admin()) {
            access_denied('Leads Sources');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->leads_model->add_source($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('lead_source')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->leads_model->update_source($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('lead_source')));
                }
            }
        }
    }
    /* Delete leads source */
    public function delete_source($id)
    {
        if (!is_admin()) {
            access_denied('Delete Lead Source');
        }
        if (!$id) {
            redirect(admin_url('leads/sources'));
        }
        $response = $this->leads_model->delete_source($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lead_source_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('lead_source')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_source_lowercase')));
        }
        redirect(admin_url('leads/sources'));
    }
    // Statuses
    /* View leads statuses */
    public function statuses()
    {
        if (!is_admin()) {
            access_denied('Leads Statuses');
        }
        $data['statuses'] = $this->leads_model->get_status();
        $data['title']    = 'Leads statuses';
        $this->load->view('admin/leads/manage_statuses', $data);
    }
    /* Add or update leads status */
    public function status()
    {
        if (!is_admin()) {
            access_denied('Leads Statuses');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->leads_model->add_status($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('lead_status')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->leads_model->update_status($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('lead_status')));
                }
            }
        }
    }
    /* Delete leads status from databae */
    public function delete_status($id)
    {
        if (!is_admin()) {
            access_denied('Leads Statuses');
        }
        if (!$id) {
            redirect(admin_url('leads/statuses'));
        }
        $response = $this->leads_model->delete_status($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lead_status_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('lead_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lead_status_lowercase')));
        }
        redirect(admin_url('leads/statuses'));
    }
    /* Add new lead note */
    public function add_note()
    {
        if ($this->input->post()) {
            $this->leads_model->add_note($this->input->post());
        }
    }
    /**
     * Convert lead to client
     * @since  version 1.0.1
     * @return mixed
     */
    public function convert_to_client()
    {
        if ($this->input->post()) {
            $this->load->model('clients_model');
            $data                = $this->input->post();
            $original_lead_email = $data['original_lead_email'];
            unset($data['original_lead_email']);
            if (isset($data['merge_db_fields'])) {
                $merge_db_fields = $data['merge_db_fields'];
                unset($data['merge_db_fields']);
            }
            if (isset($data['include_leads_custom_fields'])) {
                $include_leads_custom_fields = $data['include_leads_custom_fields'];
                unset($data['include_leads_custom_fields']);
            }
            $id = $this->clients_model->add($data);
            if ($id) {
                // Add all permissions to this lead-client
                $permissions = $this->perfex_base->get_customer_permissions();
                foreach ($permissions as $permission) {
                    $this->db->insert('tblcustomerpermissions', array(
                        'permission_id' => $permission['id'],
                        'userid' => $id
                    ));
                }
                $activity = _l('not_lead_activity_converted', get_staff_full_name());
                $this->leads_model->log_lead_activity($data['leadid'], $activity);
                $default_status = $this->leads_model->get('', true);
                $this->db->where('id', $data['leadid']);
                $this->db->update('tblleads', array(
                    'date_converted' => date('Y-m-d H:i:s'),
                    'status' => $default_status->id
                ));
                // Check if lead email is different then client email
                $client = $this->clients_model->get($id);
                if ($client->email != $original_lead_email) {
                    if ($original_lead_email != '') {
                        $activity = '<span data-toggle="tooltip" title="' . _l('not_lead_activity_email_change') . '">';
                        $activity .= _l('not_lead_activity_converted_email', array(
                            $original_lead_email,
                            $client->email
                        ));
                        $activity .= '</span>';
                        $this->leads_model->log_lead_activity($data['leadid'], $activity);
                    }
                }
                if (isset($include_leads_custom_fields)) {
                    foreach ($include_leads_custom_fields as $fieldid => $value) {
                        // checked dont merge
                        if ($value == 3) {
                            continue;
                        }
                        // get the value of this leads custom fiel
                        $this->db->where('relid', $data['leadid']);
                        $this->db->where('fieldto', 'leads');
                        $this->db->where('fieldid', $fieldid);
                        $lead_custom_field_value = $this->db->get('tblcustomfieldsvalues')->row()->value;
                        if ($value == 1) {
                            $this->db->where('id', $fieldid);
                            $field = $this->db->get('tblcustomfields')->row();
                            // check if this field exists for custom fields
                            $this->db->where('fieldto', 'customers');
                            $this->db->where('name', $field->name);
                            $exists               = $this->db->get('tblcustomfields')->row();
                            $copy_custom_field_id = NULL;
                            if ($exists) {
                                $copy_custom_field_id = $exists->id;
                            } else {
                                // there is no name with the same custom field for leads at the custom side create the custom field now
                                $this->db->insert('tblcustomfields', array(
                                    'fieldto' => 'customers',
                                    'name' => $field->name,
                                    'required' => $field->required,
                                    'type' => $field->type,
                                    'options' => $field->options,
                                    'field_order' => $field->field_order,
                                    'active' => $field->active,
                                    'show_on_pdf' => $field->show_on_pdf
                                ));
                                $new_customer_field_id = $this->db->insert_id();
                                if ($new_customer_field_id) {
                                    $copy_custom_field_id = $new_customer_field_id;
                                }
                            }
                            if ($copy_custom_field_id != NULL) {
                                $this->db->insert('tblcustomfieldsvalues', array(
                                    'relid' => $id,
                                    'fieldid' => $copy_custom_field_id,
                                    'fieldto' => 'customers',
                                    'value' => $lead_custom_field_value
                                ));
                            }
                        } else {
                            if (isset($merge_db_fields)) {
                                $db_field = $merge_db_fields[$fieldid];
                                // in case user dont select anything from the db fields
                                if ($db_field == '') {
                                    continue;
                                }
                                if ($db_field == 'country') {
                                    $this->db->where('iso2', $lead_custom_field_value);
                                    $this->db->or_where('short_name', $lead_custom_field_value);
                                    $this->db->or_like('long_name', $lead_custom_field_value);
                                    $country = $this->db->get('tblcountries')->row();
                                    if ($country) {
                                        $lead_custom_field_value = $country->country_id;
                                    } else {
                                        $lead_custom_field_value = 0;
                                    }
                                } else {
                                    $lead_custom_field_value = 0;
                                }
                                $this->db->where('userid', $id);
                                $this->db->update('tblclients', array(
                                    $db_field => $lead_custom_field_value
                                ));
                            }
                        }
                    }
                }
                // set the lead to status client in case is not status client
                $this->db->where('isdefault', 1);
                $status_client_id = $this->db->get('tblleadsstatus')->row()->id;
                $this->db->where('id', $data['leadid']);
                $this->db->update('tblleads', array(
                    'status' => $status_client_id
                ));
                set_alert('success', _l('lead_to_client_base_converted_success'));
                logActivity('Created Lead Client Profile [LeadID: ' . $data['leadid'] . ', ClientID: ' . $id . ']');
                redirect(admin_url('clients/client/' . $id));
            }
        }
    }
    // Ajax
    /* Used in canban when dragging */
    public function update_can_ban_lead_status()
    {
        if ($this->input->post()) {
            if ($this->input->is_ajax_request()) {
                $success = $this->leads_model->update_can_ban_lead_status($this->input->post());
                $message = '';
                if (!is_array($success)) {
                    if ($success) {
                        $message = _l('lead_status_updated');
                    }
                } else if ($success['is_converted_to_client']) {
                    $success = false;
                    $message = _l('lead_is_client_cant_change_status_canban');
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
                die();
            }
        }
    }
    public function update_status_order()
    {
        if ($this->input->post()) {
            $this->leads_model->update_status_order();
        }
    }
    public function test_email_integration()
    {
        if (!is_admin()) {
            access_denied('Leads Test Email Integration');
        }
        require_once(APPPATH . 'third_party/php-imap/Imap.php');
        $mail       = $this->leads_model->get_email_integration();
        $mailbox    = $mail->imap_server;
        $username   = $mail->email;
        $password   = $mail->password;
        $encryption = $mail->encryption;
        // open connection
        $imap       = new Imap($mailbox, $username, $password, $encryption);
        if ($imap->isConnected() === false) {
            set_alert('danger', _l('lead_email_connection_not_ok') . '<br /><b>' . $imap->getError() . '</b>');
        } else {
            set_alert('success', _l('lead_email_connection_ok'));
        }
        redirect(admin_url('leads/email_integration'));
    }
    public function email_integration()
    {
        if (!is_admin()) {
            access_denied('Leads Email Intregration');
        }
        if ($this->input->post()) {
            $success = $this->leads_model->update_email_integration($this->input->post());
            if ($success) {
                set_alert('success', _l('leads_email_integration_updated'));
            }
            redirect(admin_url('leads/email_integration'));
        }
        $data['roles']    = $this->roles_model->get();
        $data['sources']  = $this->leads_model->get_source();
        $data['statuses'] = $this->leads_model->get_status();
        $this->load->model('staff_model');
        $data['members'] = $this->staff_model->get('', 1);
        $data['title']   = _l('leads_email_integration');
        $data['mail']    = $this->leads_model->get_email_integration();
        $this->load->view('admin/leads/email_integration', $data);
    }
    public function change_status_color()
    {
        if ($this->input->post()) {
            $this->leads_model->change_status_color($this->input->post());
        }
    }
    public function import()
    {
        $simulate_data  = array();
        $total_imported = 0;
        if ($this->input->post()) {
            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Setup our new file path
                    $newFilePath = TEMP_FOLDER . $_FILES['file_csv']['name'];
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 777);
                    }
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $import_result = true;
                        $fd            = fopen($newFilePath, 'r');
                        $rows          = array();
                        while ($row = fgetcsv($fd)) {
                            $rows[] = $row;
                        }
                        fclose($fd);
                        if (count($rows) <= 1) {
                            set_alert('warning', 'Not enought rows for importing');
                            redirect(admin_url('leads/import'));
                        }
                        unset($rows[0]);
                        if ($this->input->post('simulate')) {
                            if (count($rows) > 500) {
                                set_alert('warning', 'Recommended splitting the CSV file into smaller files. Our recomendation is 500 row, your CSV file has ' . count($rows));
                            }
                        }
                        $db_temp_fields = $this->db->list_fields('tblleads');
                        $db_fields      = array();
                        foreach ($db_temp_fields as $field) {
                            if (in_array($field, $this->not_importable_leads_fields)) {
                                continue;
                            }
                            $db_fields[] = $field;
                        }
                        $custom_fields = get_custom_fields('leads');
                        $_row_simulate = 0;
                        foreach ($rows as $row) {
                            // do for db fields
                            $insert = array();
                            for ($i = 0; $i < count($db_fields); $i++) {
                                // Avoid errors on nema field. is required in database
                                if ($db_fields[$i] == 'name' && $row[$i] == '') {
                                    $row[$i] = '/';
                                }
                                $insert[$db_fields[$i]] = $row[$i];
                            }
                            if (count($insert) > 0) {
                                $total_imported++;
                                $insert['dateadded']   = date('Y-m-d H:i:s');
                                $insert['addedfrom']   = get_staff_user_id();
                                $insert['lastcontact'] = NULL;
                                $insert['status']      = $this->input->post('status');
                                $insert['source']      = $this->input->post('source');
                                if($this->input->post('responsible')){
                                    $insert['assigned'] = $this->input->post('responsible');
                                }
                                if (!$this->input->post('simulate')) {
                                    $this->db->insert('tblleads', $insert);
                                    $leadid = $this->db->insert_id();
                                } else {
                                    $simulate_data[$_row_simulate] = $insert;
                                    $leadid                        = true;
                                }
                                if ($leadid) {
                                    $insert = array();
                                    foreach ($custom_fields as $field) {
                                        if (!$this->input->post('simulate')) {
                                            if ($row[$i] != '') {
                                                $this->db->insert('tblcustomfieldsvalues', array(
                                                    'relid' => $leadid,
                                                    'fieldid' => $field['id'],
                                                    'value' => $row[$i],
                                                    'fieldto' => 'leads'
                                                ));
                                            }
                                        } else {
                                            $simulate_data[$_row_simulate][$field['name']] = $row[$i];
                                        }
                                        $i++;
                                    }
                                }
                            }
                            $_row_simulate++;
                            if ($this->input->post('simulate') && $_row_simulate >= 100) {
                                break;
                            }
                        }
                        unlink($newFilePath);
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
        }
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();

        $this->load->model('staff_model');
        $data['members']  = $this->staff_model->get('',1);
        if (count($simulate_data) > 0) {
            $data['simulate'] = $simulate_data;
        }
        if (isset($import_result)) {
            set_alert('success', _l('import_total_imported', $total_imported));
        }
        $data['not_importable'] = $this->not_importable_leads_fields;
        $data['title']          = 'Import';
        $this->load->view('admin/leads/import', $data);
    }
    function email_exists()
    {
        if ($this->input->post()) {
            // First we need to check if the email is the same
            $leadid = $this->input->post('leadid');
            if ($leadid != 'undefined') {
                $this->db->where('id', $leadid);
                $_current_email = $this->db->get('tblleads')->row();
                if ($_current_email->email == $this->input->post('email')) {
                    echo json_encode(true);
                    die();
                }
            }
            $exists = total_rows('tblleads', array(
                'email' => $this->input->post('email')
            ));
            if ($exists > 0) {
                echo 'false';
            } else {
                echo 'true';
            }
        }
    }
}
