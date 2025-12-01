<table style="display:none">
  <tr class='crm-financialbatch-form-block-created_id'>
     <td class='label'>{$form.created_id.label}</td>
     <td class='content'>{$form.created_id.html}</td>
  </tr>
  <tr class='crm-financialbatch-form-block-batch_date'>
     <td class='label'>{$form.batch_date.label}</td>
     <td class='content'>{$form.batch_date.html}</td>
  </tr>
  <tr class='crm-financialbatch-form-block-org_id'>
     <td class='label'>{$form.org_id.label}</td>
     <td class='content'>{$form.org_id.html}</td>
  </tr>
</table>

{literal}
<script type="text/javascript">
CRM.$(function($) {
// Batch create form
$(".crm-contribution-form-block-name")
    .after($('tr.crm-financialbatch-form-block-created_id'));
$(".crm-contribution-form-block-payment_instrument")
    .before($('tr.crm-financialbatch-form-block-batch_date'));
$(".crm-contribution-form-block-payment_instrument")
    .before($('tr.crm-financialbatch-form-block-org_id'));
});
</script>
{/literal}
