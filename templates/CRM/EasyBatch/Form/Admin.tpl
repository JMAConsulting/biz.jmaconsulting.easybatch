{foreach from=$batchIDs item="batchid" key="batchkey"}
  <div id="{$batchid}_label">{$form.$batchid.label}</div>
  <div id="{$batchid}_html">{$form.$batchid.html}</div>
{/foreach}

{literal}
<script type="text/javascript">
CRM.$(function($) {
  var batchCount = '{/literal}{$batchCount}{literal}';
  {/literal}{foreach from=$batchIDs item="batchid" key="batchkey"}{literal}
    var batchId = '{/literal}{$batchid}{literal}';
    var batchLabelHtml = $('#' + batchId + '_label').html();
    var batchContentHtml = $('#' + batchId + '_html').html();
    $(".crm-preferences-form-block-batch_close_time")
      .after("<tr class='crm-preferences-form-block-auto_batch_id'><td class='label'>" + batchLabelHtml + "</td><td class='content'>" + batchContentHtml + "</td></tr>");
    $('#' + batchId + '_label').hide();
    $('#' + batchId + '_html').hide();
  {/literal}{/foreach}{literal}
});
</script>
{/literal}