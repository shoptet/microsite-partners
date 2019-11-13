let $form;

const createUrl = (data) => {
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
  const $currentUrlPath = location.protocol + '//' + location.host + location.pathname;
  return $currentUrlPath + queryString;
};

const handleFormSubmit = (e) => {
  e.preventDefault();
  const data = $form.serializeArray();
  const url = createUrl(data);
  window.location.href = url;
};

export const initRequestArchiveFilter = () => {
  $form = $('#requestArchiveFilterForm');
  $form.on('submit', handleFormSubmit);
  $form.find(':input').change(() => $form.submit());
};