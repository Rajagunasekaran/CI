<?php
/**
 * Newsfeed post attachments
 * @param  mixed $postid Post ID to add attachments
 * @return array  - Result values
 */
function handle_newsfeed_post_attachments($postid)
{
    $path = NEWSFEED_FOLDER . $postid . '/';
    $CI =& get_instance();
    if (isset($_FILES['file']['name'])) {
        $uploaded_files = false;
        // Get the temp file path
        $tmpFilePath    = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            $type        = $_FILES["file"]["type"];
            // Setup our new file path


            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
            $filename    = unique_filename($path, $_FILES["file"]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $file_uploaded = true;
                $CI->db->insert('tblpostattachments', array(
                    'filename' => $filename,
                    'postid' => $postid,
                    'filetype' => $type,
                    'datecreated' => date('Y-m-d H:i:s')
                ));
            }
        }
        if ($file_uploaded == true) {
            echo json_encode(array(
                'success' => true,
                'postid' => $postid
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'postid' => $postid
            ));
        }
    }
}
function handle_project_file_uploads($project_id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        $path        = PROJECT_ATTACHMENTS_FOLDER . $project_id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Setup our new file path


            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
             $filename    = unique_filename($path, $_FILES["file"]["name"]);
             $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                if (is_client_logged_in()) {
                    $addedfrom = 0;
                } else {
                    $addedfrom = get_staff_user_id();
                }
                $data = array(
                    'project_id' => $project_id,
                    'file_name' => $filename,
                    'filetype' => $_FILES["file"]["type"],
                    'dateadded' => date('Y-m-d H:i:s'),
                    'addedfrom' => $addedfrom
                );
                if(is_client_logged_in()){
                    $data['visible_to_customer'] = 1;
                } else {
                    $data['visible_to_customer'] = ($CI->input->post('visible_to_customer') == 'true' ? 1 : 0);
                }
                $CI->db->insert('tblprojectfiles', $data);
                    $CI->load->model('projects_model');
                    $project = $CI->projects_model->get($project_id);

                    $additional_data = '';
                    $additional_data .= $filename . '<br />'.$_FILES["file"]["type"];
                    $CI->projects_model->log_activity($project_id,'project_activity_uploaded_file',$additional_data,$data['visible_to_customer']);

                $insert_id = $CI->db->insert_id();
                if($insert_id){
                    if(is_client_logged_in()){
                    $members = $CI->projects_model->get_project_members($project_id);
                    foreach($members as $member){
                        add_notification(
                          array(
                              'fromclientid'=>get_client_user_id(),
                              'description'=>_l('not_customer_uploaded_project_file') . ' - ' . $project->name,
                              'link'=>'projects/view/'.$project_id.'?group=project_files',
                              'touserid'=>$member['staff_id'],
                              )
                          );
                    }
                    }
                }
                return true;
            }
        }
    }
    return false;
}
/**
 * Handle contract attachments if any
 * @param  mixed $contractid
 * @return boolean
 */
function handle_contract_attachment($contractid)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        $path        = CONTRACTS_UPLOADS_FOLDER . $contractid . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Setup our new file path


            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
            $filename    = unique_filename($path, $_FILES["file"]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->db->insert('tblcontractattachments', array(
                    'contractid' => $contractid,
                    'file_name' => $filename,
                    'filetype' => $_FILES["file"]["type"],
                    'dateadded' => date('Y-m-d H:i:s')
                ));
                return true;
            }
        }
    }
    return false;
}
/**
 * Handle lead attachments if any
 * @param  mixed $leadid
 * @return boolean
 */
function handle_lead_attachments($leadid)
{
    $CI =& get_instance();
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        $path        = LEAD_ATTACHMENTS_FOLDER . $leadid . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Setup our new file path
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
             $filename    = unique_filename($path, $_FILES["file"]["name"]);
             $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $CI->db->insert('tblleadattachments', array(
                    'leadid' => $leadid,
                    'file_name' => $filename,
                    'filetype' => $_FILES["file"]["type"],
                    'addedfrom' => get_staff_user_id(),
                    'dateadded' => date('Y-m-d H:i:s')
                ));
                $CI->load->model('leads_model');
                $CI->leads_model->log_lead_activity($leadid, _l('not_lead_activity_added_attachment'));
                return true;
            }
        }
    }
    return false;
}
/**
 * Check for task attachment
 * @since Version 1.0.1
 * @param  mixed $taskid
 * @return mixed           false if no attachment || array uploaded attachments
 */
function handle_tasks_attachments($taskid)
{
    $path           = TASKS_ATTACHMENTS_FOLDER . $taskid . '/';
    $uploaded_files = array();
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Setup our new file path


            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
              $filename    = unique_filename($path, $_FILES["file"]["name"]);
               $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                array_push($uploaded_files, array(
                    'filename' => $filename,
                    'filetype' => $_FILES["file"]["type"]
                ));
            }
        }
    }
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }
    return false;
}
/**
 * Invoice attachments
 * @since  Version 1.0.4
 * @param  mixed $invoiceid invoice ID to add attachments
 * @return array  - Result values
 */
function handle_invoice_attachments($invoiceid)
{
    $path = INVOICE_ATTACHMENTS_FOLDER . $invoiceid . '/';
    $CI =& get_instance();
    if (isset($_FILES['file']['name'])) {
        $uploaded_files = false;
        // Get the temp file path
        $tmpFilePath    = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $type        = $_FILES["file"]["type"];
            // Setup our new file path


            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
            $filename    = unique_filename($path, $_FILES["file"]["name"]);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $file_uploaded = true;
                $CI->db->insert('tblinvoiceattachments', array(
                    'file_name' => $filename,
                    'invoiceid' => $invoiceid,
                    'filetype' => $type,
                    'datecreated' => date('Y-m-d H:i:s')
                ));
                $insert_id = $CI->db->insert_id();
                $CI->load->model('invoices_model');
                $CI->invoices_model->log_invoice_activity($invoiceid, _l('invoice_activity_added_attachment'));
            }
        }
        if ($file_uploaded == true) {
            echo json_encode(array(
                'success' => true,
                'invoiceid' => $invoiceid,
                'attachment_id' => $insert_id,
                'filetype' => $type,
                'file_name' => $filename
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'invoiceid' => $invoiceid,
                'file_name' => $filename
            ));
        }
    }
}
/**
 * Client attachments
 * @since  Version 1.0.4
 * @param  mixed $clientid Client ID to add attachments
 * @return array  - Result values
 */
function handle_client_attachments_upload($clientid)
{
    $path = CLIENT_ATTACHMENTS_FOLDER . $clientid . '/';
    $CI =& get_instance();
    if (isset($_FILES['file']['name'])) {
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $type        = $_FILES["file"]["type"];
            // Setup our new file path


            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
             $filename    = unique_filename($path, $_FILES['file']['name']);
              $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI->db->insert('tblclientattachments', array(
                    'file_name' => $filename,
                    'clientid' => $clientid,
                    'filetype' => $type,
                    'datecreated' => date('Y-m-d H:i:s')
                ));
            }
        }
    }
}
function handle_expense_attachments($id)
{
    $path = EXPENSE_ATTACHMENTS_FOLDER . $id . '/';
    $CI =& get_instance();
    if (isset($_FILES['file']['name'])) {
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Setup our new file path
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
             $filename    = $_FILES["file"]["name"];
             $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI->db->where('id', $id);
                $CI->db->update('tblexpenses', array(
                    'attachment' => $filename,
                    'filetype' => $_FILES["file"]["type"]
                ));
            }
        }
    }
}
/**
 * Check for ticket attachment after inserting ticket to database
 * @param  mixed $ticketid
 * @return mixed           false if no attachment || array uploaded attachments
 */
function handle_ticket_attachments($ticketid)
{
    $path           = TICKET_ATTACHMENTS_FOLDER . $ticketid . '/';
    $uploaded_files = array();
    for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
        if ($i <= get_option('maximum_allowed_ticket_attachments')) {
            // Get the temp file path
            $tmpFilePath = $_FILES['attachments']['tmp_name'][$i];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                // Getting file extension
                $path_parts         = pathinfo($_FILES["attachments"]["name"][$i]);
                $extension          = $path_parts['extension'];
                $allowed_extensions = explode('|', get_option('ticket_attachments_file_extensions'));
                // Check for all cases if this extension is allowed
                if (!in_array($extension, $allowed_extensions)) {
                    continue;
                }
                // Setup our new file path
                if (!file_exists($path)) {
                    mkdir($path);
                    fopen($path . 'index.html', 'w');
                }
                 $filename    = unique_filename($path, $_FILES["attachments"]["name"][$i]);
                 $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    array_push($uploaded_files, array(
                        'file_name' => $filename,
                        'filetype' => $_FILES["attachments"]["type"][$i]
                    ));
                }
            }
        }
    }
    if (count($uploaded_files) > 0) {
        return $uploaded_files;
    }
    return false;
}
/**
 * Check for company logo upload
 * @param  mixed $ticketid
 * @return boolean
 */
function handle_company_logo_upload()
{
    if (isset($_FILES['company_logo']['name']) && $_FILES['company_logo']['name'] != '') {
        $path        = COMPANY_FILES_FOLDER . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['company_logo']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts         = pathinfo($_FILES["company_logo"]["name"]);
            $extension          = $path_parts['extension'];
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');
                return false;
            }
            // Setup our new file path
            $filename    = 'logo' . '.' . $extension;
            $newFilePath = $path . $filename;
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_option('company_logo', $filename);
                return true;
            }
        }
    }
    return false;
}
function handle_favicon_upload()
{
    if (isset($_FILES['favicon']['name']) && $_FILES['favicon']['name'] != '') {
        $path        = COMPANY_FILES_FOLDER . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['favicon']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts  = pathinfo($_FILES["favicon"]["name"]);
            $extension   = $path_parts['extension'];
            // Setup our new file path
            $filename    = 'favicon' . '.' . $extension;
            $newFilePath = $path . $filename;
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . 'index.html', 'w');
            }
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                update_option('favicon', $filename);
                return true;
            }
        }
    }
    return false;
}
/**
 * Check for staff profile image
 * @param  mixed $ticketid
 * @return boolean
 */
function handle_staff_profile_image_upload()
{
    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        $path        = STAFF_PROFILE_IMAGES_FOLDER . get_staff_user_id() . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts         = pathinfo($_FILES["profile_image"]["name"]);
            $extension          = $path_parts['extension'];
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');
                return false;
            }
            // Setup our new file path
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . '/index.html', 'w');
            }
            $filename    = unique_filename($path, $_FILES["profile_image"]["name"]);
             $newFilePath = $path . '/' . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $config                   = array();
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'thumb_' . $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width']          = 160;
                $config['height']         = 160;
                $CI->load->library('image_lib', $config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'small_' . $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width']          = 32;
                $config['height']         = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('staffid', get_staff_user_id());
                $CI->db->update('tblstaff', array(
                    'profile_image' => $filename
                ));
                // Remove original image
                unlink($newFilePath);
                return true;
            }
        }
    }
    return false;
}

/**
 * Check for staff profile image
 * @param  mixed $ticketid
 * @return boolean
 */
function handle_client_profile_image_upload()
{
    if (isset($_FILES['profile_image']['name']) && $_FILES['profile_image']['name'] != '') {
        $path        = CLIENT_PROFILE_IMAGES_FOLDER . get_client_user_id() . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['profile_image']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts         = pathinfo($_FILES["profile_image"]["name"]);
            $extension          = $path_parts['extension'];
            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png'
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');
                return false;
            }
            // Setup our new file path
            if (!file_exists($path)) {
                mkdir($path);
                fopen($path . '/index.html', 'w');
            }
             $filename    = unique_filename($path, $_FILES["profile_image"]["name"]);
             $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI =& get_instance();
                $config                   = array();
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'thumb_' . $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width']          = 160;
                $config['height']         = 160;
                $CI->load->library('image_lib', $config);
                $CI->image_lib->resize();
                $CI->image_lib->clear();
                $config['image_library']  = 'gd2';
                $config['source_image']   = $newFilePath;
                $config['new_image']      = 'small_' . $filename;
                $config['maintain_ratio'] = TRUE;
                $config['width']          = 32;
                $config['height']         = 32;
                $CI->image_lib->initialize($config);
                $CI->image_lib->resize();
                $CI->db->where('userid', get_client_user_id());
                $CI->db->update('tblclients', array(
                    'profile_image' => $filename
                ));
                // Remove original image
                unlink($newFilePath);
                return true;
            }
        }
    }
    return false;
}

function handle_project_discussion_comment_attachments($discussion_id,$post_data,$insert_data){
    if (isset($_FILES['file']['name'])) {
       $path = PROJECT_DISCUSSION_ATTACHMENT_FOLDER .$discussion_id . '/';
                 // Get the temp file path
       $tmpFilePath = $_FILES['file']['tmp_name'];
                 // Make sure we have a filepath
       if (!empty($tmpFilePath) && $tmpFilePath != '') {
                 // Setup our new file path
        if (!file_exists($path)) {
            mkdir($path);
            fopen($path . 'index.html', 'w');
        }
        $filename    = unique_filename($path, $_FILES['file']['name']);
        $newFilePath = $path . $filename;
                 // Upload the file into the temp dir
        if (move_uploaded_file($tmpFilePath, $newFilePath)) {
            $insert_data['file_name'] = $filename;
            $insert_data['file_mime_type'] = $post_data['file_mime_type'];
        }
    }
}

return $insert_data;
}
