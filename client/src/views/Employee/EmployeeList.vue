<template>
    <div class="container">
        <div class="search-wrapper">
            <div class="search-container">
                <input v-model="searchQuery" type="text" placeholder="Tìm kiếm theo ID, tên, phòng ban, vị trí..."
                    class="search-input">
            </div>
            <router-link to="/create" class="btn btn-add">Thêm nhân viên</router-link>
        </div>

        <div class="table-wrapper">
            <table class="employee-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Họ và Tên</th>
                        <th>Giới tính</th>
                        <th>Số điện thoại</th>
                        <th>Phòng ban</th>
                        <th>Vị trí</th>
                        <th>Trình độ</th>
                        <th>Ngày bắt đầu</th>
                        <th>Hợp đồng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="employee in paginatedEmployees" :key="employee.id">
                        <td>{{ "NV" + employee.id }}</td>
                        <td>
                            <img v-if="employee.profile_image"
                                :src="'http://127.0.0.1:8000/storage/' + employee.profile_image" alt="Profile"
                                class="profile-img">
                            <span v-else>Không có ảnh</span>
                        </td>
                        <td>{{ truncate(employee.name, 20) }}</td>
                        <td>{{ employee.gender }}</td>
                        <td>{{ employee.phone }}</td>
                        <td>{{ truncate(employee.department_name, 15) }}</td>
                        <td>{{ truncate(employee.position_name, 15) }}</td>
                        <td>{{ truncate(employee.education_level, 15) }}</td> <!-- Hiển thị Trình độ -->
                        <td>{{ formatDate(employee.start_date) }}</td>
                        <td>{{ truncate(employee.salary_type, 15) }}</td>
                        <td>
                            <router-link :to="'/edit/' + employee.id" class="btn btn-edit">Sửa</router-link>
                            <button @click="confirmDelete(employee)" class="btn btn-delete">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <button @click="prevPage" :disabled="currentPage === 1" class="btn btn-page">
                Trang trước
            </button>
            <span>Trang {{ currentPage }} / {{ totalPages }}</span>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="btn btn-page">
                Trang sau
            </button>
        </div>

        <!-- Modal xác nhận xóa -->
        <div class="modal" v-if="showDeleteModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Xác nhận xóa</h2>
                    <span class="close" @click="showDeleteModal = false">&times;</span>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa nhân viên?</p>
                </div>
                <div class="modal-footer">
                    <button @click="showDeleteModal = false" class="btn btn-secondary">Hủy</button>
                    <button @click="deleteEmployee" class="btn btn-danger">Xóa</button>
                </div>
            </div>
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
            itemsPerPage: 5, // Giới hạn 5 thông tin mỗi trang
            searchQuery: '',
            showDeleteModal: false,
            employeeToDelete: null
        };
    },
    mounted() {
        this.fetchEmployees();
    },
    computed: {
        filteredEmployees() {
            if (!this.searchQuery) {
                return this.employees;
            }

            const query = this.searchQuery.toLowerCase();
            return this.employees.filter(employee => {
                const employeeId = `nv${employee.id}`.toLowerCase();
                const employeeName = employee.name.toLowerCase();
                const employeeDepartment = employee.department_name.toLowerCase();
                const employeePosition = employee.position_name.toLowerCase();
                const employeeEducationLevel = employee.education_level.toLowerCase();
                const employeeSalaryType = employee.salary_type.toLowerCase();

                return (
                    employeeId.includes(query) ||
                    employeeName.includes(query) ||
                    employeeDepartment.includes(query) ||
                    employeePosition.includes(query) ||
                    employeeEducationLevel.includes(query) ||
                    employeeSalaryType.includes(query)
                );
            });
        },
        totalPages() {
            return Math.ceil(this.filteredEmployees.length / this.itemsPerPage);
        },
        paginatedEmployees() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredEmployees.slice(start, end);
        },
    },
    watch: {
        searchQuery() {
            this.currentPage = 1; // Reset về trang 1 khi thay đổi tìm kiếm
        }
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
        confirmDelete(employee) {
            this.employeeToDelete = employee;
            this.showDeleteModal = true;
        },

        async deleteEmployee() {
            if (!this.employeeToDelete) return;
            
            try {
                await axios.delete(`/api/employees/${this.employeeToDelete.id}`);
                this.fetchEmployees();
                this.showDeleteModal = false;
                this.employeeToDelete = null;
            } catch (error) {
                console.error('Error deleting employee:', error);
                this.showDeleteModal = false;
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
    max-width: 83vw;
    padding: 20px;
}

.search-wrapper {
    position: relative;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.search-container {
    position: relative;
    width: 350px;
    transition: all 0.3s ease;
}

.search-input {
    width: 100%;
    padding: 12px 40px 12px 15px;
    border: 2px solid #ddd;
    border-radius: 25px;
    font-size: 14px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.search-input:hover {
    background-color: #f8f9fa;
}

.search-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
    width: 105%;
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
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

/* .btn-add:hover {
    background-color: #218838;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
} */

.table-wrapper {
    position: relative;
    margin-top: 20px;
    overflow-x: auto;
    /* Kích hoạt thanh cuộn ngang khi tràn */
    white-space: nowrap;
    /* Ngăn xuống dòng */
}

.employee-table {
    width: 100%;
    min-width: 1300px;
    /* Tăng min-width để phù hợp với cột mới */
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
    background-color: #f8f9fa;
    color: black;
    font-weight: bold;
    border-bottom: 2px solid #dee2e6;
}

.employee-table tr:hover {
    background-color: #f5f5f5;
}

.profile-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
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

/* Modal styles */
.modal {
    display: block;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border-radius: 5px;
    width: 400px;
    position: relative;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(-100px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
    color: #333;
}

.close {
    font-size: 28px;
    font-weight: bold;
    color: #666;
    cursor: pointer;
}

.close:hover {
    color: #000;
}

.modal-body {
    margin-bottom: 20px;
}

.modal-body p {
    margin: 0;
    font-size: 1rem;
    color: #555;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-top: 1px solid #ddd;
    padding-top: 15px;
}

.modal-footer button {
    padding: 8px 20px;
    border-radius: 4px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
    border: none;
}

.btn-danger:hover {
    background-color: #c82333;
}
</style>
