<template>
    <div class="container">
        <div class="header">
            <h1></h1>
            <div class="header-actions">
                <select v-model="selectedDepartment" @change="filterByDepartment" class="department-filter">
                    <option value="">Tất cả phòng ban</option>
                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
                <router-link to="/positions/create" class="btn btn-primary">Thêm chức vụ mới</router-link>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên chức vụ</th>
                        <th>Mô tả</th>
                        <th>Phòng ban</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="position in paginatedPositions" :key="position.id">
                        <td>{{ position.id }}</td>
                        <td>{{ position.name }}</td>
                        <td>{{ position.description }}</td>
                        <td>{{ getDepartmentName(position.department_id) }}</td>
                        <td>
                            <div class="action-buttons">
                                <router-link :to="`/positions/${position.id}/edit`" class="btn btn-sm btn-edit btn-primary">Sửa</router-link>
                                <button @click="confirmDelete(position)" class="btn btn-sm btn-danger">Xóa</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="positions.length === 0">
                        <td colspan="5" class="text-center">Không có vị trí nào</td>
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
                    <p>Bạn có chắc chắn muốn xóa vị trí?</p>
                </div>
                <div class="modal-footer">
                    <button @click="showDeleteModal = false" class="btn btn-secondary">Hủy</button>
                    <button @click="deletePosition" class="btn btn-danger">Xóa</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    computed: {
        totalPages() {
            return Math.ceil(this.positions.length / this.itemsPerPage);
        },
        paginatedPositions() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.positions.slice(start, end);
        }
    },
    data() {
        return {
            positions: [],
            departments: [],
            selectedDepartment: '',
            message: '',
            showDeleteModal: false,
            positionToDelete: null,
            currentPage: 1,
            itemsPerPage: 10
        };
    },
    created() {
        this.loadDepartments();
        this.loadPositions();
        
        // Kiểm tra nếu có department query param
        if (this.$route.query.department) {
            this.selectedDepartment = parseInt(this.$route.query.department);
        }
        
        // Kiểm tra nếu có message query param
        if (this.$route.query.message) {
            this.message = this.$route.query.message;
        }
    },
    methods: {
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
        async loadDepartments() {
            try {
                const response = await axios.get('/api/departments');
                this.departments = response.data;
            } catch (error) {
                console.error('Lỗi khi tải danh sách phòng ban:', error);
            }
        },
        async loadPositions() {
            try {
                let url = '/api/positions';
                if (this.selectedDepartment) {
                    url = `/api/positions/department/${this.selectedDepartment}`;
                }
                const response = await axios.get(url);
                this.positions = response.data;
            } catch (error) {
                console.error('Lỗi khi tải danh sách vị trí:', error);
                this.message = 'Đã xảy ra lỗi khi tải danh sách vị trí';
            }
        },
        getDepartmentName(departmentId) {
            const department = this.departments.find(dept => dept.id === departmentId);
            return department ? department.name : 'Không xác định';
        },
        filterByDepartment() {
            // Cập nhật URL với department query param
            if (this.selectedDepartment) {
                this.$router.replace({ query: { ...this.$route.query, department: this.selectedDepartment } });
            } else {
                const query = { ...this.$route.query };
                delete query.department;
                this.$router.replace({ query });
            }
            
            this.loadPositions();
        },
        confirmDelete(position) {
            this.positionToDelete = position;
            this.showDeleteModal = true;
        },
        confirmDelete(position) {
            this.positionToDelete = position;
            this.showDeleteModal = true;
        },
        async deletePosition() {
            if (!this.positionToDelete) return;
            
            try {
                await axios.delete(`/api/positions/${this.positionToDelete.id}`);
                this.message = `Đã xóa vị trí "${this.positionToDelete.name}" thành công`;
                this.loadPositions();
                this.showDeleteModal = false;
                this.positionToDelete = null;
            } catch (error) {
                console.error('Error deleting position:', error);
                if (error.response && error.response.data && error.response.data.message) {
                    this.message = error.response.data.message;
                } else {
                    this.message = 'Đã xảy ra lỗi khi xóa vị trí';
                }
                this.showDeleteModal = false;
            }
        }
    }
};
</script>

<style scoped>
.container {
    padding: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.department-filter {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ddd;
    min-width: 200px;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table th, .table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
}

.table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.table tbody tr:hover {
    background-color: #f1f1f1;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.btn {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background-color: #ffc107;
    color: #333;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 14px;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}
.btn-edit{
    background-color: #ffc107;
    color: #333;
}
.btn-danger {
    background-color: #dc3545;
    color: white;
}


.btn-danger:hover {
    background-color: #c82333;
}

.alert {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

/* Modal styles */
.modal {
    display: block;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #ddd;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
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

.pagination span {
    font-size: 16px;
    margin: 0 10px;
}

@media (max-width: 768px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-actions {
        margin-top: 10px;
        width: 100%;
    }
    
    .modal-content {
        width: 90%;
    }
}
</style>