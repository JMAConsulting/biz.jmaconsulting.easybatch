<div id="batchContent">{$form.financial_batch_id.html}</div>
<div id="batchLabel">{$form.financial_batch_id.label}</div>

{literal}
<script type="text/javascript">
CRM.$(function($) {
  var batchLabelHtml = $('#batchLabel').html();
  var batchContentHtml = $('#batchContent').html();
  $(".crm-contribution-form-block-receive_date")
    .after("<tr class='crm-contribution-form-block-batch_id'><td class='label'>" + batchLabelHtml + "</td><td class='content'>" + batchContentHtml + "</td></tr>");
  $('#batchLabel').hide();
  $('#batchContent').hide();
});
</script>
{/literal}