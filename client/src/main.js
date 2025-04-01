import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
  faChevronDown,
  faChevronRight,
} from "@fortawesome/free-solid-svg-icons";

library.add(faChevronDown, faChevronRight);

const app = createApp(App);
app.use(router);

app.component("font-awesome-icon", FontAwesomeIcon);

app.mount("#app");
