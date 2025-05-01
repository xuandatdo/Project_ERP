<template>
    <div>
      <h1>Danh sách phương tiện</h1>
      <router-link to="/vehicles/create" class="btn">Thêm phương tiện</router-link>
      
      <table>
        <thead>
          <tr>
            <th>STT</th>
            <th>Mã đối tác</th>
            <th>Tên phương tiện</th>
            <th>Tải tối đa</th>
            <th>Lịch bảo dưỡng</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(vehicle, index) in vehicles" :key="vehicle.id">
            <td>{{ index + 1 }}</td>
            <td>{{ vehicle.partner_code }}</td>
            <td>{{ vehicle.name }}</td>
            <td>{{ vehicle.max_load }}</td>
            <td>{{ vehicle.maintenance_date }}</td>
            <td class="actions">
              <router-link :to="`/vehicles/${vehicle.id}/edit`">Chỉnh sửa</router-link>
              <button @click="deleteVehicle(vehicle.id)">Xóa</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  import { useToast } from "vue-toastification";
  
  export default {
    data() {
      return {
        vehicles: [],
      };
    },
    methods: {
      fetchVehicles() {
        axios.get("/api/vehicles")
          .then((response) => {
            this.vehicles = response.data;
          })
          .catch((error) => {
            this.toast.error(error.response?.data?.message || "Không thể tải danh sách phương tiện!");
          });
      },
      deleteVehicle(id) {
        if (confirm("Bạn có chắc muốn xóa phương tiện này không?")) {
          axios.delete(`/api/vehicles/${id}`)
            .then(() => {
              this.toast.success("Đã xóa phương tiện thành công!");
              this.fetchVehicles();
            })
            .catch((error) => {
              this.toast.error(error.response?.data?.message || "Xóa phương tiện thất bại!");
            });
        }
      },
    },
    mounted() {
      this.toast = useToast();
      this.fetchVehicles();
    },
  };
  </script>
  
  <style scoped>
  h1 {
    margin: 10px 0 20px 20px;
  }
  .btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
    margin: 10px 0 15px 20px;
  }
  .btn:hover {
    background-color: #0056b3;
  }
  
  /* Table */
  table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-left: 10px;
  }
  
  th, td {
    padding: 14px 16px;
    border-bottom: 1px solid #eaeaea;
    text-align: left;
    font-size: 14px;
  }
  
  th {
    background-color: #343a40;
    color: white;
    text-transform: uppercase;
    font-size: 13px;
  }
  
  tr:nth-child(even) {
    background-color: #f9f9f9;
  }
  
  tr:hover {
    background-color: #f1f1f1;
  }
  
  /* Action buttons */
  .actions a,
  .actions button {
    margin-right: 6px;
    font-size: 13px;
    padding: 6px 10px;
    border-radius: 4px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-weight: bold;
  }
  
  .actions a {
    color: #007bff;
    border: 1px solid #007bff;
    background-color: transparent;
  }
  
  .actions a:hover {
    background-color: #007bff;
    color: white;
  }
  
  .actions button {
    background-color: #dc3545;
    color: white;
  }
  
  .actions button:hover {
    background-color: #c82333;
  }
  </style>
