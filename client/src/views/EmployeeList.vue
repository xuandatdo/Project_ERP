<template>
    <div class="container">

        <div class="table-wrapper">
            <router-link to="/create" class="btn btn-add">Thêm nhân viên</router-link>
            <table class="employee-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Giới tính</th>
                        <th>Số điện thoại</th>
                        <th>Ngày bắt đầu</th>
                        <th>Hợp đồng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="employee in paginatedEmployees" :key="employee.id">
                        <td>{{ "NV" + employee.id }}</td>
                        <td>{{ truncate(employee.name, 20) }}</td>
                        <td>{{ employee.gender }}</td>
                        <td>{{ employee.phone }}</td>
                        <td>{{ formatDate(employee.start_date) }}</td>
                        <td>{{ truncate(employee.salary_type, 15) }}</td>
                        <td>
                            <router-link :to="'/edit/' + employee.id" class="btn btn-edit">Sửa</router-link>
                            <button @click="deleteEmployee(employee.id)" class="btn btn-delete">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button @click="prevPage" :disabled="currentPage === 1" class="btn btn-page">
                Trang trước
            </button>
            <span>Trang {{ currentPage }} / {{ totalPages }}</span>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="btn btn-page">
                Trang sau
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            employees: [],
            currentPage: 1,
            itemsPerPage: 10,
        };
    },
    mounted() {
        this.fetchEmployees();
    },
    computed: {
        totalPages() {
            return Math.ceil(this.employees.length / this.itemsPerPage);
        },
        paginatedEmployees() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.employees.slice(start, end);
        },
    },
    methods: {
        async fetchEmployees() {
            try {
                const response = await axios.get('/api/employees');
                this.employees = response.data;
            } catch (error) {
                console.error('Error fetching employees:', error);
            }
        },
        async deleteEmployee(id) {
            if (confirm('Bạn có chắc muốn xóa nhân viên này?')) {
                try {
                    await axios.delete(`/api/employees/${id}`);
                    this.fetchEmployees();
                } catch (error) {
                    console.error('Error deleting employee:', error);
                }
            }
        },
        truncate(text, maxLength) {
            if (!text || text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        },
        formatDate(date) {
            if (!date) return '';
            const [year, month, day] = date.split('-');
            return `${day}/${month}/${year}`;
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
    },
};
</script>

<style scoped>
.container {
    max-width: 2000px;
    /* margin: 0 auto; */
    padding: 20px;
}

h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

.table-wrapper {
    position: relative;
    margin-top: 40px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
}

.btn-add {
    background-color: #28a745;
    color: white;
    position: absolute;
    top: -40px;
    right: 0;
    padding: 10px;
    /* margin-bottom: 15px; */
}

.btn-add:hover {
    background-color: #218838;
}

.employee-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border: 1px solid #ddd;
}

.employee-table th,
.employee-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    border-right: 1px solid #ddd;
}

.employee-table th:last-child,
.employee-table td:last-child {
    border-right: none;
}

.employee-table th {
    background-color: #007bff;
    color: white;
    font-weight: bold;
}

.employee-table tr:hover {
    background-color: #f5f5f5;
}

.btn-edit {
    background-color: #ffc107;
    color: #333;
    margin-right: 10px;
}

.btn-edit:hover {
    background-color: #e0a800;
}

.btn-delete {
    background-color: #dc3545;
    color: white;
}

.btn-delete:hover {
    background-color: #c82333;
}

.pagination {
    margin-top: 20px;
    text-align: center;
}

.btn-page {
    background-color: #007bff;
    color: white;
    margin: 0 10px;
}

.btn-page:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
}

.btn-page:hover:not(:disabled) {
    background-color: #0056b3;
}

span {
    font-size: 16px;
    margin: 0 10px;
}
</style>