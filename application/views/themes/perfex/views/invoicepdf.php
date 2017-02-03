<?php
// Tag - used in BULK pdf exporter
if ($tag != '') {
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetDrawColor(245, 245, 245);
    $pdf->SetXY(0, 0);
    $pdf->SetFont('freesans', 'B', 15);
    $pdf->SetTextColor(0);
    $pdf->SetLineWidth(0.75);
    $pdf->StartTransform();
    $pdf->Rotate(-35, 109, 235);
    $pdf->Cell(100, 1, strtoupper($tag), 'TB', 0, 'C', '1');
    $pdf->StopTransform();
    $pdf->SetFont('freesans', '', 10);
    $pdf->setX(10);
    $pdf->setY(10);
}
if (get_option('show_status_on_pdf_ei') == 1) {
    // Top
    if ($status == 1) {
        $_status = _l('invoice_status_unpaid');
        $pdf->SetFillColor(252, 45, 66);
    } else if ($status == 2) {
        $_status = _l('invoice_status_paid');
        $pdf->SetFillColor(37, 155, 36);
    } else if ($status == 3) {
        $_status = _l('invoice_status_not_paid_completely');
        $pdf->SetFillColor(255, 111, 0);
    } else if($status == 4) {
        $_status = _l('invoice_status_overdue');
        $pdf->SetFillColor(255, 111, 0);
    } else {
         $_status = _l('invoice_status_cancelled');
         $pdf->SetFillColor(252, 45, 66);
    }
    $pdf->SetTextColor(255);
    $pdf->Cell(50, 10, strtoupper($_status), 0, 1, 'C', '1');
    $pdf->SetTextColor(0);
}
$html = '';
$html .= '<span style="font-weight:bold;font-size:20px;">' . _l('invoice_pdf_heading') . '</span><br />';
$html .= '<b style="color:#6a6c6f"># ' . $invoice_number . '</b>';
// Show link only if is not paid status
if ($status != 2) {
    $pdf->Write(10, _l('view_invoice_pdf_link_pay'), site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash), false, 'L', true);
}
$pdf->writeHTMLCell(0, 0, 0, 20, $html, 0, 1, false, true, 'R', false);
$pdf->Ln(6);
$pdf->setJPEGQuality(100);
$custom_pdf_logo_image_url = get_option('custom_pdf_logo_image_url');
if ($custom_pdf_logo_image_url == '') {
    $pdf->writeHTMLCell(35, 0, 10, 30, '<a href="' . site_url() . '"><img src="' . site_url('uploads/company/' . get_option('company_logo')) . '"></a>', 0, 1, false, true, 'L', false);
} else {
    $pdf->writeHTMLCell(35, 0, 10, 30, '<a href="' . site_url() . '"><img src="' . $custom_pdf_logo_image_url . '"></a>', 0, 1, false, true, 'L', false);
}
// Get Y position for the separation
$y            = $pdf->getY();
$invoice_info = '<b>' . get_option('invoice_company_name') . '</b><br />';
$invoice_info .= get_option('invoice_company_address') . '<br/>';
$invoice_info .= get_option('invoice_company_city') . ', ';
$invoice_info .= get_option('invoice_company_country_code') . ' ';
$invoice_info .= get_option('invoice_company_postal_code') . ' ';

// check for company custom fields
$custom_company_fields = get_company_custom_fields();
if (count($custom_company_fields) > 0) {
    $invoice_info .= '<br />';
}
foreach ($custom_company_fields as $field) {
    $invoice_info .= $field['label'] . ': ' . $field['value'] . '<br />';
}

$pdf->writeHTMLCell(91, '', '', $y, $invoice_info, 0, 0, false, true, 'J', true);

// Bill to
$client_details = '<b>' . _l('invoice_bill_to') . '</b><br />';
$client_details .= $invoice->client->company . '<br />';
$client_details .= $invoice->billing_street . '<br />' . $invoice->billing_city . ', ' . $invoice->billing_state . '<br />' . get_country_short_name($invoice->billing_country) . ',' . $invoice->billing_zip . '<br />';
if (!empty($invoice->client->vat)) {
    $client_details .= _l('invoice_vat') . ' ' . $invoice->client->vat . '<br />';
}

$inserted_customer_custom_field = false;
// check for invoice custom fields which is checked show on pdf
$pdf_custom_fields              = get_custom_fields('customers', array(
    'show_on_pdf' => 1
));
foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($invoice->clientid, $field['id'], 'customers');
    if ($value == '') {
        continue;
    }
    $inserted_customer_custom_field = true;
    $client_details .= $field['name'] . ': ' . $value . '<br />';
}
if ($inserted_customer_custom_field) {
    $client_details .= '<br />';
}
$pdf->writeHTMLCell(99, '', '', '', $client_details, 0, 1, false, true, 'R', true);

// ship to to
if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) {
    $pdf->Ln(5);
    $shipping_details = '<b>' . _l('ship_to') . '</b><br />';
    $shipping_details .= $invoice->shipping_street . '<br />' . $invoice->shipping_city . ', ' . $invoice->shipping_city . '<br />' . get_country_short_name($invoice->shipping_country) . ',' . $invoice->shipping_zip;
    $pdf->writeHTMLCell(191, '', '', '', $shipping_details, 0, 1, false, true, 'R', true);
    $pdf->Ln(5);
}

// Dates
$pdf->Cell(0, 0, _l('invoice_data_date') . ' ' . _d($invoice->date), 0, 1, 'R', 0, '', 0);
if (!empty($invoice->duedate)) {
    $pdf->Cell(0, 0, _l('invoice_data_duedate') . ' ' . _d($invoice->duedate), 0, 1, 'R', 0, '', 0);
}
if ($invoice->sale_agent != 0) {
    if (get_option('show_sale_agent_on_invoices') == 1) {
        $pdf->Cell(0, 0, _l('sale_agent_string') . ': ' . get_staff_full_name($invoice->sale_agent), 0, 1, 'R', 0, '', 0);
    }
}

// check for invoice custom fields which is checked show on pdf
$pdf_custom_fields = get_custom_fields('invoice', array(
    'show_on_pdf' => 1
));
foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
    if ($value == '') {
        continue;
    }
    $pdf->Cell(0, 0, $field['name'] . ': ' . $value, 0, 1, 'R', 0, '', 0);
}
// The Table
$pdf->Ln(5);
$item_width = 38;
// If show item taxes is disabled in PDF we should increase the item width table heading
if (get_option('show_tax_per_item') == 0) {
    $item_width = $item_width + 15;
}
// Header
$qty_heading = _l('invoice_table_quantity_heading');
if($invoice->show_quantity_as == 2){
    $qty_heading = _l('invoice_table_hours_heading');
} else if($invoice->show_quantity_as == 3){
    $qty_heading = _l('invoice_table_quantity_heading') .'/'._l('invoice_table_hours_heading');
}
$tblhtml = '
<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
    <tr height="30" bgcolor="#323a45" style="color:#fff;text-align:center;">
        <th width="5%;">#</th>
        <th width="' . $item_width . '%" align="left">' . _l('invoice_table_item_heading') . '</th>
        <th width="12%">' . $qty_heading . '</th>
        <th width="15%">' . _l('invoice_table_rate_heading') . '</th>';
if (get_option('show_tax_per_item') == 1) {
        $tblhtml .= '<th width="15%">' . _l('invoice_table_tax_heading') . '</th>';
}
$tblhtml .= '<th width="15%">' . _l('invoice_table_amount_heading') . '</th>
</tr>';
// Items
$taxes             = array();
$_calculated_taxes = array();
$i                 = 1;
$tblhtml .= '<tbody>';
foreach ($invoice->items as $item) {
    $tblhtml .= '<tr style="text-align:center;font-size:9px;">';
    $tblhtml .= '<td>' . $i . '</td>';
    $tblhtml .= '<td align="left">' . $item['description'] . '<br /><span style="color:#777">' . $item['long_description'] . '</span></td>';
    $tblhtml .= '<td>' . floatVal($item['qty']) . '</td>';
    $tblhtml .= '<td>' . _format_number($item['rate']) . '</td>';
    if (get_option('show_tax_per_item') == 1) {
        $tblhtml .= '<td>';
    }
    $item_taxes = get_invoice_item_taxes($item['id']);
    if (count($item_taxes) > 0) {
        foreach ($item_taxes as $tax) {
            $calc_tax     = 0;
            $tax_not_calc = false;
            if (!in_array($tax['taxname'], $_calculated_taxes)) {
                array_push($_calculated_taxes, $tax['taxname']);
                $tax_not_calc = true;
            }
            if ($tax_not_calc == true) {
                $taxes[$tax['taxname']]          = array();
                $taxes[$tax['taxname']]['total'] = array();
                array_push($taxes[$tax['taxname']]['total'], (($item['qty'] * $item['rate']) / 100 * $tax['taxrate']));
                $taxes[$tax['taxname']]['tax_name'] = $tax['taxname'];
                $taxes[$tax['taxname']]['taxrate']  = $tax['taxrate'];
            } else {
                array_push($taxes[$tax['taxname']]['total'], (($item['qty'] * $item['rate']) / 100 * $tax['taxrate']));
            }
            // Replace the DB separator for space
            if (get_option('show_tax_per_item') == 1) {
                $tblhtml .= str_replace('|', ' ', $tax['taxname']) . '%, ';
            }
        }
        if (get_option('show_tax_per_item') == 1) {
            // Remove the last , and white space
            $tblhtml = substr($tblhtml, 0, -2);
        }
    } else {
        if (get_option('show_tax_per_item') == 1) {
            $tblhtml .= '0%';
        }
    }
    if (get_option('show_tax_per_item') == 1) {
        $tblhtml .= '</td>';
    }
    $i++;
    $tblhtml .= '<td>' . _format_number(($item['qty'] * $item['rate'])) . '</td>';
    $tblhtml .= '</tr>';
}
$tblhtml .= '</tbody>';
$tblhtml .= '</table>';
$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->Ln(8);
$tbltotal = '';
$tbltotal .= '<table cellpadding="6">';
$tbltotal .= '
<tr>
    <td align="right" width="80%">' . _l('invoice_subtotal') . '</td>
    <td align="right" width="20%">' . _format_number($invoice->subtotal) . '</td>
</tr>';
if ($invoice->discount_percent != 0) {
    $tbltotal .= '
    <tr>
        <td align="right" width="80%">' . _l('invoice_discount') . '(' . $invoice->discount_percent . '%)' . '</td>
        <td align="right" width="20%">-' . _format_number($invoice->discount_total) . '</td>
    </tr>';
}
foreach ($taxes as $tax) {
    $total = array_sum($tax['total']);
    if ($invoice->discount_percent != 0 && $invoice->discount_type == 'before_tax') {
        $total_tax_calculated = ($total * $invoice->discount_percent) / 100;
        $total                = ($total - $total_tax_calculated);
    }
    // The tax is in format TAXNAME|20
    $_tax_name = explode('|', $tax['tax_name']);
    $tbltotal .= '<tr>
    <td align="right" width="80%">' . $_tax_name[0] . '(' . _format_number($tax['taxrate']) . '%)' . '</td>
    <td align="right" width="20%">' . _format_number($total, $invoice->symbol) . '</td>
</tr>';
}
if ($invoice->adjustment != '0.00') {
    $tbltotal .= '<tr>
    <td align="right" width="80%">' . _l('invoice_adjustment') . '</td>
    <td align="right" width="20%">' . _format_number($invoice->adjustment) . '</td>
</tr>';
}
$tbltotal .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="80%">' . _l('invoice_total') . '</td>
    <td align="right" width="20%">' . format_money($invoice->total, $invoice->symbol) . '</td>
</tr>';

if ($invoice->status == 3) {
    $tbltotal .= '
    <tr>
        <td align="right" width="80%">' . _l('invoice_total_paid') . '</td>
        <td align="right" width="20%">' . format_money(sum_from_table('tblinvoicepaymentrecords', array(
        'field' => 'amount',
        'where' => array(
            'invoiceid' => $invoice->id
        )
    )), $invoice->symbol) . '</td>
    </tr>
    <tr style="background-color:#f0f0f0;">
       <td align="right" width="80%">' . _l('invoice_amount_due') . '</td>
       <td align="right" width="20%">' . format_money(get_invoice_total_left_to_pay($invoice->id, $invoice->total), $invoice->symbol) . '</td>
   </tr>';
}
$tbltotal .= '</table>';
$pdf->writeHTML($tbltotal, true, false, false, false, '');

if (get_option('total_to_words_enabled') == 1) {
    $CI->load->library('numberword');
    // Set the font bold
    $pdf->SetFont('freesans', 'B', 10);
    $pdf->Cell(0, 0, _l('num_word') . ': ' . $CI->numberword->convert($invoice->total, $invoice->currency_name), 0, 1, 'C', 0, '', 0);
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont('freesans', '', 10);
    $pdf->Ln(4);
}

if (count($invoice->payments) > 0) {
    $pdf->Ln(4);
    $border = 'border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid; 1px solid black;';
    $pdf->SetFont('freesans', 'B', 10);
    $pdf->Cell(0, 0, _l('invoice_received_payments'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont('freesans', '', 10);
    $pdf->Ln(4);
    $tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
        <tr height="20"  style="color:#000;border:1px solid #000;">
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_number_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_mode_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_date_heading') . '</th>
        <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_amount_heading') . '</th>
    </tr>';
    $tblhtml .= '<tbody>';
    foreach ($invoice->payments as $payment) {
        $tblhtml .= '
            <tr>
            <td>' . $payment['paymentid'] . '</td>
            <td>' . $payment['name'] . '</td>
            <td>' . _d($payment['date']) . '</td>
            <td>' . format_money($payment['amount'], $invoice->symbol) . '</td>
            </tr>
        ';
    }
    $tblhtml .= '</tbody>';
    $tblhtml .= '</table>';
    $pdf->writeHTML($tblhtml, true, false, false, false, '');
}
if (!empty($invoice->clientnote)) {
    $pdf->Ln(4);
    $pdf->MultiCell(190, 0, _l('invoice_note') . ' ' . clear_textarea_breaks($invoice->clientnote), 0, 'L');
}
if (!empty($invoice->terms)) {
    $pdf->Ln(4);
    $pdf->Cell(0, 0, _l('terms_and_conditions'), 0, 1, 'L', 0, '', 0);
    $pdf->Ln(2);
    $pdf->MultiCell(190, 0, clear_textarea_breaks($invoice->terms), 0, 'L');
}
