<table>
{foreach from=$batchIDs item="batchid" key="batchkey"}
  <tr class="crm-activity-form-block-source_contact_id">
    <td class="label">{$form.$batchid.label}</td>
    <td class="view-value">
      {$form.$batchid.html}
    </td>
  </tr>
{/foreach}
</table>