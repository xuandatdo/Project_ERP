<template>
    <div class="container">
        <form @submit.prevent="updateEmployee" class="employee-form">
            <div class="form-grid">
                <!-- Tên -->
                <div class="form-group">
                    <label for="name">Tên <span class="required">*</span></label>
                    <input v-model="form.name" id="name" type="text" placeholder="Nhập tên" required />
                    <span v-if="errors.name" class="error">{{ errors.name }}</span>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input v-model="form.email" id="email" type="email" placeholder="Nhập email" required />
                    <span v-if="errors.email" class="error">{{ errors.email }}</span>
                </div>

                <!-- Địa chỉ -->
                <div class="form-group">
                    <label for="address">Địa chỉ <span class="required">*</span></label>
                    <input v-model="form.address" id="address" type="text" placeholder="Nhập địa chỉ" required />
                    <span v-if="errors.address" class="error">{{ errors.address }}</span>
                </div>

                <!-- Điện thoại -->
                <div class="form-group">
                    <label for="phone">Điện thoại <span class="required">*</span></label>
                    <input v-model="form.phone" id="phone" type="tel" placeholder="Nhập số điện thoại" required
                        pattern="[0-9]{10}" />
                    <span v-if="errors.phone" class="error">{{ errors.phone }}</span>
                </div>

                <!-- Giới tính -->
                <div class="form-group">
                    <label for="gender">Giới tính <span class="required">*</span></label>
                    <select v-model="form.gender" id="gender" required>
                        <option value="" disabled>Chọn giới tính</option>
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                        <!-- <option value="Khác">Khác</option> -->
                    </select>
                    <span v-if="errors.gender" class="error">{{ errors.gender }}</span>
                </div>

                <!-- Vị trí -->
                <div class="form-group">
                    <label for="position">Vị trí <span class="required">*</span></label>
                    <input v-model="form.position" id="position" type="text" placeholder="Nhập vị trí" required />
                    <span v-if="errors.position" class="error">{{ errors.position }}</span>
                </div>

                <!-- Phòng ban -->
                <div class="form-group">
                    <label for="department">Phòng ban <span class="required">*</span></label>
                    <input v-model="form.department" id="department" type="text" placeholder="Nhập phòng ban"
                        required />
                    <span v-if="errors.department" class="error">{{ errors.department }}</span>
                </div>

                <!-- Địa điểm làm việc -->
                <div class="form-group">
                    <label for="workplace">Địa điểm làm việc <span class="required">*</span></label>
                    <input v-model="form.workplace" id="workplace" type="text" placeholder="Nhập địa điểm" required />
                    <span v-if="errors.workplace" class="error">{{ errors.workplace }}</span>
                </div>

                <!-- Ngày bắt đầu -->
                <div class="form-group">
                    <label for="start_date">Ngày bắt đầu <span class="required">*</span></label>
                    <input v-model="form.start_date" id="start_date" type="date" required />
                    <span v-if="errors.start_date" class="error">{{ errors.start_date }}</span>
                </div>

                <!-- Ngày kết thúc (không bắt buộc) -->
                <div class="form-group">
                    <label for="end_date">Ngày kết thúc</label>
                    <input v-model="form.end_date" id="end_date" type="date" />
                </div>

                <!-- Kết thúc thử việc (không bắt buộc) -->
                <div class="form-group">
                    <label for="probation_end">Kết thúc thử việc</label>
                    <input v-model="form.probation_end" id="probation_end" type="date" />
                </div>

                <!-- Thời gian làm việc -->
                <div class="form-group">
                    <label for="work_hours">Thời gian làm việc <span class="required">*</span></label>
                    <input v-model="form.work_hours" id="work_hours" type="text" placeholder="Ví dụ: 8h/ngày"
                        required />
                    <span v-if="errors.work_hours" class="error">{{ errors.work_hours }}</span>
                </div>

                <!-- Cấu trúc lương -->
                <div class="form-group">
                    <label for="salary_structure">Cấu trúc lương <span class="required">*</span></label>
                    <input v-model="form.salary_structure" id="salary_structure" type="text"
                        placeholder="Ví dụ: Lương cơ bản + Thưởng" required />
                    <span v-if="errors.salary_structure" class="error">{{ errors.salary_structure }}</span>
                </div>

                <!-- Người phụ trách -->
                <div class="form-group">
                    <label for="supervisor">Người phụ trách <span class="required">*</span></label>
                    <input v-model="form.supervisor" id="supervisor" type="text" placeholder="Nhập tên người phụ trách"
                        required />
                    <span v-if="errors.supervisor" class="error">{{ errors.supervisor }}</span>
                </div>

                <!-- Loại tiền lương -->
                <div class="form-group">
                    <label for="salary_type">Loại tiền lương <span class="required">*</span></label>
                    <select v-model="form.salary_type" id="salary_type" required>
                        <option value="" disabled>Chọn loại lương</option>
                        <option value="Lương tháng">Lương tháng</option>
                        <option value="Lương ngày">Lương ngày</option>
                        <option value="Lương giờ">Lương giờ</option>
                    </select>
                    <span v-if="errors.salary_type" class="error">{{ errors.salary_type }}</span>
                </div>

                <!-- Tiền lương -->
                <div class="form-group">
                    <label for="salary_amount">Tiền lương <span class="required">*</span></label>
                    <input v-model.number="form.salary_amount" id="salary_amount" type="number"
                        placeholder="Nhập số tiền" required min="0" />
                    <span v-if="errors.salary_amount" class="error">{{ errors.salary_amount }}</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cập nhật nhân viên</button>
                <router-link to="/" class="btn btn-secondary">Quay lại</router-link>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            form: {
                name: '',
                email: '',
                address: '',
                phone: '',
                gender: '',
                position: '',
                department: '',
                workplace: '',
                start_date: '',
                end_date: '',
                probation_end: '',
                work_hours: '',
                salary_structure: '',
                supervisor: '',
                salary_type: '',
                salary_amount: null,
            },
            errors: {},
        };
    },
    mounted() {
        this.fetchEmployee();
    },
    methods: {
        async fetchEmployee() {
            try {
                const response = await axios.get(`/api/employees/${this.$route.params.id}`);
                this.form = response.data;
            } catch (error) {
                console.error('Error fetching employee:', error);
                alert('Không thể tải thông tin nhân viên!');
            }
        },
        validateForm() {
            this.errors = {};

            if (!this.form.name) this.errors.name = 'Vui lòng nhập tên';
            if (!this.form.email || !this.isValidEmail(this.form.email)) this.errors.email = 'Vui lòng nhập email hợp lệ';
            if (!this.form.address) this.errors.address = 'Vui lòng nhập địa chỉ';
            if (!this.form.phone || !this.isValidPhone(this.form.phone)) this.errors.phone = 'Số điện thoại phải là 10 chữ số';
            if (!this.form.gender) this.errors.gender = 'Vui lòng chọn giới tính';
            if (!this.form.position) this.errors.position = 'Vui lòng nhập vị trí';
            if (!this.form.department) this.errors.department = 'Vui lòng nhập phòng ban';
            if (!this.form.workplace) this.errors.workplace = 'Vui lòng nhập địa điểm làm việc';
            if (!this.form.start_date) this.errors.start_date = 'Vui lòng chọn ngày bắt đầu';
            if (!this.form.work_hours) this.errors.work_hours = 'Vui lòng nhập thời gian làm việc';
            if (!this.form.salary_structure) this.errors.salary_structure = 'Vui lòng nhập cấu trúc lương';
            if (!this.form.supervisor) this.errors.supervisor = 'Vui lòng nhập người phụ trách';
            if (!this.form.salary_type) this.errors.salary_type = 'Vui lòng chọn loại lương';
            if (!this.form.salary_amount || this.form.salary_amount <= 0) this.errors.salary_amount = 'Tiền lương phải lớn hơn 0';

            return Object.keys(this.errors).length === 0;
        },
        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },
        isValidPhone(phone) {
            const phoneRegex = /^\d{10}$/;
            return phoneRegex.test(phone);
        },
        async updateEmployee() {
            if (this.validateForm()) {
                try {
                    await axios.put(`/api/employees/${this.$route.params.id}`, this.form);
                    this.$router.push('/');
                } catch (error) {
                    console.error('Error updating employee:', error);
                    alert('Có lỗi xảy ra khi cập nhật nhân viên!');
                }
            } else {
                alert('Vui lòng điền đầy đủ và chính xác thông tin!');
            }
        },
    },
};
</script>

<style scoped>
.container {
    max-width: 900px;
    /* margin: 0 auto; */
    padding: 20px;
}

h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

.employee-form {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #555;
}

.required {
    color: red;
}

input,
select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

input:focus,
select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

.error {
    color: red;
    font-size: 12px;
    margin-top: 5px;
}

.form-actions {
    margin-top: 20px;
    text-align: center;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    margin: 0 10px;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    text-decoration: none;
}

.btn-secondary:hover {
    background-color: #5a6268;
}
</style>