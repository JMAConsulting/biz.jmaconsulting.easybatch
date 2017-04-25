<table style="display:none">
  <tr class='crm-contribution-form-block-batch_id'>
     <td class='label'>{$form.financial_batch_id.label}</td>
     <td class='content'>{$form.financial_batch_id.html}</td>
  </tr>
</table>
{literal}
<script type="text/javascript">
CRM.$(function($) {
  var batchLabelHtml = $('#batchLabel').html();
  var batchContentHtml = $('#batchContent').html();
  // Contribution.
  $(".crm-contribution-form-block-receive_date, .crm-payment-form-block-trxn_date, .crm-membership-form-block-receive_date")
    .after($('tr.crm-contribution-form-block-batch_id'));

  // Event.
  $( document ).ajaxComplete(function(event, xhr, settings) {
    var str = settings.url;
    if (str.indexOf("civicrm/contact/view/participant") >= 0) {
      $(".crm-event-eventfees-form-block-total_amount")
        .after($('tr.crm-contribution-form-block-batch_id'));
    }
  });
});
</script>
{/literal}