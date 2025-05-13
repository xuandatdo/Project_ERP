<template>
    <div class="container">
        <!-- <div class="header">
            <h1>Thêm vị trí</h1>
            
        </div> -->

        <div class="alert alert-danger" v-if="error">
            {{ error }}
        </div>

        <form @submit.prevent="createPosition" class="position-form">
            <div class="form-group">
                <label for="name">Tên vị trí <span class="required">*</span></label>
                <input type="text" id="name" v-model="position.name" class="form-control" required
                    placeholder="Nhập tên vị trí" @input="validateName">
                <span v-if="errors.name" class="error">{{ errors.name }}</span>
            </div>

            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" v-model="position.description" class="form-control" rows="4"
                    placeholder="Nhập mô tả vị trí"></textarea>
            </div>

            <div class="form-group">
                <label for="department_id">Phòng ban <span class="required">*</span></label>
                <select id="department_id" v-model="position.department_id" class="form-control" required>
                    <option value="">-- Chọn phòng ban --</option>
                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                    {{ isSubmitting ? 'Đang xử lý...' : 'Thêm vị trí' }}
                </button>
                <router-link to="/positions" class="btn btn-secondary">Quay lại</router-link>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            position: {
                name: '',
                description: '',
                department_id: ''
            },
            departments: [],
            error: '',
            isSubmitting: false,
            errors: {
                name: ''
            }
        };
    },
    created() {
        this.loadDepartments();

        // Nếu có department_id trong query params, thiết lập giá trị mặc định
        if (this.$route.query.department) {
            this.position.department_id = parseInt(this.$route.query.department);
        }
    },
    methods: {
        validateName() {
            const numberRegex = /[0-9]/;
            const specialCharsRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>/?]+/;

            if (numberRegex.test(this.position.name)) {
                this.errors.name = 'Tên vị trí không được chứa số';
                return false;
            }

            if (specialCharsRegex.test(this.position.name)) {
                this.errors.name = 'Tên vị trí không được chứa ký tự đặc biệt';
                return false;
            }

            this.errors.name = '';
            return true;
        },
        async loadDepartments() {
            try {
                const response = await axios.get('/api/departments');
                this.departments = response.data;
            } catch (error) {
                console.error('Lỗi khi tải danh sách phòng ban:', error);
                this.error = 'Đã xảy ra lỗi khi tải danh sách phòng ban';
            }
        },
        async createPosition() {
            if (!this.validateName()) {
                return;
            }

            this.isSubmitting = true;
            this.error = '';

            try {
                const response = await axios.post('/api/positions', this.position);
                this.$router.push({
                    path: '/positions',
                    query: { message: `Đã thêm vị trí "${response.data.position.name}" thành công` }
                });
            } catch (error) {
                console.error('Lỗi khi tạo vị trí:', error);
                if (error.response && error.response.data && error.response.data.message) {
                    this.error = error.response.data.message;
                } else {
                    this.error = 'Đã xảy ra lỗi khi tạo vị trí';
                }
            } finally {
                this.isSubmitting = false;
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
}

.position-form {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.required {
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.form-control:focus {
    border-color: #007bff;
    outline: none;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0069d9;
}

.btn-primary:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.alert {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}
</style>