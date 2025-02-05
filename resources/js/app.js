import { createApp } from "vue/dist/vue.esm-bundler";
import TextBlock from "./components/TextBlock.vue";
import ContactForm from "./components/ContactForm.vue";
import axios from 'axios';

import.meta.glob(["../../images/**"]);

import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

// Update axios initialization
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const app = createApp({});
app.component("TextBlock", TextBlock);
app.component("ContactForm", ContactForm);
app.mount("#app");