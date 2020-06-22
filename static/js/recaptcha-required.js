const showError = function ($recaptcha) {
  console.error('recaptcha is required!');
  const $errorEl = $('<div class="g-recaptcha-required-error"></div>');
  $errorEl.text(local.recaptchaRequiredMessage);
  $recaptcha.after($errorEl);
};

const validateForm = function (e) {
  const $form = $(this);
  const $recaptchaInput = $form.find('.g-recaptcha-required #g-recaptcha-response');
  if (!$recaptchaInput.val()) {
    const $recaptcha = $form.find('.g-recaptcha-required');
    showError($recaptcha);
    e.preventDefault();
    return;
  }
};

const handleRecaptcha = function () {
 const $recaptcha = $(this);
 const $form = $recaptcha.closest('form');
 $form.on('submit', validateForm);
};

export const initRecaptchaRequired = () => {
  const $recaptchas = $('.g-recaptcha-required');
  $recaptchas.each(handleRecaptcha);
};