<template>
    <div class="container">
        <!-- <div v-if="message" :class="['alert', message.type === 'success' ? 'alert-success' : 'alert-danger']">
            {{ message.text }}
        </div> -->
        <div class="form-wrapper">
            <form @submit.prevent="updateTransportPlan" class="transport-form">
                <div class="form-grid">
                    <!-- Hàng 1: Biển số xe, Trọng lượng, Thời gian dự kiến -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="license_plate">Biển số xe <span class="required">*</span></label>
                            <input v-model="form.license_plate" id="license_plate" type="text"
                                placeholder="Nhập biển số xe" required />
                            <span v-if="errors.license_plate" class="error">{{ errors.license_plate }}</span>
                        </div>
                        <div class="form-group">
                            <label for="weight">Trọng lượng (kg) <span class="required">*</span></label>
                            <input v-model.number="form.weight" id="weight" type="number" placeholder="Nhập trọng lượng"
                                required min="0" />
                            <span v-if="errors.weight" class="error">{{ errors.weight }}</span>
                        </div>
                        <div class="form-group">
                            <label for="expected_time">Thời gian dự kiến <span class="required">*</span></label>
                            <input v-model="form.expected_time" id="expected_time" type="datetime-local" required />
                            <span v-if="errors.expected_time" class="error">{{ errors.expected_time }}</span>
                        </div>
                    </div>

                    <!-- Hàng 2: Trạng thái, Địa điểm giao hàng, Địa điểm nhận hàng -->
                    <div class="form-row">
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

                    <!-- Hàng 3: Tên tài xế, Số điện thoại tài xế, Số lượng -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="driver_name">Tên tài xế <span class="required">*</span></label>
                            <input v-model="form.driver_name" id="driver_name" type="text" placeholder="Nhập tên tài xế"
                                required />
                            <span v-if="errors.driver_name" class="error">{{ errors.driver_name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="driver_phone">Số điện thoại tài xế <span class="required">*</span></label>
                            <input v-model="form.driver_phone" id="driver_phone" type="tel"
                                placeholder="Nhập số điện thoại" required pattern="[0-9]{10}" />
                            <span v-if="errors.driver_phone" class="error">{{ errors.driver_phone }}</span>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Số lượng <span class="required">*</span></label>
                            <input v-model.number="form.quantity" id="quantity" type="number"
                                placeholder="Nhập số lượng" required min="1" />
                            <span v-if="errors.quantity" class="error">{{ errors.quantity }}</span>
                        </div>
                    </div>

                    <!-- Hàng 4: Thông tin hàng hóa chi tiết, Ghi chú -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cargo_details">Thông tin hàng hóa chi tiết</label>
                            <textarea v-model="form.cargo_details" id="cargo_details"
                                placeholder="Nhập thông tin chi tiết hàng hóa"></textarea>
                            <span v-if="errors.cargo_details" class="error">{{ errors.cargo_details }}</span>
                        </div>
                        <div class="form-group">
                            <label for="notes">Ghi chú</label>
                            <textarea v-model="form.notes" id="notes" placeholder="Nhập ghi chú"></textarea>
                            <span v-if="errors.notes" class="error">{{ errors.notes }}</span>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Cập nhật kế hoạch vận chuyển</button>
                    <router-link to="/transport" class="btn btn-secondary">Quay lại</router-link>
                </div>
            </form>
        </div>
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
    created() {
        this.fetchTransportPlan();
    },
    methods: {
        validateForm() {
            this.errors = {};

            if (!this.form.license_plate) this.errors.license_plate = 'Vui lòng nhập biển số xe';
            if (!this.form.weight) this.errors.weight = 'Vui lòng nhập trọng lượng';
            if (!this.form.expected_time) this.errors.expected_time = 'Vui lòng chọn thời gian dự kiến';
            else if (new Date(this.form.expected_time) < new Date()) {
                this.errors.expected_time = 'Thời gian dự kiến không thể là quá khứ';
            }
            if (!this.form.status) this.errors.status = 'Vui lòng chọn trạng thái';
            if (!this.form.delivery_location) this.errors.delivery_location = 'Vui lòng nhập địa điểm giao hàng';
            if (!this.form.pickup_location) this.errors.pickup_location = 'Vui lòng nhập địa điểm nhận hàng';
            if (!this.form.driver_name) this.errors.driver_name = 'Vui lòng nhập tên tài xế';
            if (!this.form.driver_phone) this.errors.driver_phone = 'Vui lòng nhập số điện thoại tài xế';
            else if (!/^[0-9]{10}$/.test(this.form.driver_phone)) {
                this.errors.driver_phone = 'Số điện thoại phải là 10 chữ số';
            }
            if (!this.form.quantity) this.errors.quantity = 'Vui lòng nhập số lượng';

            return Object.keys(this.errors).length === 0;
        },

        showMessage(text, type = 'success') {
            this.message = { text, type };
            setTimeout(() => {
                this.message = null;
            }, 3000);
        },

        async fetchTransportPlan() {
            try {
                const response = await axios.get(`/api/transport-plans/${this.$route.params.id}`);
                // Format expected_time for datetime-local input
                const data = response.data;
                if (data.expected_time) {
                    data.expected_time = new Date(data.expected_time).toISOString().slice(0, 16);
                }
                this.form = data;
            } catch (error) {
                console.error('Lỗi khi lấy thông tin kế hoạch vận chuyển:', error);
                this.showMessage('Có lỗi xảy ra khi lấy thông tin kế hoạch vận chuyển', 'error');
            }
        },

        async updateTransportPlan() {
            if (!this.validateForm()) return;

            try {
                // Format expected_time to ISO string for API consistency
                const formattedForm = {
                    ...this.form,
                    expected_time: new Date(this.form.expected_time).toISOString()
                };
                const response = await axios.put(`/api/transport-plans/${this.$route.params.id}`, formattedForm);
                this.showMessage('Cập nhật kế hoạch vận chuyển thành công!');
                setTimeout(() => {
                    this.$router.push('/transport');
                }, 1500);
            } catch (error) {
                console.error('Lỗi khi cập nhật kế hoạch vận chuyển:', error);
                this.showMessage('Có lỗi xảy ra khi cập nhật kế hoạch vận chuyển', 'error');
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
    max-width: 100vw;
    margin: 0 20px;
    padding: 20px;
}

.form-wrapper {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 20px;
    border: 1px solid #ddd;
}

.form-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

label {
    font-weight: bold;
    font-size: 14px;
    color: #333;
}

.required {
    color: red;
}

input,
select,
textarea {
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background-color: #fff;
    transition: all 0.3s ease;
}

input:focus,
select:focus,
textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
}

input[type="datetime-local"] {
    font-family: inherit;
    width: 100%;
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
    justify-content: flex-end;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s ease;
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
}

.btn-secondary:hover {
    background-color: #5a6268;
}

/* .alert {
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
} */
</style>
