    <div class="panel_s">
        <div class="panel-body">
            <h4 class="bold no-margin"><?php echo _l('clients_contracts'); ?></h4>
        </div>
    </div>
    <div class="panel_s">
       <div class="panel-body">
        <div class="table-responsive">
            <table class="table dt-table">
             <thead>
                <tr>
                    <th><?php echo _l('clients_contracts_dt_subject'); ?></th>
                    <th><?php echo _l('clients_contracts_type'); ?></th>
                    <th><?php echo _l('clients_contracts_dt_start_date'); ?></th>
                    <th><?php echo _l('clients_contracts_dt_end_date'); ?></th>
                    <th><?php echo _l('contract_attachments'); ?></th>
                    <?php
                    $custom_fields = get_custom_fields('contracts',array('show_on_client_portal'=>1));
                    foreach($custom_fields as $field){ ?>
                    <th><?php echo $field['name']; ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($contracts as $contract){
                    $expiry_class = '';
                    if (!empty($contract['dateend'])) {
                        $_date_end = date('Y-m-d', strtotime($contract['dateend']));
                        if ($_date_end < date('Y-m-d')) {
                            $expiry_class = 'alert-danger';
                        }
                    }
                    ?>
                    <tr class="<?php echo $expiry_class; ?>">
                        <td><?php echo $contract['subject']; ?></td>
                        <td><?php echo $contract['type_name']; ?></td>
                        <td><?php echo _d($contract['datestart']); ?></td>
                        <td><?php echo _d($contract['dateend']); ?></td>
                        <td>
                            <?php foreach($contract['attachments'] as $attachment){ ?>
                            <div class="mbot5"><i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i><a href="<?php echo site_url('download/file/contract/' . $attachment['id']); ?>"><?php echo $attachment['file_name']; ?></a></div>
                            <?php } ?>
                        </td>
                        <?php foreach($custom_fields as $field){ ?>
                        <td><?php echo get_custom_field_value($contract['id'],$field['id'],'contracts'); ?></td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
