<div id="batchclosetime"></div>
<table style="display:none">
  <tr class='crm-paymentProcessor-form-block-cc_financial_batch'>
     <td class='label'></td>
     <td class='content'>{$form.cc_financial_batch.html}&nbsp;{$form.cc_financial_batch.label}</td>
  </tr>
  <tr class='crm-paymentProcessor-form-block-auto_financial_batch'>
     <td class='label'></td>
     <td class='content'>{$form.auto_financial_batch.html}&nbsp;{$form.auto_financial_batch.label}</td>
  </tr>
  <tr class='crm-paymentProcessor-form-block-batch_close_time'>
     <td class='label'>{$form.batch_close_time.label}</td>
     <td class='content'>{$form.batch_close_time.html}{include file="CRM/common/jcalendar.tpl" elementName=batch_close_time}</td>
  </tr>
  {if $batches and !$isHideBatch}
  <tr class="crm-paymentProcessor-form-block-non_payment_transactions_batch">
    <td class='label'><label>{ts}Current non-payment transactions batch{/ts}</label></td>
    <td>
      <div>
        {foreach from=$batches item="batchTitle"}
          <span class="label">{$batchTitle}</span>
  	  <br/>
        {/foreach}
      </div>
    </td>
  </tr>
  {/if}
</table>
{literal}
<script type="text/javascript">
CRM.$(function($) {
  $($('tr.crm-paymentProcessor-form-block-non_payment_transactions_batch'))
    .insertAfter('tr.crm-paymentProcessor-form-block-financial_account');
  $($('tr.crm-paymentProcessor-form-block-batch_close_time'))
    .insertAfter('tr.crm-paymentProcessor-form-block-financial_account');
  $($('tr.crm-paymentProcessor-form-block-cc_financial_batch'))
    .insertAfter('tr.crm-paymentProcessor-form-block-financial_account');
  $($('tr.crm-paymentProcessor-form-block-auto_financial_batch'))
    .insertAfter('tr.crm-paymentProcessor-form-block-financial_account');
  
  $("#batch_close_time").replaceWith($("#batchclosetime"));
  $("input[name^='batch_close_time_display_']").hide();
  $('label[for="batch_close_time_time"]').hide();

  showHideElement();
  $("#auto_financial_batch").click(function() {
    showHideElement();
  });
  function showHideElement() {
    if ($('#auto_financial_batch').prop('checked')) {
      $("tr.crm-paymentProcessor-form-block-batch_close_time").show();
      $('tr.crm-paymentProcessor-form-block-cc_financial_batch').show();
      $("tr.crm-paymentProcessor-form-block-non_payment_transactions_batch").hide();
    }
    else {
      $("tr.crm-paymentProcessor-form-block-batch_close_time").hide();
      $('tr.crm-paymentProcessor-form-block-cc_financial_batch').hide();
      $("tr.crm-paymentProcessor-form-block-non_payment_transactions_batch").show();
    }
  }
});
</script>
{/literal}