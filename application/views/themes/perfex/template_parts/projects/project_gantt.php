<div id="gantt"></div>
<script>
    jQuery(document).ready(function(){
        var gantt_data = <?php echo json_encode($gantt_data); ?>;
        gantt = $("#gantt").gantt({
           source: gantt_data,
           maxScale: "weeks",
           itemsPerPage: 20,
           navigate: 'scroll',
       });
    });
</script>
