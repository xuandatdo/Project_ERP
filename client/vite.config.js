import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
  plugins: [vue()],
  server: {
    proxy: {
      "/api": {
        target: "http://localhost:8000", // URL API Laravel
        changeOrigin: true,
        secure: false,
      },
      "/storage": {
        // Thêm proxy cho /storage
        target: "http://localhost:8000",
        changeOrigin: true,
        secure: false,
      },
    },
  },
});
