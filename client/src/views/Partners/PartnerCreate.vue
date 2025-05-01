<template>
    <div>
      <h1>Thêm đối tác</h1>
      <form @submit.prevent="createPartner">
        <div>
          <label>Mã đối tác:</label>
          <input v-model="form.code" type="text" required />
        </div>
        <div>
          <label>Tên đối tác:</label>
          <input v-model="form.name" type="text" required />
        </div>
        <div>
          <label>Tổng số đơn:</label>
          <input v-model="form.total_orders" type="number" />
        </div>
        <div>
          <label>Tổng số tiền:</label>
          <input v-model="form.total_amount" type="number" step="0.01" />
        </div>
        <button type="submit">Lưu</button>
        <button type="button" @click="$router.push('/partners')" class="btn-back">Trở về</button>
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
          code: "",
          name: "",
          total_orders: 0,
          total_amount: 0.0,
        },
      };
    },
    methods: {
      createPartner() {
        axios.post("/api/partners", this.form)
        .then(() => {
          this.toast.success("Thêm đối tác thành công!");
          this.$router.push("/partners");
        })
        .catch((error) => {
          this.toast.error(error.response?.data?.message || "Thêm đối tác thất bại!");
        });
      },
    },
    mounted() {
        this.toast = useToast();
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

    form input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

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
        background-color: #6c757d; /* Bootstrap secondary color */
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