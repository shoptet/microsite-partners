const $contactForm = $('#requestContactForm');
const $contactFormError = $('#requestContactFormError');
const $contactFormSuccess = $('#requestContactFormSuccess');

const formError = function(text) {
  if (text.length > 0) {
    $contactFormError.removeClass('d-none');
  } else {
    $contactFormError.addClass('d-none');
  }
  $contactFormError.text(text);
};

const formSuccess = function(text) {
  $contactFormSuccess.removeClass('d-none').text(text);
};

const isEmail = function(email) {
  const regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

const validateForm = function() {
  let isValid = true;
  let value = '';
  let $this = null;
  $contactForm.find('input[name], textarea[name]').each(function() {
    $this = $(this);
    value = $.trim($this.val());
    if (!value) {
      formError('Vyplňte prosím všechna pole');
      isValid = false;
      return false;
    }
    if ($this.attr('type') === 'email' && !isEmail(value)) {
      formError('Vyplňte prosím správný e-mail');
      isValid = false;
      return false;
    }
  });

  return isValid;
};

const onSuccess = function() {
  $contactForm.find('button[type=submit]').remove();
  formError('');
  formSuccess('Odesláno!');
};

const onError = function(xhr) {
  formError('Omlouvám se, ale při odeslání došlo k chybě. Než chybu opravíme, kontaktujte raději obchodníka přímo e-mailem.');
  console.error(xhr);
};

const getFormData = function() {
  const data = {};

  let $this = null;
  $contactForm.find('[name]').each(function() {
    $this = $(this);
    data[$this.attr('name')] = $this.val();
  });

  return data;
};

const sendData = function(data) {
  $.ajax({
    type: 'POST',
    url: window.ajaxurl,
    data: Object.assign(
      {
        action: 'request_message',
      },
      data
    ),
    success: onSuccess,
    error: onError,
    complete: function() {
      $contactForm.removeClass('is-loading');
    },
  });
};

export const initRequestMessage = () => {
  $contactForm.on('submit', function(e) {
    e.preventDefault();
    if (!validateForm()) return;
    $contactForm.addClass('is-loading');
    const data = getFormData();
    sendData(data);
  });
};