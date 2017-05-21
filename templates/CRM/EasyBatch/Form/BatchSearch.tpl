<div id="batchclosetime">{include file="CRM/common/jcalendar.tpl" elementName=batch_date_hidden}</div>
{literal}
  <script type="text/javascript">
    CRM.$(function($) {
      $('#batch_date').hide();
      $($('#batchclosetime')).insertAfter('.crm-financial-search-form-block-batch_date td:nth-child(2) #batch_date');
      $('#batchclosetime .dateplugin').change(function(){
        $('#batch_date').val($(this).val());
      	$('#batch_date').change();
      });
      $(document).ajaxSuccess(function( event, xhr, settings ) { 
        console.log(xhr);
      });
    });
  </script>
{/literal}
