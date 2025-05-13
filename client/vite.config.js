import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
  plugins: [vue()],
  server: {
    proxy: {
      "/api": {
        target: "https://www.erp.plt.pro.vn/", // URL API Laravel
        changeOrigin: true,
        secure: false,
      },
      "/storage": {
        // Thêm proxy cho /storage
        target: "https://www.erp.plt.pro.vn/",
        changeOrigin: true,
        secure: false,
      },
    },
  },
});
