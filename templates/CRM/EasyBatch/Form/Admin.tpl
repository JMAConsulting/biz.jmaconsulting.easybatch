<div style="display:none;">
  <span class='help-icon-auto_batch_non_payment_trxns'>{help id="auto_batch_non_payment_trxns" file="CRM/EasyBatch/Form/HelpIcons"}</span>
</div>
{if $batches}
<table class='non-payment-batches'>
  <tr class="crm-preferences-form-block-non_payment_transactions_batch">
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
</table>
{/if}
{literal}
<script type="text/javascript">
CRM.$(function($) {
  $($('table.non-payment-batches tr'))
    .insertAfter('.crm--form-block-auto_batch_non_payment_trxns');
  $($('span.help-icon-auto_batch_non_payment_trxns a'))
    .insertAfter('.crm--form-block-auto_batch_non_payment_trxns td label');
});
</script>
{/literal}