const showError = function ($recaptcha) {
  $recaptcha.siblings('.g-recaptcha-required-error').remove();
  const $errorEl = $('<div class="g-recaptcha-required-error"></div>');
  $errorEl.text(local.recaptchaRequiredMessage);
  $recaptcha.after($errorEl);
};

const removeError = function ($recaptcha) {
  $recaptcha.siblings('.g-recaptcha-required-error').remove();
};

const renderRecaptcha = function ($recaptcha) {
  if (
    !$recaptcha.length ||
    !$recaptcha.hasClass('review-recaptcha-placeholder') ||
    $recaptcha.attr('data-recaptcha-widget-id')
  ) {
    return false;
  }

  if (typeof window.grecaptcha === 'undefined' || typeof window.grecaptcha.render !== 'function') {
    return false;
  }

  $recaptcha.addClass('is-rendering');

  const widgetId = window.grecaptcha.render($recaptcha.get(0), {
    sitekey: $recaptcha.data('sitekey'),
    callback: function () {
      removeError($recaptcha);
    },
    'expired-callback': function () {
      showError($recaptcha);
    },
    'error-callback': function () {
      showError($recaptcha);
    },
  });

  $recaptcha.attr('data-recaptcha-widget-id', widgetId);
  $recaptcha.removeClass('is-rendering');

  return true;
};

const scheduleRecaptchaRender = function ($recaptcha, attempts = 20) {
  if (
    !$recaptcha.length ||
    !$recaptcha.hasClass('review-recaptcha-placeholder') ||
    $recaptcha.attr('data-recaptcha-widget-id')
  ) {
    return false;
  }

  if (renderRecaptcha($recaptcha)) {
    return true;
  }

  if (attempts <= 0) {
    return true;
  }

  window.setTimeout(function () {
    scheduleRecaptchaRender($recaptcha, attempts - 1);
  }, 250);

  return true;
};

const maybeRenderRecaptcha = function ($form) {
  const $recaptcha = $form.find('.g-recaptcha-required');
  return scheduleRecaptchaRender($recaptcha);
};

const getRecaptchaResponse = function ($recaptcha) {
  const widgetId = $recaptcha.attr('data-recaptcha-widget-id');

  if (
    widgetId &&
    typeof window.grecaptcha !== 'undefined' &&
    typeof window.grecaptcha.getResponse === 'function'
  ) {
    return window.grecaptcha.getResponse(widgetId);
  }

  const $recaptchaInput = $recaptcha.find('#g-recaptcha-response');
  return $recaptchaInput.val();
};

const stopFormSubmit = function (e) {
  e.preventDefault();
  e.stopImmediatePropagation();
};

const validateForm = function (e) {
  const $form = $(this);
  const $recaptcha = $form.find('.g-recaptcha-required');

  if (maybeRenderRecaptcha($form)) {
    showError($recaptcha);
    stopFormSubmit(e);
    return;
  }

  if (!getRecaptchaResponse($recaptcha)) {
    showError($recaptcha);
    stopFormSubmit(e);
    return;
  }

  removeError($recaptcha);
};

const handleRecaptcha = function () {
 const $recaptcha = $(this);
 const $form = $recaptcha.closest('form');
 $form.on('submit', validateForm);
};

export const initRecaptchaRequired = () => {
  const $recaptchas = $('.g-recaptcha-required');
  $recaptchas.each(handleRecaptcha);

  $(document).on('shown.bs.collapse', '.collapse', function () {
    $(this)
      .find('.review-recaptcha-placeholder')
      .each(function () {
        scheduleRecaptchaRender($(this));
      });
  });
};
