                     <div class="row">
                      <?php echo form_open($this->uri->uri_string(),array('class'=>'client-form')); ?>
                      <div class="col-md-12">
                       <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                         <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
                          <?php echo _l( 'customer_profile_details'); ?>
                        </a>
                      </li>
                      <li role="presentation">
                       <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">
                        <?php echo _l( 'billing_shipping'); ?>
                      </a>
                    </li>
                    <li role="presentation">
                     <a href="#client_permissions" aria-controler="client_permissions" role="tab" data-toggle="tab">
                      <?php echo _l('customer_permissions'); ?>
                    </a>
                  </li>
                  <li role="presentation">
                   <a href="#client_advanced" aria-controler="client_advanced" role="tab" data-toggle="tab">
                    <?php echo _l('advanced_options'); ?>
                  </a>
                </li>
              </ul>
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane ptop10 active" id="contact_info">
                 <div class="row">
                  <div class="col-md-4">
                   <?php $value=( isset($client) ? $client->company : ''); ?>
                   <?php echo render_input( 'company', 'client_company',$value); ?>
                   <?php $value=( isset($client) ? $client->firstname : ''); ?>
                   <?php echo render_input( 'firstname', 'client_firstname',$value); ?>
                   <?php $value=( isset($client) ? $client->lastname : ''); ?>
                   <?php echo render_input( 'lastname', 'client_lastname',$value); ?>
                   <?php $value=( isset($client) ? $client->email : ''); ?>
                   <?php echo render_input( 'email', 'client_email',$value, 'email'); ?>
                   <?php $value=( isset($client) ? $client->phonenumber : ''); ?>
                   <?php echo render_input( 'phonenumber', 'client_phonenumber',$value); ?>
                   <?php $value=( isset($client) ? $client->vat : ''); ?>
                   <?php echo render_input( 'vat', 'client_vat_number',$value); ?>

                   <div class="client_password_set_wrapper">
                    <label for="password" class="control-label">
                     <?php echo _l( 'client_password'); ?>
                   </label>
                   <div class="input-group">
                     <input type="text" class="form-control password" id="password" name="password">
                     <span class="input-group-addon">
                      <a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
                    </span>
                  </div>
                  <?php if(isset($client) && $client->last_password_change != NULL){ ?>
                  <p class="text-muted">
                   <?php echo _l( 'client_password_change_populate_note'); ?>
                 </p>
                 <?php echo _l( 'client_password_last_changed'); ?>
                 <?php echo time_ago($client->last_password_change); ?>
                 <?php } ?>
               </div>
               <?php if(!isset($client)){ ?>
               <div class="checkbox checkbox-primary">
                <input type="checkbox" name="donotsendwelcomeemail">
                <label>
                 <?php echo _l( 'client_do_not_send_welcome_email'); ?>
               </label>
             </div>
             <?php } ?>
             <div class="checkbox checkbox-primary">
              <input type="checkbox" name="send_set_password_email">
              <label>
               <?php echo _l( 'client_send_set_password_email'); ?>
             </label>
           </div>
         </div>
         <div class="col-md-4">
           <?php $value=( isset($client) ? $client->address : ''); ?>
           <?php echo render_input( 'address', 'client_address',$value); ?>
           <?php $value=( isset($client) ? $client->city : ''); ?>
           <?php echo render_input( 'city', 'client_city',$value); ?>
           <?php $value=( isset($client) ? $client->state : ''); ?>
           <?php echo render_input( 'state', 'client_state',$value); ?>
           <?php $value=( isset($client) ? $client->zip : ''); ?>
           <?php echo render_input( 'zip', 'client_postal_code',$value); ?>

           <div class="form-group">
            <label for="country" class="control-label">
             <?php if(isset($client)){ if(file_exists(FCPATH . 'assets/images/country-flags/'.$client->iso2 . '.png')){ ?>
             <img src="<?php echo site_url('assets/images/country-flags/'.$client->iso2 . '.png'); ?>" alt="<?php echo $client->short_name; ?>">
             <?php } } ?> Country
           </label>
           <select name="country" class="form-control selectpicker" id="country" data-live-search="true">
             <option value=""></option>
             <?php
             $countries= get_all_countries();
             $customer_default_country = get_option('customer_default_country');
             foreach($countries as $country){ $selected='' ;
             if(isset($client)){
              if($client->country == $country['country_id']){ $selected = 'selected'; }
            } else {
              if($country['country_id'] == $customer_default_country){
               $selected = 'selected';
             }
           } ?>
           <option value="<?php echo $country['country_id']; ?>" <?php echo $selected; ?>>
            <?php echo $country[ 'short_name']; ?>
          </option>
          <?php } ?>
        </select>
      </div>
      <?php $value=( isset($client) ? $client->latitude : ''); ?>
      <?php echo render_input( 'latitude', 'customer_latitude',$value); ?>
      <?php $value=( isset($client) ? $client->longitude : ''); ?>
      <?php echo render_input( 'longitude', 'customer_longitude',$value); ?>
    </div>
    <div class="col-md-4">
     <?php $rel_id=( isset($client) ? $client->userid : false); ?>
     <?php echo render_custom_fields( 'customers',$rel_id); ?>
   </div>
 </div>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="billing_and_shipping">
 <div class="row">
  <div class="col-md-12">
   <div class="row">
    <div class="col-md-6">
     <h4><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="label label-primary"><?php echo _l('customer_billing_same_as_profile'); ?></small></a></h4>
     <?php $value=( isset($client) ? $client->billing_street : ''); ?>
     <?php echo render_input( 'billing_street', 'billing_street',$value); ?>
     <?php $value=( isset($client) ? $client->billing_city : ''); ?>
     <?php echo render_input( 'billing_city', 'billing_city',$value); ?>
     <?php $value=( isset($client) ? $client->billing_state : ''); ?>
     <?php echo render_input( 'billing_state', 'billing_state',$value); ?>
     <?php $value=( isset($client) ? $client->billing_zip : ''); ?>
     <?php echo render_input( 'billing_zip', 'billing_zip',$value); ?>
     <?php $selected=( isset($client) ? $client->billing_country : $customer_default_country ); ?>
     <?php echo render_select( 'billing_country',$countries,array( 'country_id',array( 'short_name')), 'billing_country',$selected); ?>
   </div>
   <div class="col-md-6">
     <h4><?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="label label-primary"><?php echo _l('customer_billing_copy'); ?></small></a></h4>
     <?php $value=( isset($client) ? $client->shipping_street : ''); ?>
     <?php echo render_input( 'shipping_street', 'shipping_street',$value); ?>
     <?php $value=( isset($client) ? $client->shipping_city : ''); ?>
     <?php echo render_input( 'shipping_city', 'shipping_city',$value); ?>
     <?php $value=( isset($client) ? $client->shipping_state : ''); ?>
     <?php echo render_input( 'shipping_state', 'shipping_state',$value); ?>
     <?php $value=( isset($client) ? $client->shipping_zip : ''); ?>
     <?php echo render_input( 'shipping_zip', 'shipping_zip',$value); ?>
     <?php $selected=( isset($client) ? $client->shipping_country : $customer_default_country ); ?>
     <?php echo render_select( 'shipping_country',$countries,array( 'country_id',array( 'short_name')), 'shipping_country',$selected); ?>
   </div>
   <?php if(isset($client) &&
   (total_rows('tblinvoices',array('clientid'=>$client->userid)) > 0 || total_rows('tblestimates',array('clientid'=>$client->userid)) > 0)){ ?>
   <div class="col-md-12">
     <div class="alert alert-warning">
      <div class="checkbox checkbox-primary">
       <input type="checkbox" name="update_all_other_transactions">
       <label>
        <?php echo _l('customer_update_address_info_on_invoices'); ?><br />
      </label>
    </div>
    <b><?php echo _l('customer_update_address_info_on_invoices_help'); ?></b>
  </div>
</div>
<?php } ?>
</div>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="client_advanced">
 <?php
 $date_formats = get_available_date_formats();
 ?>
 <div class="row">
  <div class="col-md-6">
   <div class="form-group">
    <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
    </label>
    <select name="default_language" id="default_language" class="form-control selectpicker">
     <option value=""><?php echo _l('system_default_string'); ?></option>
     <?php foreach(list_folders(APPPATH .'language') as $language){
      $selected = '';
      if(isset($client)){
       if($client->default_language == $language){
        $selected = 'selected';
      }
    }
    ?>
    <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
    <?php } ?>
  </select>
</div>
<?php
$selected = '';
foreach($currencies as $currency){
  if(isset($client)){
   if($currency['id'] == $client->default_currency){
    $selected = $currency['id'];
  }
}
}
?>
<?php echo render_select('default_currency',$currencies,array('id','symbol','name'),'invoice_add_edit_currency',$selected,array('data-none-selected-text'=>_l('system_default_string'))); ?>
<?php
$selected = array();
if(isset($customer_groups)){
  foreach($customer_groups as $group){
   array_push($selected,$group['groupid']);
 }
}
echo render_select('groups_in[]',$groups,array('id','name'),'customer_groups',$selected,array('multiple'=>true));
?>
</div>
</div>
</div>
<div role="tabpanel" class="tab-pane ptop10" id="client_permissions">
 <?php foreach($customer_permissions as $permission){ ?>
 <div class="col-md-6 row">
  <div class="row">
   <div class="col-md-6 mtop10 border-right">
    <span class="bold"><?php echo $permission['name']; ?></span>
  </div>
  <div class="col-md-6 mtop10">
    <input type="checkbox" class="switch-box" <?php if(isset($client) && has_customer_permission($permission['short_name'],$client->userid)){echo 'checked';} else if(!isset($client)){echo 'checked';} ?>  value="<?php echo $permission['id']; ?>" data-size="mini" name="permissions[]">
  </div>
</div>
</div>
<div class="clearfix">  </div>
<?php } ?>
</div>
<button type="submit" class="btn btn-info mtop20">
 <?php echo _l( 'submit'); ?>
</button>
</div>
</div>
<?php echo form_close(); ?>
</div>
