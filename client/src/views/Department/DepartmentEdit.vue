<template>
    <div class="container">
        <div class="header">
            <h1>Chỉnh sửa phòng ban</h1>
        </div>

        <div class="alert alert-danger" v-if="error">
            {{ error }}
        </div>

        <div v-if="loading" class="loading">
            <p>Đang tải dữ liệu...</p>
        </div>

        <form v-else @submit.prevent="updateDepartment" class="department-form">
            <div class="form-group">
                <label for="name">Tên phòng ban <span class="required">*</span></label>
                <input type="text" id="name" v-model="department.name" class="form-control" required
                    placeholder="Nhập tên phòng ban" @input="validateName">
                <span v-if="errors.name" class="error">{{ errors.name }}</span>
            </div>

            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea id="description" v-model="department.description" class="form-control" rows="4"
                    placeholder="Nhập mô tả phòng ban"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                    {{ isSubmitting ? 'Đang xử lý...' : 'Cập nhật phòng ban' }}
                </button>
                <router-link to="/departments" class="btn btn-secondary">Quay lại</router-link>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            department: {
                name: '',
                description: ''
            },
            error: '',
            loading: true,
            isSubmitting: false,
            errors: {
                name: ''
            }
        };
    },
    created() {
        this.loadDepartment();
    },
    methods: {
        validateName() {
            const numberRegex = /[0-9]/;
            const specialCharsRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>/?]+/;

            if (numberRegex.test(this.department.name)) {
                this.errors.name = 'Tên phòng ban không được chứa số';
                return false;
            }

            if (specialCharsRegex.test(this.department.name)) {
                this.errors.name = 'Tên phòng ban không được chứa ký tự đặc biệt';
                return false;
            }

            this.errors.name = '';
            return true;
        },
        async loadDepartment() {
            try {
                const response = await axios.get(`/api/departments/${this.$route.params.id}`);
                this.department = response.data;
            } catch (error) {
                console.error('Lỗi khi tải thông tin phòng ban:', error);
                if (error.response && error.response.status === 404) {
                    this.error = 'Không tìm thấy phòng ban';
                } else {
                    this.error = 'Đã xảy ra lỗi khi tải thông tin phòng ban';
                }
            } finally {
                this.loading = false;
            }
        },
        async updateDepartment() {
            if (!this.validateName()) {
                return;
            }

            this.isSubmitting = true;
            this.error = '';

            try {
                const response = await axios.put(`/api/departments/${this.$route.params.id}`, this.department);
                this.$router.push({
                    path: '/departments',
                    query: { message: `Đã cập nhật phòng ban "${response.data.department.name}" thành công` }
                });
            } catch (error) {
                console.error('Lỗi khi cập nhật phòng ban:', error);
                if (error.response && error.response.data && error.response.data.message) {
                    this.error = error.response.data.message;
                } else {
                    this.error = 'Đã xảy ra lỗi khi cập nhật phòng ban';
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

.department-form {
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

.loading {
    text-align: center;
    padding: 20px;
}

.error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}
</style>