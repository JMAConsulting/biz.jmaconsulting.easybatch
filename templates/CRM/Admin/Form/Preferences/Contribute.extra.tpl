<div id="batchclosetime">{include file="CRM/common/jcalendar.tpl" elementName=batch_close_time}</div>
{literal}
  <script type="text/javascript">
    CRM.$(function($) {

      $("#batch_close_time").replaceWith($("#batchclosetime"));
      $("input[name^='batch_close_time_display_']").hide();
      $('label[for="batch_close_time_time"]').hide();

      showHideElement('auto_financial_batch', 'batch_close_time');
      $("#auto_financial_batch").click(function() {
        showHideElement('auto_financial_batch', 'batch_close_time');
      });
      function showHideElement(checkEle, toHide) {
        if ($('#' + checkEle).prop('checked')) {
          $("tr.crm-preferences-form-block-" + toHide).show();
        }
        else {
          $("tr.crm-preferences-form-block-" + toHide).hide();
        }
      }
      $("tr.crm-preferences-form-block-prior_financial_period td:nth-child(2)").html($('#orgs'));
    });
  </script>
{/literal}