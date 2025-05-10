<template>
  <div v-if="isLoading">Loading...</div>
  <div v-else>
    <h1>Chỉnh sửa phương tiện</h1>
    <form @submit.prevent="updateVehicle">
      <div>
        <label>Mã đối tác:</label>
        <select v-model="form.partner_code" required>
          <option value="" disabled>Chọn mã đối tác</option>
          <option v-for="partner in partners" :key="partner.id" :value="partner.code">
            {{ partner.code }} - {{ partner.name }}
          </option>
        </select>
      </div>
      <div>
        <label>Tên phương tiện:</label>
        <input v-model="form.name" type="text" required />
      </div>
      <div>
        <label>Tải tối đa:</label>
        <input v-model="form.max_load" type="number" />
        <h6>[ Tải tối đa là 999999 ]</h6>
      </div>
      <div>
        <label>Lịch bảo dưỡng:</label>
        <input v-model="form.maintenance_date" type="date" />
      </div>
      <button type="submit">Cập nhật</button>
      <button type="button" @click="$router.push('/vehicles')" class="btn-back">Trở về</button>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";

export default {
  data() {
    return {
      isLoading: true,
      form: {
        partner_code: "",
        name: "",
        max_load: 0,
        maintenance_date: "",
      },
      partners: [],
      maxLoadLimit: 999999, // Example maximum load limit
    };
  },
  methods: {
    fetchVehicle() {
      console.log(this.$route.params.id);
      axios.get(`/api/vehicles/${this.$route.params.id}`)
        .then((response) => {
          console.log("API Response:", response.data);
          this.form.partner_code = response.data.partner_code || "";
          this.form.name = response.data.name || "";
          this.form.max_load = response.data.max_load || 0;
          this.form.maintenance_date = response.data.maintenance_date || "";
        })
        .catch((error) => {
          console.error("API Error:", error);
          if (error.response?.status === 404) {
            this.toast.error("This Vehicle has been Deleted");
            this.$router.push("/vehicles");
          } else {
            this.toast.error(error.response?.data?.message || "Không thể tải thông tin phương tiện!");
          }
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    fetchPartners() {
      return axios.get("/api/partners")
        .then((response) => {
          console.log(response.data);
          this.partners = response.data;
        })
        .catch((error) => {
          this.toast.error(error.response?.data?.message || "Không thể tải danh sách đối tác!");
        });
    },
    updateVehicle() {
      // Validation for max_load
      if (this.form.max_load < 0) {
        this.toast.error("Tải tối đa không được là số âm");
        return;
      }
      if (this.form.max_load > this.maxLoadLimit) {
        this.toast.error("Số tải vượt mức cho phép");
        return;
      }

      axios.put(`/api/vehicles/${this.$route.params.id}`, this.form)
        .then(() => {
          this.toast.success("Cập nhật phương tiện thành công!");
          this.$router.push("/vehicles");
        })
        .catch((error) => {
          this.toast.error(error.response?.data?.message || "Cập nhật phương tiện thất bại!");
        });
    },
  },
  mounted() {
    this.toast = useToast();
    this.fetchPartners().then(() => {
      this.fetchVehicle();
    });
  },
};
</script>

<style scoped>
/* Reuse the styles from the original file */
h1 {
    margin-bottom: 20px;
    text-align: center;
    color: #333;
    margin-top: 10px;
}

form {
    max-width: 500px;
    margin: 0 auto;
    padding: 25px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

form div {
    margin-bottom: 20px;
}

form label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #333;
}

form select,
form input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    transition: border-color 0.2s;
}

form select:focus,
form input:focus {
    border-color: #007bff;
    outline: none;
}

button[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    font-size: 15px;
    transition: background-color 0.2s;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

.btn-back {
    width: 100%;
    padding: 12px;
    background-color: #6c757d;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    font-size: 15px;
    margin-top: 10px;
    transition: background-color 0.2s;
}

.btn-back:hover {
    background-color: #5a6268;
}
</style>