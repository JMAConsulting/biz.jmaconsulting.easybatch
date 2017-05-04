<table style="display:none">
  <tr class='crm-financialbatch-form-block-contact_id'>
     <td class='label'>{$form.contact_id.label}</td>
     <td class='content'>{$form.contact_id.html}</td>
  </tr>
  <tr class='crm-financialbatch-form-block-batch_date'>
     <td class='label'>{$form.batch_date.label}</td>
     <td class='content'>{include file="CRM/common/jcalendar.tpl" elementName=batch_date}</td>
  </tr>
  <tr class='crm-financialbatch-form-block-org_id'>
     <td class='label'>{$form.org_id.label}</td>
     <td class='content'>{$form.org_id.html}</td>
  </tr>
</table>

{literal}
<script type="text/javascript">
CRM.$(function($) {

// Financial Batch search form
$(".crm-financial-search-form-block-status_id")
    .after($('tr.crm-financialbatch-form-block-org_id'));
$(".crm-financial-search-form-block-status_id")
    .after($('tr.crm-financialbatch-form-block-batch_date'));
$(".crm-financial-search-form-block-total")
    .after($('tr.crm-financial-search-form-block-sort_name'));

// Batch create form
$(".crm-contribution-form-block-name")
    .after($('tr.crm-financialbatch-form-block-contact_id'));
$(".crm-contribution-form-block-payment_instrument")
    .before($('tr.crm-financialbatch-form-block-batch_date'));
$(".crm-contribution-form-block-payment_instrument")
    .before($('tr.crm-financialbatch-form-block-org_id'));
});
</script>
{/literal}