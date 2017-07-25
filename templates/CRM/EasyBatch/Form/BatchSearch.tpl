<div id="batchclosetime"><div id="batchclosetimefrom" style="float:left">{include file="CRM/common/jcalendar.tpl" elementName=batch_date_from_hidden}</div> <div id="batchclosetimeto" style="overflow:hidden"><span id="totext">to</span> {include file="CRM/common/jcalendar.tpl" elementName=batch_date_to_hidden}</div></div>
{literal}
  <script type="text/javascript">
    CRM.$(function($) {
      $('#batch_date_from').hide();
      $('#batch_date_to').hide();
      $('tr.crm-financial-search-form-block-batch_date_to').hide();
      $($('#batchclosetime')).insertAfter('.crm-financial-search-form-block-batch_date_from td:nth-child(2) #batch_date_from');
      $('#batchclosetimefrom .dateplugin').change(function(){
        $('#batch_date_from').val($(this).val());
	$('#batch_date_from').change();
      });
      $('#batchclosetimeto .dateplugin').change(function(){
        $('#batch_date_to').val($(this).val());
	$('#batch_date_to').change();
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
