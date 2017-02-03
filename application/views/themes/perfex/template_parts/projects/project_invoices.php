        <div class="table-responsive">
            <table class="table dt-table">
             <thead>
                <tr>
                    <th><?php echo _l('clients_invoice_dt_number'); ?></th>
                    <th><?php echo _l('clients_invoice_dt_date'); ?></th>
                    <th><?php echo _l('clients_invoice_dt_duedate'); ?></th>
                    <th><?php echo _l('clients_invoice_dt_amount'); ?></th>
                    <th><?php echo _l('clients_invoice_dt_status'); ?></th>
                    <?php
                    $custom_fields = get_custom_fields('invoice',array('show_on_client_portal'=>1));
                    foreach($custom_fields as $field){ ?>
                    <th><?php echo $field['name']; ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($invoices as $invoice){ ?>
                <tr>
                    <td><a href="<?php echo site_url('viewinvoice/' . $invoice['id'] . '/' . $invoice['hash']); ?>"><?php echo format_invoice_number($invoice['id']); ?></a></td>
                    <td><?php echo _d($invoice['date']); ?></td>
                    <td><?php echo _d($invoice['duedate']); ?></td>
                    <td><?php echo format_money($invoice['total'], $invoice['symbol']);; ?></td>
                    <td><?php echo format_invoice_status($invoice['status'], 'pull-left', true); ?></td>
                    <?php foreach($custom_fields as $field){ ?>
                    <td><?php echo get_custom_field_value($invoice['id'],$field['id'],'invoice'); ?></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
