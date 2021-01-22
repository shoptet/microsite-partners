const $contactForm = $('#contactForm');
const $contactFormError = $('#contactFormError');
const $contactFormSuccess = $('#contactFormSuccess');

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
      formError(local.contactAllFieldsRequiredMessage);
      isValid = false;
      return false;
    }
    if ($this.attr('type') === 'email' && !isEmail(value)) {
      formError(local.contactCorrectEmailMessage);
      isValid = false;
      return false;
    }
  });

  return isValid;
};

const onSuccess = function() {
  $contactForm.find('button[type=submit]').remove();
  formError('');
  formSuccess(local.contactFormSent);

  if ( 'partner_message' == $contactForm.data('ajax-action') ) {
    dataLayer.push({'event': 'kontaktovatPartnera'});
  }
};

const onError = function(xhr) {
  formError(local.contactFormErrorMessage);
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
  const action = $contactForm.data('ajax-action');
  $.ajax({
    type: 'POST',
    url: window.ajaxurl,
    data: Object.assign(
      {
        action: action,
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

export const initContactMessage = () => {
  $contactForm.on('submit', function(e) {
    e.preventDefault();
    
    if ( !validateForm() ) {
      return;
    }

    if ( $contactForm.data('ajax-action') ) {
      if ($contactForm.data('submitted') === true) {
        return;
      }
      $contactForm.data('submitted', true);
      $contactForm.addClass('is-loading');
      const data = getFormData();
      sendData(data);
    } else {
      if ( 'onboarding_contact' == $contactForm.data('action') ) {
        dataLayer.push({'event': 'PartneriKontakt'});
      }
      this.submit();
    }

  });
};