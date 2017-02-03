<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open_multipart($this->uri->uri_string().'?group='.$group,array('id'=>'settings-form')); ?>
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
			<div class="panel_s">
				<div class="panel-body">
						<ul class="nav nav-tabs no-margin" role="tablist">
						<li class="active">
								<a href="<?php echo admin_url('settings?group=general'); ?>" data-group="general">
								<?php echo _l('settings_group_general'); ?></a>
							</li>
							<li>
								<a href="<?php echo admin_url('settings?group=company'); ?>" data-group="company">
								<?php echo _l('settings_sales_heading_company'); ?></a>
							</li>
							<li>
								<a href="<?php echo admin_url('settings?group=localization'); ?>" data-group="localization">
								<?php echo _l('settings_group_localization'); ?></a>
							</li>
							<li>
								<a href="<?php echo admin_url('settings?group=tickets'); ?>" data-group="tickets">
								<?php echo _l('settings_group_tickets'); ?></a>
							</li>
							<li>
								<a href="<?php echo admin_url('settings?group=sales'); ?>" data-group="sales">
								<?php echo _l('settings_group_sales'); ?></a>
							</li>
							<?php if(get_staff_user_id() == 1){ ?>
							<li>
								<a href="<?php echo admin_url('settings?group=online_payment_modes'); ?>" data-group="online_payment_modes">
								<?php echo _l('settings_group_online_payment_modes'); ?></a>
							</li>
							<?php } ?>
							<li>
								<a href="<?php echo admin_url('settings?group=email'); ?>" data-group="email">
								<?php echo _l('settings_group_email'); ?></a>
							</li>
							<li>
								<a href="<?php echo admin_url('settings?group=clients'); ?>" data-group="clients">
								<?php echo _l('settings_group_clients'); ?></a>
							</li>
							<li>
							<a href="<?php echo admin_url('settings?group=calendar'); ?>" data-group="calendar">
								<?php echo _l('settings_calendar'); ?></a>
							</li>
							<li>
								<a href="<?php echo admin_url('settings?group=cronjob'); ?>" data-group="cronjob">
								<?php echo _l('settings_group_cronjob'); ?></a>
							</li>
							<li>
								<a href="<?php echo admin_url('settings?group=misc'); ?>" data-group="misc">
								<?php echo _l('settings_group_misc'); ?></a>
							</li>
						</ul>
				</div>
			</div>
				<div class="panel_s">
					<div class="panel-body">
						<?php echo $group_view; ?>
					</div>
				</div>
			</div>
			<div class="col-md-12 text-left">
				<button type="submit" class="btn btn-info"><?php echo _l('settings_save'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
