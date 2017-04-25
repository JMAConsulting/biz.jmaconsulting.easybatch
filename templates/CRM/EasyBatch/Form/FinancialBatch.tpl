<div id="batchContent">{$form.financial_batch_id.html}</div>
<div id="batchLabel">{$form.financial_batch_id.label}</div>

{literal}
<script type="text/javascript">
CRM.$(function($) {
  var batchLabelHtml = $('#batchLabel').html();
  var batchContentHtml = $('#batchContent').html();

  // Contribution.
  $(".crm-contribution-form-block-receive_date")
    .after("<tr class='crm-contribution-form-block-batch_id'><td class='label'>" + batchLabelHtml + "</td><td class='content'>" + batchContentHtml + "</td></tr>");

  // Payment.
  $(".crm-payment-form-block-trxn_date")
    .after("<tr class='crm-contribution-form-block-batch_id'><td class='label'>" + batchLabelHtml + "</td><td class='content'>" + batchContentHtml + "</td></tr>");

  // Membership.
  $(".crm-membership-form-block-membership_type_id")
    .after("<tr class='crm-contribution-form-block-batch_id'><td class='label'>" + batchLabelHtml + "</td><td class='content'>" + batchContentHtml + "</td></tr>");

  // Event.
  $( document ).ajaxComplete(function(event, xhr, settings) {
    var str = settings.url;
    if (str.indexOf("civicrm/contact/view/participant") >= 0) {
      $(".crm-event-eventfees-form-block-total_amount")
        .after("<tr class='crm-contribution-form-block-batch_id'><td class='label'>" + batchLabelHtml + "</td><td class='content'>" + batchContentHtml + "</td></tr>");
    }
  });

  $('#batchLabel').hide();
  $('#batchContent').hide();
  $( document ).ajaxComplete(function() {
    $('#batchLabel').hide();
    $('#batchContent').hide();
  });
});
</script>
{/literal}