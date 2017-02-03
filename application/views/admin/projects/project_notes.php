<p class="text-muted"><?php echo _l('project_note_private'); ?></p>
<?php echo form_open(admin_url('projects/save_note/'.$project->id)); ?>
<?php $this->load->view('admin/editor/template',array('name'=>'content','contents'=>$staff_notes)); ?>
<button type="submit" class="btn btn-info"><?php echo _l('project_save_note'); ?></button>
<?php echo form_close(); ?>
