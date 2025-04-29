<template>
    <div class="container">
        <div class="header">
            <h1></h1>
            <router-link to="/tasks/create" class="btn btn-primary">Thêm công việc mới</router-link>
        </div>

        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên công việc</th>
                        <th>Người phụ trách</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Ưu tiên</th>
                        <th>Tiến độ</th>
                        <th>Trạng thái</th>
                        <th>Mô tả</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task in tasks" :key="task.id">
                        <td>{{ task.id }}</td>
                        <td>{{ task.name }}</td>
                        <td>{{ task.employee_name }}</td>
                        <td>{{ formatDate(task.start_date) }}</td>
                        <td>{{ formatDate(task.end_date) }}</td>
                        <td>{{ task.priority }}</td>
                        <td>{{ task.progress }}%</td>
                        <td>{{ task.status }}</td>
                        <td>{{ task.description }}</td>
                        <td>
                            <div class="action-buttons">
                                <router-link :to="`/tasks/${task.id}/edit`" class="btn btn-sm btn-edit btn-primary">Sửa</router-link>
                                <button @click="confirmDelete(task)" class="btn btn-sm btn-delete btn-danger">Xóa</button>
                            </div>
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
                    <p>Bạn có chắc chắn muốn xóa công việc?</p>
                </div>
                <div class="modal-footer">
                    <button @click="showDeleteModal = false" class="btn btn-secondary">Hủy</button>
                    <button @click="deleteTask" class="btn btn-danger">Xóa</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { ref, onMounted } from 'vue';

export default {
    setup() {
        const tasks = ref([]);
        const message = ref('');
        const currentPage = ref(1);
        const totalPages = ref(1);
        const itemsPerPage = 10;
        const showDeleteModal = ref(false);
        const taskToDelete = ref(null);

        const fetchTasks = async (page) => {
            try {
                const response = await axios.get(`/api/tasks?page=${page}`);
                tasks.value = response.data.data;
                totalPages.value = Math.ceil(response.data.total / itemsPerPage);
            } catch (error) {
                console.error('Error fetching tasks:', error);
                message.value = 'Có lỗi xảy ra khi tải danh sách công việc';
            }
        };

        const confirmDelete = (task) => {
            taskToDelete.value = task;
            showDeleteModal.value = true;
        };

        const deleteTask = async () => {
            if (!taskToDelete.value) return;
            
            try {
                await axios.delete(`/api/tasks/${taskToDelete.value.id}`);
                message.value = `Đã xóa công việc "${taskToDelete.value.name}" thành công`;
                fetchTasks(currentPage.value);
                showDeleteModal.value = false;
                taskToDelete.value = null;
            } catch (error) {
                console.error('Error deleting task:', error);
                if (error.response && error.response.data && error.response.data.message) {
                    message.value = error.response.data.message;
                } else {
                    message.value = 'Có lỗi xảy ra khi xóa công việc';
                }
                showDeleteModal.value = false;
            }
        };

        const formatDate = (date) => {
            return new Date(date).toLocaleDateString('vi-VN');
        };

        const getPriorityClass = (priority) => {
            const classes = {
                'Cao': 'priority-high',
                'Trung bình': 'priority-medium',
                'Thấp': 'priority-low'
            };
            return `priority ${classes[priority] || ''}`;
        };

        const getProgressClass = (progress) => {
            if (progress < 30) return 'bg-danger';
            if (progress < 70) return 'bg-warning';
            return 'bg-success';
        };

        const getStatusClass = (status) => {
            const classes = {
                'Chưa bắt đầu': 'status-not-started',
                'Đang thực hiện': 'status-in-progress',
                'Hoàn thành': 'status-completed',
                'Tạm dừng': 'status-paused'
            };
            return `status ${classes[status] || ''}`;
        };

        const prevPage = () => {
            if (currentPage.value > 1) {
                currentPage.value--;
                fetchTasks(currentPage.value);
            }
        };

        const nextPage = () => {
            if (currentPage.value < totalPages.value) {
                currentPage.value++;
                fetchTasks(currentPage.value);
            }
        };

        onMounted(() => {
            fetchTasks(1);
        });

        return {
            tasks,
            message,
            currentPage,
            totalPages,
            deleteTask,
            formatDate,
            getPriorityClass,
            getProgressClass,
            getStatusClass,
            prevPage,
            nextPage,
            showDeleteModal,
            taskToDelete,
            confirmDelete
        };
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
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table th,
.table td {
    padding: 12px;
    text-align: left;
    border: 1px solid #dee2e6;
}

.table th {
    background-color: #f5f5f5;
    font-weight: bold;
    white-space: nowrap;
}

.table td {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.table-responsive {
    overflow-x: auto;
    min-width: 100%;
}
.action-buttons {
    display: flex;
    gap: 8px;
}

.btn {
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    border: none;
    font-size: 14px;
    text-decoration: none;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-edit {
    background-color: #ffc107;
    color: #333;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-page {
    background-color: #6c757d;
    color: white;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    margin-top: 20px;
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

.btn-danger:hover {
    background-color: #c82333;
}
</style>