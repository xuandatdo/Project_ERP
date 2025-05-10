<template>
    <div>
      <h1>Thêm phương tiện</h1>
      <form @submit.prevent="createVehicle">
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
        <button type="submit">Lưu</button>
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
    fetchPartners() {
      axios.get("/api/partners")
        .then((response) => {
          this.partners = response.data;
        })
        .catch((error) => {
          this.toast.error(error.response?.data?.message || "Không thể tải danh sách đối tác!");
        });
    },
    createVehicle() {
      // Validation for max_load
      if (this.form.max_load < 0) {
        this.toast.error("Tải tối đa không được là số âm");
        return;
      }
      if (this.form.max_load > this.maxLoadLimit) {
        this.toast.error("Số tải vượt mức cho phép");
        return;
      }

      // Validation for name
      const nameRegex = /^[a-zA-Z0-9\s]+$/;
      if (this.form.name.length > 50) {
        this.toast.error("Tên phương tiện không được vượt quá 50 ký tự");
        return;
      }
      if (!nameRegex.test(this.form.name)) {
        this.toast.error("Tên phương tiện không được chứa ký tự đặc biệt");
        return;
      }

      axios.post("/api/vehicles", this.form)
        .then(() => {
          this.toast.success("Thêm phương tiện thành công!");
          this.$router.push("/vehicles");
        })
        .catch((error) => {
          this.toast.error(error.response?.data?.message || "Thêm phương tiện thất bại!");
        });
    },
  },
  mounted() {
    this.toast = useToast();
    this.fetchPartners();
  },
};
</script>

<style scoped>
h1 {
    text-align: center;
    margin-top: 10px;
    margin-bottom: 20px;
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
@media (max-width: 600px) {
    table th, table td {
        font-size: 12px;
        padding: 10px;
    }

    .btn {
        font-size: 12px;
        padding: 8px 12px;
    }

    form {
        padding: 15px;
    }
}
</style>