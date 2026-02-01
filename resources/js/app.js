import '../css/app.css';

import './bootstrap';

import Alpine from 'alpinejs'
window.Alpine = Alpine

const pages = import.meta.glob('./pages/*.js');

const page = document.body?.dataset?.page || null;
const key = `./pages/${page}.js`;

if (page && pages[key]) {
  pages[key]().catch(() => {

  });
}
