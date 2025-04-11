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
                        <th>Trình độ</th> <!-- Thêm cột Trình độ -->
                        <th>Ngày bắt đầu</th>
                        <th>Hợp đồng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="employee in paginatedEmployees" :key="employee.id">
                        <td>{{ "NV" + employee.id }}</td>
                        <td>
                            <img v-if="employee.profile_image" :src="'/storage/' + employee.profile_image" alt="Profile"
                                class="profile-img">
                            <span v-else>Không có ảnh</span>
                        </td>
                        <td>{{ truncate(employee.name, 20) }}</td>
                        <td>{{ employee.gender }}</td>
                        <td>{{ employee.phone }}</td>
                        <td>{{ truncate(employee.department, 15) }}</td>
                        <td>{{ truncate(employee.position, 15) }}</td>
                        <td>{{ truncate(employee.education_level, 15) }}</td> <!-- Hiển thị Trình độ -->
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
            itemsPerPage: 5, // Giới hạn 5 thông tin mỗi trang
            searchQuery: ''
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
                const employeeDepartment = employee.department.toLowerCase();
                const employeePosition = employee.position.toLowerCase();
                const employeeEducationLevel = employee.education_level.toLowerCase(); // Thêm tìm kiếm theo Trình độ

                return (
                    employeeId.includes(query) ||
                    employeeName.includes(query) ||
                    employeeDepartment.includes(query) ||
                    employeePosition.includes(query) ||
                    employeeEducationLevel.includes(query) // Tìm kiếm theo Trình độ
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
    max-width: 85vw;
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
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.btn-add:hover {
    background-color: #218838;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

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
    background-color: #007bff;
    color: white;
    font-weight: bold;
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
</style>