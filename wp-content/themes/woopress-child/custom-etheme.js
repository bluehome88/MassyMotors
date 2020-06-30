jQuery(document).ready(function ($) {
  const $submitButton = $('.wpcf7-form-control.wpcf7-submit');
  const $disabledMessage = $('.disabled-message');
  const $select = $('.wpcf7-form-control.wpcf7-select.wpcf7-validates-as-required');
  const disabledOptions = ['Westmoorings', 'Trincity'];
  $select.on('change', function () {
    const isWestmoorings = disabledOptions.includes(this.value);
    $submitButton.attr('disabled', isWestmoorings);
    $disabledMessage.toggle(isWestmoorings);
  });
});
