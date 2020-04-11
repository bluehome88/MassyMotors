jQuery(document).ready(function($){
  const submitButton = $('.wpcf7-form-control.wpcf7-submit');
  const disabledMessage = $('.disabled-message');
  $('.wpcf7-form-control.wpcf7-select.wpcf7-validates-as-required').on('change', function () {
    const isWestmoorings = this.value === 'Westmoorings';
    submitButton.attr('disabled', isWestmoorings);
    disabledMessage.toggle(isWestmoorings);
  });
});
