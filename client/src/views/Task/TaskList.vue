<template>
    <div class="container">
        <div class="header">
            <h1></h1>
            <router-link to="/tasks/create" class="btn btn-primary">Thêm công việc mới</router-link>
        </div>

        <div class="alert alert-info" v-if="message">
            {{ message }}
        </div>

        <div class="table-responsive">
            <table class="table">
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
                        <td>
                            <span :class="getPriorityClass(task.priority)">{{ task.priority }}</span>
                        </td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" :style="{ width: task.progress + '%' }" :class="getProgressClass(task.progress)">
                                    {{ task.progress }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <span :class="getStatusClass(task.status)">{{ task.status }}</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <router-link :to="`/tasks/${task.id}/edit`" class="btn btn-sm btn-edit btn-primary">Sửa</router-link>
                                <button @click="deleteTask(task.id)" class="btn btn-sm btn-delete btn-danger">Xóa</button>
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

        const deleteTask = async (id) => {
            if (confirm('Bạn có chắc chắn muốn xóa công việc này?')) {
                try {
                    await axios.delete(`/api/tasks/${id}`);
                    message.value = 'Xóa công việc thành công';
                    fetchTasks(currentPage.value);
                } catch (error) {
                    console.error('Error deleting task:', error);
                    message.value = 'Có lỗi xảy ra khi xóa công việc';
                }
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
            nextPage
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
}

.table th {
    background-color: #f5f5f5;
    font-weight: bold;
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

.btn-edit{
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

.progress {
    height: 20px;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    color: black;
    text-align: center;
    line-height: 20px;
    transition: width 0.3s ease;
    font-weight: bold;
}

.bg-danger {
    background-color: #ff4444;
}

.bg-warning {
    background-color: #ffbb33;
}

.bg-success {
    background-color: rgb(243, 229, 229);
}
.priority,
.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.priority-high {
    background-color: #dc3545;
    color: white;
}

.priority-medium {
    background-color: #ffc107;
    color: black;
}

.priority-low {
    background-color: #28a745;
    color: white;
}

.status-not-started {
    background-color: #6c757d;
    color: white;
}

.status-in-progress {
    background-color: #007bff;
    color: white;
}

.status-completed {
    background-color: #28a745;
    color: white;
}

.status-paused {
    background-color: #ffc107;
    color: black;
}
</style>