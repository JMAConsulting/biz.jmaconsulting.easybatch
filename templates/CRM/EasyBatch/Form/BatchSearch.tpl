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
        $('table.dataTable thead th.crm-batch-org').remove();
        $('table.dataTable thead th.crm-batch-date').remove();
        $("<th class = 'crm-batch-org' >Organization</th>").insertAfter('table.dataTable thead th.crm-batch-name');
        $("<th class = 'crm-batch-date'>Date</th>").insertAfter('table.dataTable thead th.crm-batch-name');
        $('table.dataTable tbody tr').each(function(a, tr) {
	  $(tr).find('td.crm-batch-org').remove();
	  $(tr).find('td.crm-batch-date').remove();
	  var batchID = $(tr).attr('data-id');
	  var org_id = $('a.rowbatchdata-' + batchID).attr('org_id');
	  var batchdate = $('a.rowbatchdata-' + batchID).attr('batchdate');
          $("<td class = 'crm-batch-org' >" + org_id + "</td>")
            .insertAfter($(tr).find('td.crm-batch-name'));
          $("<td class = 'crm-batch-date'>" + batchdate + "</td>")
            .insertAfter($(tr).find('td.crm-batch-name'));
        });
      });
    });
  </script>
{/literal}
