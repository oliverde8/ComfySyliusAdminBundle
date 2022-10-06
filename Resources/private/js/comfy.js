jQuery( document ).ready(function() {
  jQuery('.config-entry').each(function () {
    let $configEntry = $(this);
    let $mainInput = $configEntry.find('.config-entry__input input, .config-entry__input textarea,.config-entry__input select');
    let $useParentCheckbox = $configEntry.find('.config-entry__use-parent input');

    let handleUseParentChange = function (){
      if ($useParentCheckbox.is(':disabled')) {
        $mainInput.prop('disabled', true);
      } else {
        if ($useParentCheckbox.is(":checked")) {
          $mainInput.prop('disabled', true);
        } else {
          $mainInput.prop('disabled', false);
        }
      }
    }

    handleUseParentChange();
    $useParentCheckbox.change(handleUseParentChange);
  })
});
