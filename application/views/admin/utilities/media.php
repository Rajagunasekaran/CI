<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_open_multipart(admin_url('utilities/upload_media'),array('class'=>'dropzone','id'=>'media-upload')); ?>
						<input type="file" name="file" multiple />
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<h4 class="bold no-margin"><?php echo _l('media_files'); ?></h4>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div id="media-view" class="table-responsive">
							<table class="table table-media dt-table">
								<thead>
									<tr>
										<th><?php echo _l('media_dt_filename'); ?></th>
										<th><?php echo _l('media_dt_last_modified'); ?></th>
										<th><?php echo _l('media_dt_filesize'); ?></th>
										<th><?php echo _l('media_dt_mime_type'); ?></th>
										<th><?php echo _l('options'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$folder                 = list_folders(MEDIA_FOLDER);
									foreach ($folder as $folder) {
										$files = list_files(MEDIA_FOLDER . $folder);
										if ($files) {
											foreach ($files as $file) {
												$_fullpath = MEDIA_FOLDER . $folder . '/' . $file;
												$link      = site_url('media/' . $folder . '/' . $file);
												echo '<tr>';
												echo '<td><i class="' . get_mime_class(get_mime_by_extension($file)) . '"></i> <a href="' . $link . '" target="_blank" download>' . $file . '</a></td>';
												echo '<td>'.date('M dS, Y, g:i a', filemtime($_fullpath)).'</td>';
												echo '<td>'.bytesToSize($_fullpath).'</td>';
												echo '<td>'.get_mime_by_extension($file).'</td>';
												echo '<td>';
												$options = '';
												if(is_image($_fullpath)){
													$base64 = base64_encode(file_get_contents($_fullpath));
													$src    = 'data: ' . get_mime_by_extension($file) . ';base64,' . $base64;
													ob_start();
													?>
													<button type="button" class="btn btn-info btn-icon" data-placement="top" data-html="true" data-toggle="popover" data-content='<img src="<?php
													echo $src;
													?>" class="img img-responsive mbot20">' data-trigger="focus"><i class="fa fa-eye"></i></button>
													<?php
													$options .= ob_get_contents();
													ob_end_clean();
												}
												$options .= '<button type="button" data-toggle="modal" data-return-url="' . admin_url('utilities/media') . '" data-original-file-name="' . $file . '" data-filetype="' . get_mime_by_extension($file) . '" data-path="' . $_fullpath . '" data-target="#send_file" class="btn btn-info btn-icon"><i class="fa fa-envelope"></i></button>';
												$options .= '<a href="#" data-filename="'.$file.'" data-folder="'.$folder.'" class="btn btn-danger btn-icon delete_file"><i class="fa fa-remove"></i></a>';
												echo $options;
												echo '</td>';
												echo '</tr>';
											}
										}
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<?php include_once(APPPATH . 'views/admin/clients/modals/send_file_modal.php'); ?>
<?php init_tail(); ?>
<script>
	$(document).ready(function() {
    // Initialize dropzone
    Dropzone.options.mediaUpload = {
    	paramName: "file",
    	addRemoveLinks: true,
    	maxFilesize: '<?php echo get_option('media_max_file_size_upload'); ?>',
    	accept: function(file, done) {
    		done();
    	},
    	success: function(file, response) {
    		window.location.reload();
    	},
    	init: function() {
    		this.on('removedfile', function(file, response) {
    			var response = $.parseJSON(file.xhr.response);
    			remove_file(response.filename, response.folder, 'undefined', file.name);
    		});
    	}
    };
    $('.delete_file').on('click',function(e){
      e.preventDefault();
      var filename = $(this).data('filename');
      var folder = $(this).data('folder');
      $.post(admin_url + 'utilities/remove_media_file/', {
         filename: filename,
         folder: folder
     }).success(function() {
         window.location.reload();
     });
 });
});

</script>
</body>
</html>
