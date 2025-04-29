import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import "./axios";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { fas } from "@fortawesome/free-solid-svg-icons";
import { Toast, options } from "./plugins/toast";

library.add(fas);

const app = createApp(App);
app.use(router);

app.component("font-awesome-icon", FontAwesomeIcon);
app.use(Toast, options);
app.mount("#app");
