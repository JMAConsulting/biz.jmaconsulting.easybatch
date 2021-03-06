<table style="display:none">
  <tr class='crm-contribution-form-block-batch_id'>
     <td class='label'>{$form.financial_batch_id.label}</td>
     <td class='content'>{$form.financial_batch_id.html}</td>
  </tr>
</table>
{literal}
<script type="text/javascript">
CRM.$(function($) {
  hideBatch($('#contribution_status_id'));
  $('#contribution_status_id').change(function() {
    hideBatch($(this));
  })
  var batchLabelHtml = $('#batchLabel').html();
  var batchContentHtml = $('#batchContent').html();

  // Contribution.
  $("#receiptDate, .crm-payment-form-block-trxn_date, .crm-membership-form-block-receive_date, .crm-membershiprenew-form-block-receive_date").after($('tr.crm-contribution-form-block-batch_id'));

  // Membership
  $(".crm-member-membership-form-block-financial_type_id, .crm-member-membershiprenew-form-block-financial_type_id, .crm-participant-form-block-payment_processor_id, .crm-payment-form-block-payment_processor_id").before($('tr.crm-contribution-form-block-batch_id'));

  // Event.
  $( document ).ajaxComplete(function(event, xhr, settings) {
    var str = settings.url;
    var getVar = str.split("eventId=");
    if (str.indexOf("civicrm/contact/view/participant") >= 0 && getVar[1] != undefined ) {
      $(".crm-event-eventfees-form-block-payment_instrument_id")
        .before($('.crm-contribution-form-block-batch_id').parent().html());
    }
  });

  function hideBatch($ele) {
    var statusID = parseInt($ele.val());
    var nonPaymentStatuses = {/literal}{$statuses}{literal};
    $('.crm-contribution-form-block-batch_id').toggle(!($.inArray(parseInt($ele.val()), nonPaymentStatuses) > -1));
  }
});
</script>
{/literal}
