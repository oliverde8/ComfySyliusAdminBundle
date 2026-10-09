document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.config-entry').forEach(function (configEntry) {
    const mainInputs = configEntry.querySelectorAll('.config-entry__input input, .config-entry__input textarea, .config-entry__input select');
    const useParentCheckbox = configEntry.querySelector('.config-entry__use-parent input');

    if (!useParentCheckbox) {
      return;
    }

    const handleUseParentChange = function () {
      const disabled = useParentCheckbox.disabled || useParentCheckbox.checked;
      mainInputs.forEach(function (input) {
        input.disabled = disabled;
      });
    };

    handleUseParentChange();
    useParentCheckbox.addEventListener('change', handleUseParentChange);
  });
});
