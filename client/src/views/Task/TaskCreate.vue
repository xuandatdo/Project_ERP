<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Thêm công việc mới</h2>
            </div>
            <div class="card-body">
                <form @submit.prevent="createTask">
                    <div class="form-group">
                        <label for="name">Tên công việc</label>
                        <input type="text" id="name" v-model="task.name" class="form-control" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="employee_id">Người phụ trách</label>
                            <select id="employee_id" v-model="task.employee_id" class="form-control" required>
                                <option value="">Chọn người phụ trách</option>
                                <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                    {{ employee.name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="start_date">Ngày bắt đầu</label>
                            <input type="date" id="start_date" v-model="task.start_date" class="form-control" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="end_date">Ngày kết thúc</label>
                            <input type="date" id="end_date" v-model="task.end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="priority">Ưu tiên</label>
                            <select id="priority" v-model="task.priority" class="form-control" required>
                                <option value="">Chọn mức độ ưu tiên</option>
                                <option value="Cao">Cao</option>
                                <option value="Trung bình">Trung bình</option>
                                <option value="Thấp">Thấp</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="progress">Tiến độ (%)</label>
                            <input type="number" id="progress" v-model.number="task.progress" class="form-control"
                                min="0" max="100" required>
                            <span v-if="errors.progress" class="error">{{ errors.progress }}</span>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="status">Trạng thái</label>
                            <select id="status" v-model="task.status" class="form-control" required>
                                <option value="">Chọn trạng thái</option>
                                <option value="Chưa bắt đầu">Chưa bắt đầu</option>
                                <option value="Đang thực hiện">Đang thực hiện</option>
                                <option value="Hoàn thành">Hoàn thành</option>
                                <option value="Tạm dừng">Tạm dừng</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả công việc</label>
                        <textarea id="description" v-model="task.description" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Thêm công việc</button>
                        <router-link to="/tasks" class="btn btn-secondary">Hủy</router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { ref, reactive, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';

export default {
    setup() {
        const router = useRouter();
        const employees = ref([]);
        const errors = ref({});
        const task = reactive({
            name: '',
            employee_id: '',
            start_date: '',
            end_date: '',
            priority: '',
            progress: 0,
            status: 'Chưa bắt đầu',
            description: ''
        });

        // Watch cho trạng thái để cập nhật tiến độ
        watch(() => task.status, (newStatus) => {
            if (newStatus === 'Chưa bắt đầu') {
                task.progress = 0;
            } else if (newStatus === 'Hoàn thành') {
                task.progress = 100;
            } else if (newStatus === 'Tạm dừng' && task.progress === 100) {
                task.progress = 99;
            }
        });

        // Watch cho tiến độ để cập nhật trạng thái
        watch(() => task.progress, (newProgress) => {
            if (newProgress === 0) {
                task.status = 'Chưa bắt đầu';
            } else if (newProgress === 100) {
                if (task.status === 'Tạm dừng') {
                    task.progress = 99;
                } else {
                    task.status = 'Hoàn thành';
                }
            } else if (newProgress > 0 && newProgress < 100) {
                if (task.status === 'Chưa bắt đầu' || task.status === 'Hoàn thành') {
                    task.status = 'Đang thực hiện';
                }
            }
        });

        const fetchEmployees = async () => {
            try {
                const response = await axios.get('/api/employees');
                employees.value = response.data;
            } catch (error) {
                console.error('Error fetching employees:', error);
            }
        };

        const validateTask = () => {
            errors.value = {};

            if (task.status === 'Chưa bắt đầu' && task.progress !== 0) {
                errors.value.progress = 'Công việc chưa bắt đầu phải có tiến độ 0%';
                return false;
            }
            if (task.status === 'Hoàn thành' && task.progress !== 100) {
                errors.value.progress = 'Công việc hoàn thành phải có tiến độ 100%';
                return false;
            }
            if (task.status === 'Tạm dừng' && task.progress === 100) {
                errors.value.progress = 'Công việc tạm dừng không thể có tiến độ 100%';
                return false;
            }
            if (task.status === 'Đang thực hiện' && (task.progress < 1 || task.progress > 99)) {
                errors.value.progress = 'Tiến độ phải từ 1% đến 99% khi công việc đang thực hiện';
                return false;
            }
            return true;
        };

        const createTask = async () => {
            if (!validateTask()) return;

            try {
                console.log('Sending task data:', task);
                const response = await axios.post('/api/tasks', task);
                console.log('Server response:', response.data);
                router.push('/tasks');
            } catch (error) {
                console.error('Error creating task:', error.response?.data || error.message);
                alert('Có lỗi xảy ra khi tạo công việc: ' + (error.response?.data?.message || error.message));
            }
        };

        onMounted(() => {
            fetchEmployees();
        });

        return {
            task,
            employees,
            errors,
            createTask
        };
    }
};
</script>

<style scoped>
.container {
    padding: 20px;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.card-header h2 {
    margin: 0;
    color: #333;
}

.card-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-control:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.form-row {
    display: flex;
    margin-right: -15px;
    margin-left: -15px;
}

.form-row>.form-group {
    padding-right: 15px;
    padding-left: 15px;
    margin-bottom: 20px;
}

.col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
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

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.error {
    color: red;
    font-size: 12px;
    margin-top: 5px;
}
</style>