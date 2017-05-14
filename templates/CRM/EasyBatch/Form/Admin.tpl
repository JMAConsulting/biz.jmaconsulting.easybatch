<span class='help-icon-auto_batch_non_payment_trxns'>{help id="auto_batch_non_payment_trxns" file="CRM/EasyBatch/Form/HelpIcons"}</span>
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
  $($('span.help-icon-auto_batch_non_payment_trxns a'))
    .insertAfter('.crm-preferences-form-block-auto_batch_non_payment_trxns td.label label');
  $($('table.non-payment-batches tr'))
    .insertAfter('.crm-preferences-form-block-auto_batch_non_payment_trxns');
});
</script>
{/literal}