import { slugify, sha256 } from './helpers';

export const pushPageView = () => {
  const pageView = {
    event: 'pageView',
    page: preparePage(window.dl.page),
    user: window.dl.user,
    partner: window.dl.partner,
  };
  console.log(pageView); // remove
  dataLayer.push(pageView);
};

export const initButtonClick = () => {
  document.querySelectorAll('a[href],button[type=button]').forEach((el) => {
    el.addEventListener('click', function (e) {
      const element = {
        color: 'not_available_DL',
        target: (this.href && this.href.trim()) || 'not_available_DL',
        text: this.textContent.trim() || 'not_available_DL',
        id: createElementID(this),
      };
      const buttonClick = {
        event: 'buttonClick',
        page: preparePage(window.dl.page),
        user: window.dl.user,
        element,
      };
      dataLayer.push(buttonClick);
      console.log(buttonClick); // remove
    });
  });
};

export const initFormSubmit = () => {
  document.querySelectorAll('form:not([data-ajax]):not(.wpcf7-form)').forEach((el) => {
    el.addEventListener('submit', () => handleFormSubmit(el));
  });
  document.querySelectorAll('.wpcf7').forEach((el) => {
    el.addEventListener('wpcf7mailsent', () => handleFormSubmit(el));
  });
};

export const initSearch = () => {
  document.querySelectorAll('form[data-search]').forEach((el) => {
    el.addEventListener('submit', () => handleSearch(el));
  });
};

export const handleFormSubmit = (el) => {
  const formContainer = el.closest('[data-form]');
  const form = {
    id: (formContainer && formContainer.id) || el.id || 'not_available_DL',
    type: (formContainer && formContainer.dataset && formContainer.dataset.formType) || el.dataset.formType || 'not_available_DL',
  };

  if (el.id == 'general_request') {
    form.id =  el.id + '~' + document.getElementById('acf-field_5d9f2f4a8e648').value; // category id
  }

  const user = window.dl.user;

  const nameInput = el.querySelector('[data-name] input');
  const name = (nameInput && nameInput.value);
  if (name) {
    const fullNameArray = name.split(' ');
    user.name = fullNameArray.shift() || 'not_available_DL';
    user.surname = fullNameArray.join(' ') || 'not_available_DL';
  }

  const emailInput = el.querySelector('[data-email] input');
  const email = (email && emailInput.value.trim().toLowerCase());
  if (email) {
    user.email = email;
    sha256(email).then((accountHash) => {
      user.accountHash = accountHash;
      pushFormSubmit(form, user);
    });
  } else {
    pushFormSubmit(form, user)
  }
};

const pushFormSubmit = (form, user) => {
  const formSubmit = {
    event: 'formSubmit',
    page: preparePage(window.dl.page),
    form,
    user,
  };
  dataLayer.push(formSubmit);
  console.log(formSubmit); // remove
};

export const handleSearch = (el) => {
  const user = window.dl.user;
  const searchInput = el.querySelector('input[type=search]');
  const term = searchInput && searchInput.value;

  const search = {
    event: 'search',
    search: {
      type: 'page',
      term,
      results: {
        articles: -1,
        categories: -1,
        products: -1,
        other: -1,
      },
    },
    page: preparePage(window.dl.page),
    user,
  };
  dataLayer.push(search);
  console.log(search); // remove
};

const preparePage = (page) => {
  page.path = window.location.pathname;
  page.url = window.location.href;
  if (window.location.search || window.location.hash) {
   page.params = window.location.search + window.location.hash;
  }
  return page;
};

const createElementID = (el) => {
  const parentWithID = el.closest('[id]');
  let parentID = parentWithID ? parentWithID.id : false;
  const slug = slugify(el.textContent) || slugify(el.ariaLabel);

  if (parentID == 'shp_navigation') {
    parentID = 'menu';
  }
  
  let id;
  if (el.id) {
    id = el.id;
  } else if (parentID && slug) {
    id = parentID + '~' + slug;
  } else if (parentID) {
    id = parentID;
  } else if (slug) {
    id = slug;
  } else {
    id = 'not_available_DL';
  }
  return id;
};