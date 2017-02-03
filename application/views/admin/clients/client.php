<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
         <div class="col-md-12">
          <?php if(isset($client) && $client->leadid != NULL){ ?>
          <div class="alert alert-info">
            <a href="#" onclick="init_lead(<?php echo $client->leadid; ?>); return false;"><?php echo _l('customer_from_lead',_l('lead')); ?></a>
         </div>
         <?php } ?>
         <div class="panel_s no-margin">
            <div class="panel-body">
               <?php $this->load->view('admin/clients/tabs'); ?>
            </div>
         </div>
         <div class="panel_s">
            <div class="panel-heading">
               <?php if(isset($client)){ ?>
               <img src="<?php echo client_profile_image_url($client->userid,'thumb'); ?>" class="pull-left client-profile-image-small">
               <?php } ?>
               <span class="client-profile-company"><?php echo $title; ?></span>
               <div class="clearfix"></div>
            </div>
            <div class="panel-body">
               <?php if(isset($client)){ ?>
               <?php echo form_hidden( 'isedit'); ?>
               <?php echo form_hidden( 'userid',$client->userid); ?>
               <div class="clearfix"></div>
               <?php } ?>
               <div>
                  <div class="tab-content">
                    <?php $this->load->view('admin/clients/groups/'.$group); ?>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </div>
</div>
</div>
<?php init_tail(); ?>
<script>
   init_rel_tasks_table(<?php echo $client->userid; ?>,'customer');
</script>
<?php if(!empty($google_api_key) && !empty($client->latitude) && !empty($client->longitude)){ ?>
<script>
   var latitude = '<?php echo $client->latitude; ?>';
   var longitude = '<?php echo $client->longitude; ?>';
   var marker = '<?php echo $client->company; ?>';
</script>
<script src="<?php echo site_url('assets/js/map.js'); ?>"></script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key; ?>&callback=initMap"></script>
<?php } ?>

<script>
   var customer_id = $('input[name="userid"]').val();
  Dropzone.options.clientAttachmentsUpload = {
      paramName: "file",
      addRemoveLinks: false,
      accept: function(file, done) {
          done();
      },
      success: function(file, response) {
          window.location.reload();
      }
  };

  $(document).ready(function() {
      initDataTable('.table-contracts-single-client', admin_url + 'contracts/index/' + customer_id, [5], [5]);
      initDataTable('.table-invoices-single-client', admin_url + 'invoices/list_invoices/false/' + customer_id);
      initDataTable('.table-estimates-single-client', admin_url + 'estimates/list_estimates/false/' + customer_id);
      initDataTable('.table-payments-single-client', admin_url + 'payments/list_payments/' + customer_id, [6], [6]);
      initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + customer_id + '/' + 'customer', [4], [4]);
      initDataTable('.table-expenses-single-client', admin_url + 'expenses/list_expenses/false/' + customer_id, 'undefined', 'undefined');
      initDataTable('.table-proposals-client-profile', admin_url + 'proposals/proposal_relations/' + customer_id + '/customer', 'undefined', 'undefined');
      initDataTable('.table-projects-single-client', admin_url + 'projects/index/' + customer_id, [7], [7], 'undefined', [3, 'ASC']);
      _validate_form($('.client-form'), {
          company: 'required',
          password: {
              required: {
                  depends: function(element) {
                      var is_edit = $('input[name="isedit"]');
                      var sent_set_password = $('input[name="send_set_password_email"]');
                      if (is_edit.length == 0 && sent_set_password.prop('checked') == false) {
                          return true;
                      }
                  }
              }
          },
          email: {
              required: true,
              email: true,
              remote: {
                  url: site_url + "admin/misc/clients_email_exists",
                  type: 'post',
                  data: {
                      email: function() {
                          return $('input[name="email"]').val();
                      },
                      userid: function() {
                          return customer_id;
                      }
                  }
              }
          }
      });
      $('.billing-same-as-customer').on('click', function(e) {
          e.preventDefault();
          $('input[name="billing_street"]').val($('input[name="address"]').val());
          $('input[name="billing_city"]').val($('input[name="city"]').val());
          $('input[name="billing_state"]').val($('input[name="state"]').val());
          $('input[name="billing_zip"]').val($('input[name="zip"]').val());
          $('select[name="billing_country"]').selectpicker('val', $('select[name="country"]').selectpicker('val'));
      });
      $('.customer-copy-billing-address').on('click', function(e) {
          e.preventDefault();
          $('input[name="shipping_street"]').val($('input[name="billing_street"]').val());
          $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
          $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
          $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
          $('select[name="shipping_country"]').selectpicker('val', $('select[name="billing_country"]').selectpicker('val'));
      });
  });
</script>
</body>
</html>
