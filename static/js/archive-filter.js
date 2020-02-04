const createUrl = (data, currentURL) => {
  // Remove default ordering and empty value
  data = data.filter((item) => {
    const skipDefaultOrderBy = (item.name === 'orderby' && item.value === 'date_desc');
    const skipEmptyValue = (item.value ? false : true);
    return !skipDefaultOrderBy && !skipEmptyValue;
  });
  // Create query string
  let queryString = '';
  data.forEach((item, i) => {
    queryString += (i == 0 ? '?' : '&' ) + item.name + '=' + item.value;
  });
  return currentURL + queryString;
};

function handleFormSubmit (e) {
  e.preventDefault();
  const $form = $(this);
  const data = $form.serializeArray();
  const currentURL = $form.attr('action');
  const url = createUrl(data, currentURL);
  window.location.href = url;
};

export const initArchiveFilter = () => {
  const $forms = $('#requestArchiveFilterForm, #professionalArchiveFilterForm');
  $forms.each( function() {
    const $form = $(this);
    $form.on('submit', handleFormSubmit);
    $form.find(':input').change(() => $form.submit());
  } );
};