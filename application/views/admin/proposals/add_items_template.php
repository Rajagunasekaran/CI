<div class="modal fade" id="proposal_items_template" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Add items</h4>
         </div>
         <div class="modal-body">
            <div class="row main">
               <div class="col-md-6">
                  <div class="form-group">
                     <input type="text" name="description" id="autocomplete" class="form-control">
                  </div>
                  <div class="form-group">
                     <input type="text" name="long_description" class="form-control" placeholder="Long description">
                  </div>
                  <div class="form-group">
                     <input type="number" name="quantity" min="0" value="1" class="form-control" placeholder="Quantity">
                  </div>
                  <div class="form-group">
                     <?php
                        $select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="taxid">';
                        $select .= '<option value="0" selected>'._l('no_tax').'</option>';
                        foreach($taxes as $tax){
                         $select .= '<option value="'.$tax['id'].'" data-taxrate="'.$tax['taxrate'].'" data-taxname="'.$tax['name'].'" data-subtext="'.$tax['name'].'">'.$tax['taxrate'].'%</option>';
                         }
                        $select .= '</select>';
                        echo $select;
                        ?>
                  </div>
                  <div class="clearfix"></div>
                  <div class="form-group mtop15">
                     <input type="text" name="rate" class="form-control" placeholder="Rate">
                  </div>
                  <div class="form-group">
                     <button type="button" onclick="add_items_to_preview_proposal_table(); return false;" class="btn btn-info"><i class="fa fa-check"></i></button>
                  </div>
               </div>
               <div class="col-md-12">
                  <div id="proposal-pre-items">
                     <table class="table proposal-items">
                        <thead>
                           <tr>
                              <th width="15%"><?php echo _l('proposal_items_name'); ?></th>
                              <th width="35%"><?php echo _l('proposal_items_description'); ?></th>
                              <th width="10%"><?php echo _l('proposal_items_qty'); ?></th>
                              <th width="10%"><?php echo _l('proposal_items_rate'); ?></th>
                              <th width="15%"><?php echo _l('proposal_items_tax'); ?></th>
                              <th width="15%"><?php echo _l('proposal_items_amount'); ?></th>
                              <th class="delete_item"></th>
                           </tr>
                        </thead>
                        <tbody></tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="button" class="btn btn-info add-items-to-proposal"><?php echo _l('proposal_insert_items'); ?></button>
         </div>
      </div>
   </div>
</div>
<script>
   // Items search
   $.ajax({
     type: "POST",
     url: admin_url + 'invoice_items/get_all_items_ajax',
     dataType: 'json',
     success: function(response) {
       $('body').find('#autocomplete').autocomplete({
         source: response
       }).autocomplete('instance')._renderItem = function(ul, item) {
         return $("<li class='item-auto-search' onclick='add_item_to_preview(" + item.itemid + "); return false;'>")
         .append("<a href='#' class='bold'>" + item.label + "<br><span class='text-muted'>" + item.long_description + "</span></a>")
         .appendTo(ul);
       }
     }
   });

   function add_items_to_preview_proposal_table() {
     var table_row = '';
     data = get_main_values();

     table_row += '<tr class="item">';
     if (data.qty == '' || data.qty == 0) {
       data.qty = 1;
     }
     if (data.rate == '' || isNaN(data.rate)) {
       data.rate = 0;
     }
     var tax;
     var taxid
     var tax_val = $('#proposal_items_template select[name="taxid"]').val();
     if(tax_val == 0){
       tax = 0 + '%';
       taxid = 0;
     } else {
       tax = $('#proposal_items_template select[name="taxid"] option:selected').text();
       taxid =  $('#proposal_items_template select[name="taxid"]').selectpicker('val');
     }
     var amount = data.rate * data.qty;
     table_row += '<td width="15%">'+data.description+'</td>';
     table_row += '<td width="40%">'+data.long_description+'</td>';
     table_row += '<td width="5%">'+data.qty+'</td>';
     table_row += '<td width="10%">'+accounting.formatNumber(data.rate)+'</td>';
     table_row += '<td width="15%" data-taxid="'+taxid+'">' + tax + '</td>';
     table_row += '<td width="15%">' + accounting.formatNumber(amount) + ' <a href="#" class="btn btn-danger pull-right delete_item" onclick="delete_item(this); return false;"><i class="fa fa-times"></i></a></td>';
     table_row += '</tr>';
     $('#proposal-pre-items tbody').append(table_row);
     clear_main_values();
     return true;
   }

</script>
