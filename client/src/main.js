import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import './axios';
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
  faChevronDown,
  faChevronRight,
  faChevronLeft,
} from "@fortawesome/free-solid-svg-icons";
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

library.add(faChevronDown, faChevronRight, faChevronLeft);

const app = createApp(App);
app.use(router);

app.component("font-awesome-icon", FontAwesomeIcon);
app.use(Toast);
app.mount("#app");
