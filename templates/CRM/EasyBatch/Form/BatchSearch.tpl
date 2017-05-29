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
      $(document).ajaxSuccess(function(event, xhr, settings) {
        $('#crm-batch-selector-1 thead th.crm-batch-company').remove();
        $('#crm-batch-selector-1 thead th.crm-batch-date').remove();
        $("<th class = 'crm-batch-company' >Company</th>").insertAfter('#crm-batch-selector-1 thead th.crm-batch-name');
        $("<th class = 'crm-batch-date'>Date</th>").insertAfter('#crm-batch-selector-1 thead th.crm-batch-name');
	$('#crm-batch-selector-1 tbody tr').each(function(a, tr) {
	  $(tr).find('td.crm-batch-company').remove();
	  $(tr).find('td.crm-batch-date').remove();
	  var batchID = $(tr).attr('data-id');
	  var company = $('a.rowbatchdata-' + batchID).attr('company');
	  var batchdate = $('a.rowbatchdata-' + batchID).attr('batchdate');
          $("<td class = 'crm-batch-company' >" + company + "</td>")
            .insertAfter($(tr).find('td.crm-batch-name'));
          $("<td class = 'crm-batch-date'>" + batchdate + "</td>")
            .insertAfter($(tr).find('td.crm-batch-name'));
        });
      });
    });
  </script>
{/literal}
