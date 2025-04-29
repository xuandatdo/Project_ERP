<template>
    <div class="container">
        <div v-if="message" :class="['alert', message.type === 'success' ? 'alert-success' : 'alert-danger']">
            {{ message.text }}
        </div>
        <form @submit.prevent="createTransportPlan" class="transport-form">
            <div class="form-grid">
                <!-- Hàng 1: Biển số và Trọng lượng -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="license_plate">Biển số xe <span class="required">*</span></label>
                        <input v-model="form.license_plate" id="license_plate" type="text" placeholder="Nhập biển số xe"
                            required />
                        <span v-if="errors.license_plate" class="error">{{ errors.license_plate }}</span>
                    </div>
                    <div class="form-group">
                        <label for="weight">Trọng lượng (kg) <span class="required">*</span></label>
                        <input v-model.number="form.weight" id="weight" type="number" placeholder="Nhập trọng lượng"
                            required min="0" />
                        <span v-if="errors.weight" class="error">{{ errors.weight }}</span>
                    </div>
                </div>

                <!-- Hàng 2: Thời gian dự kiến và Trạng thái -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="expected_time">Thời gian dự kiến <span class="required">*</span></label>
                        <input v-model="form.expected_time" id="expected_time" type="datetime-local" required />
                        <span v-if="errors.expected_time" class="error">{{ errors.expected_time }}</span>
                    </div>
                    <div class="form-group">
                        <label for="status">Trạng thái <span class="required">*</span></label>
                        <select v-model="form.status" id="status" required>
                            <option value="" disabled>Chọn trạng thái</option>
                            <option value="preparing">Đang chuẩn bị</option>
                            <option value="in_transit">Đang vận chuyển</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="delayed">Chậm trễ</option>
                        </select>
                        <span v-if="errors.status" class="error">{{ errors.status }}</span>
                    </div>
                </div>

                <!-- Hàng 3: Địa điểm giao và nhận -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="delivery_location">Địa điểm giao hàng <span class="required">*</span></label>
                        <input v-model="form.delivery_location" id="delivery_location" type="text"
                            placeholder="Nhập địa điểm giao hàng" required />
                        <span v-if="errors.delivery_location" class="error">{{ errors.delivery_location }}</span>
                    </div>
                    <div class="form-group">
                        <label for="pickup_location">Địa điểm nhận hàng <span class="required">*</span></label>
                        <input v-model="form.pickup_location" id="pickup_location" type="text"
                            placeholder="Nhập địa điểm nhận hàng" required />
                        <span v-if="errors.pickup_location" class="error">{{ errors.pickup_location }}</span>
                    </div>
                </div>

                <!-- Hàng 4: Thông tin tài xế -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="driver_name">Tên tài xế <span class="required">*</span></label>
                        <input v-model="form.driver_name" id="driver_name" type="text" placeholder="Nhập tên tài xế"
                            required />
                        <span v-if="errors.driver_name" class="error">{{ errors.driver_name }}</span>
                    </div>
                    <div class="form-group">
                        <label for="driver_phone">Số điện thoại tài xế <span class="required">*</span></label>
                        <input v-model="form.driver_phone" id="driver_phone" type="tel" placeholder="Nhập số điện thoại"
                            required pattern="[0-9]{10}" />
                        <span v-if="errors.driver_phone" class="error">{{ errors.driver_phone }}</span>
                    </div>
                </div>

                <!-- Hàng 5: Số lượng và thông tin hàng hóa -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="quantity">Số lượng <span class="required">*</span></label>
                        <input v-model.number="form.quantity" id="quantity" type="number" placeholder="Nhập số lượng"
                            required min="1" />
                        <span v-if="errors.quantity" class="error">{{ errors.quantity }}</span>
                    </div>
                    <div class="form-group">
                        <label for="cargo_details">Thông tin hàng hóa chi tiết</label>
                        <textarea v-model="form.cargo_details" id="cargo_details"
                            placeholder="Nhập thông tin chi tiết hàng hóa"></textarea>
                        <span v-if="errors.cargo_details" class="error">{{ errors.cargo_details }}</span>
                    </div>
                </div>

                <!-- Hàng 6: Ghi chú -->
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="notes">Ghi chú</label>
                        <textarea v-model="form.notes" id="notes" placeholder="Nhập ghi chú"></textarea>
                        <span v-if="errors.notes" class="error">{{ errors.notes }}</span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Tạo kế hoạch vận chuyển</button>
                <router-link to="/transport" class="btn btn-secondary">Quay lại</router-link>
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
                license_plate: '',
                weight: null,
                expected_time: '',
                status: '',
                delivery_location: '',
                pickup_location: '',
                driver_name: '',
                driver_phone: '',
                quantity: null,
                cargo_details: '',
                notes: ''
            },
            errors: {},
            message: null
        };
    },
    methods: {
        validateForm() {
            this.errors = {};

            if (!this.form.license_plate) this.errors.license_plate = 'Vui lòng nhập biển số xe';
            if (!this.form.weight) this.errors.weight = 'Vui lòng nhập trọng lượng';
            if (!this.form.expected_time) this.errors.expected_time = 'Vui lòng chọn thời gian dự kiến';
            if (!this.form.status) this.errors.status = 'Vui lòng chọn trạng thái';
            if (!this.form.delivery_location) this.errors.delivery_location = 'Vui lòng nhập địa điểm giao hàng';
            if (!this.form.pickup_location) this.errors.pickup_location = 'Vui lòng nhập địa điểm nhận hàng';
            if (!this.form.driver_name) this.errors.driver_name = 'Vui lòng nhập tên tài xế';
            if (!this.form.driver_phone) this.errors.driver_phone = 'Vui lòng nhập số điện thoại tài xế';
            if (!this.form.quantity) this.errors.quantity = 'Vui lòng nhập số lượng';

            return Object.keys(this.errors).length === 0;
        },

        showMessage(text, type = 'success') {
            this.message = { text, type };
            setTimeout(() => {
                this.message = null;
            }, 3000);
        },

        async createTransportPlan() {
            if (!this.validateForm()) return;

            try {
                const response = await axios.post('/api/transport-plans', this.form);
                this.showMessage('Tạo kế hoạch vận chuyển thành công!');
                setTimeout(() => {
                    this.$router.push('/transport');
                }, 1500);
            } catch (error) {
                console.error('Lỗi khi tạo kế hoạch vận chuyển:', error);
                this.showMessage('Có lỗi xảy ra khi tạo kế hoạch vận chuyển', 'error');
                if (error.response?.data?.errors) {
                    this.errors = error.response.data.errors;
                }
            }
        }
    }
};
</script>

<style scoped>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.form-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

label {
    font-weight: bold;
}

.required {
    color: red;
}

input,
select,
textarea {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

textarea {
    min-height: 100px;
    resize: vertical;
}

.error {
    color: red;
    font-size: 12px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
}

.btn-primary {
    background-color: #4CAF50;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
</style>