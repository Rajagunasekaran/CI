<div class="row">
	<?php foreach($ticket_statuses as $status){
		if($status['isdefault'] == 1){ ?>
		<a href="<?php echo site_url('clients/tickets/'.$status['ticketstatusid']); ?>">
			<div class="col-md-3 ticket-status-front">
				<div class="panel_s">
					<div class="panel-body text-center">
						<span class="front-ticket-number">
							<?php echo total_rows('tbltickets',array('userid'=>get_client_user_id(),'status'=>$status['ticketstatusid'])); ?>
						</span>
					</div>
					<div class="panel-footer text-center">
						<span style="color:<?php echo $status['statuscolor']; ?>"><?php echo $status['name']; ?></span>
					</div>
				</div>
			</div>
		</a>
		<?php } ?>
		<?php } ?>
	</div>
	<div class="panel_s">
		<div class="panel-heading">
			<?php echo _l('clients_tickets_heading'); ?>
		</div>
		<div class="panel-body">
			<a href="<?php echo site_url('clients/open_ticket'); ?>" class="btn btn-info pull-right mbot15"><?php echo _l('clients_ticket_open_subject'); ?></a>
			<div class="clearfix"></div>
			<div class="table-responsive">
				<table class="table dt-table">
					<thead>
						<th width="10%"><?php echo _l('clients_tickets_dt_number'); ?></th>
						<th><?php echo _l('clients_tickets_dt_subject'); ?></th>
						<th><?php echo _l('clients_tickets_dt_department'); ?></th>
						<?php if(get_option('services') == 1){ ?>
						<th><?php echo _l('clients_tickets_dt_service'); ?></th>
						<?php } ?>
						<th><?php echo _l('clients_tickets_dt_status'); ?></th>
						<th><?php echo _l('clients_tickets_dt_last_reply'); ?></th>
					</thead>
					<tbody>
						<?php foreach($tickets as $ticket){ ?>
							<tr>
								<td># <?php echo $ticket['ticketid']; ?></td>
								<td><?php echo $ticket['subject']; ?></td>
								<td><?php echo $ticket['department_name']; ?></td>
								<?php if(get_option('services') == 1){ ?>
								<td><?php echo $ticket['service_name']; ?></td>
								<?php } ?>
								<td>
									<span class="label pull-right" style="background:<?php echo $ticket['statuscolor']; ?>">
										<?php echo $ticket['status_name']; ?></span>
									</td>
									<td>
										<?php
										if ($ticket['lastreply'] == NULL) {
											echo _l('client_no_reply');
										} else {
											echo _dt($ticket['lastreply']);
										}
										?>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>

				</div>
			</div>

