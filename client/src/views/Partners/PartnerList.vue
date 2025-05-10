<template>
  <div>
    <h1>Danh sách đối tác</h1>
    <router-link to="/partners/create" class="btn btn-primary">Thêm đối tác</router-link>
    <table>
      <thead>
        <tr>
          <th>STT</th>
          <th>Mã đối tác</th>
          <th>Tên đối tác</th>
          <th>Tổng số đơn</th>
          <th>Tổng số tiền</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(partner, index) in paginatedPartners" :key="partner.id">
          <td>{{ (currentPage - 1) * partnersPerPage + index + 1 }}</td>
          <td>{{ partner.code }}</td>
          <td>{{ partner.name }}</td>
          <td>{{ partner.total_orders }}</td>
          <td>{{ partner.total_amount }}</td>
          <td class="actions">
            <router-link :to="`/partners/${partner.id}/edit`">Chỉnh sửa</router-link>
            <button @click="deletePartner(partner.id)">Xóa</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="pagination">
      <button 
        :disabled="currentPage === 1" 
        @click="currentPage--">
        Trước
      </button>
      <span>Trang {{ currentPage }} / {{ totalPages }}</span>
      <button 
        :disabled="currentPage === totalPages" 
        @click="currentPage++">
        Tiếp
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";

export default {
  data() {
    return {
      partners: [],
      currentPage: 1, // Current page number
      partnersPerPage: 10, // Number of partners per page
    };
  },
  computed: {
    paginatedPartners() {
      const start = (this.currentPage - 1) * this.partnersPerPage;
      const end = start + this.partnersPerPage;
      return this.partners.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.partners.length / this.partnersPerPage);
    },
  },
  methods: {
    fetchPartners() {
      axios.get("/api/partners").then((response) => {
        this.partners = response.data;
      })
      .catch((error) => {
        this.toast.error(error.response?.data?.message || "Không thể tải danh sách đối tác!");
      });
    },
    deletePartner(id) {
      if (confirm("Bạn có chắc muốn xóa đối tác này không?")) {
        axios.delete(`/api/partners/${id}`).then(() => {
          this.toast.success("Xóa đối tác thành công!");
          this.fetchPartners();
        })
        .catch((error) => {
          this.toast.error(error.response?.data?.message || "Xóa đối tác thất bại!");
        });
      }
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
  margin: 10px 0 20px 20px;
}
/* Buttons */
  .btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #28a745;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: bold;
      transition: background-color 0.2s;
      margin: 10px 0 15px 20px;
  }

  .btn:hover {
      background-color: #218838;
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
      background-color: #007bff;
      color: white;
      text-transform: uppercase;
      font-size: 13px;
  }

  tr:nth-child(even) {
      background-color: #f9f9f9;
  }

  tr:hover {
      background-color: #f1f1f1;
      cursor: default;
  }

  /* Action buttons */
  .actions a {
      color: #17a2b8; /* Bootstrap info color */
      text-decoration: none;
      font-weight: bold;
      padding: 6px 10px;
      margin-right: 5px;
      border: 1px solid #17a2b8;
      border-radius: 4px;
      transition: background-color 0.2s, color 0.2s;
  }

      .actions a:hover {
      background-color: #17a2b8;
      color: white;
  }

  .actions button {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
  }

  .actions button:hover {
      background-color: #c82333;
  }

  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
  }

  .pagination button {
    padding: 8px 12px;
    margin: 0 5px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
  }

  .pagination button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }

  .pagination button:hover:not(:disabled) {
    background-color: #0056b3;
  }

  .pagination span {
    font-size: 14px;
    font-weight: bold;
  }
</style>