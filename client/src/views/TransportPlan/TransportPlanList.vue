<template>
    <div class="container">
        <div class="search-wrapper">
            <div class="search-container">
                <input v-model="filters.search" type="text" placeholder="Tìm kiếm theo mã lô hàng, tài xế, biển số..."
                    class="search-input" @input="handleSearch" />
            </div>
            <div class="filter-container">
                <select v-model="filters.status" @change="handleSearch" class="filter-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="preparing">Đang chuẩn bị</option>
                    <option value="in_transit">Đang vận chuyển</option>
                    <option value="completed">Hoàn thành</option>
                    <option value="delayed">Chậm trễ</option>
                </select>
                <input type="date" v-model="filters.date" @change="handleSearch" class="filter-date" />
            </div>
            <router-link to="/transport/create" class="btn btn-add">Thêm kế hoạch vận chuyển</router-link>
        </div>

        <div class="table-wrapper">
            <table class="transport-table" v-if="paginatedPlans.length > 0">
                <thead>
                    <tr>
                        <th>Mã lô hàng</th>
                        <th>Biển số</th>
                        <th>Tài xế</th>
                        <th>Số điện thoại</th>
                        <th>Thời gian dự kiến</th>
                        <th>Trạng thái</th>
                        <th>Địa điểm giao</th>
                        <th>Địa điểm nhận</th>
                        <th>Số lượng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="plan in paginatedPlans" :key="plan.id">
                        <td>{{ "LH" + plan.id }}</td>
                        <td>{{ truncate(plan.license_plate, 15) }}</td>
                        <td>{{ truncate(plan.driver_name, 20) }}</td>
                        <td>{{ plan.driver_phone }}</td>
                        <td>{{ formatDateTime(plan.expected_time) }}</td>
                        <td>
                            <span :class="['status-badge', getStatusClass(plan.status)]">
                                {{ getStatusText(plan.status) }}
                            </span>
                        </td>
                        <td>{{ truncate(plan.delivery_location, 15) }}</td>
                        <td>{{ truncate(plan.pickup_location, 15) }}</td>
                        <td>{{ plan.quantity }}</td>
                        <td>
                            <router-link :to="'/transport/' + plan.id + '/edit'" class="btn btn-edit">Sửa</router-link>
                            <button @click="confirmDelete(plan)" class="btn btn-delete">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="no-data">Không có kế hoạch vận chuyển nào</div>
        </div>

        <div class="pagination" v-if="totalPages > 1">
            <button @click="prevPage" :disabled="currentPage === 1" class="btn btn-page">Trang trước</button>
            <span>Trang {{ currentPage }} / {{ totalPages }}</span>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="btn btn-page">Trang sau</button>
        </div>

        <!-- Modal xác nhận xóa -->
        <div class="modal" v-if="showDeleteModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Xác nhận xóa</h2>
                    <span class="close" @click="showDeleteModal = false">×</span>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa kế hoạch vận chuyển này?</p>
                </div>
                <div class="modal-footer">
                    <button @click="showDeleteModal = false" class="btn btn-secondary">Hủy</button>
                    <button @click="deletePlan" class="btn btn-danger">Xóa</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { useToast } from 'vue-toastification';

export default {
    setup() {
        const toast = useToast();
        return { toast };
    },
    data() {
        return {
            transportPlans: [],
            filters: {
                search: '',
                status: '',
                date: '',
            },
            currentPage: 1,
            itemsPerPage: 5,
            showDeleteModal: false,
            planToDelete: null,
        };
    },
    mounted() {
        this.loadTransportPlans();
    },
    computed: {
        filteredPlans() {
            if (!this.filters.search && !this.filters.status && !this.filters.date) {
                return this.transportPlans;
            }

            return this.transportPlans.filter((plan) => {
                const query = this.filters.search.toLowerCase();
                const planId = `lh${plan.id}`.toLowerCase();
                const licensePlate = plan.license_plate.toLowerCase();
                const driverName = plan.driver_name.toLowerCase();
                const driverPhone = plan.driver_phone.toLowerCase();
                const deliveryLocation = plan.delivery_location.toLowerCase();
                const pickupLocation = plan.pickup_location.toLowerCase();

                const matchesSearch =
                    !this.filters.search ||
                    planId.includes(query) ||
                    licensePlate.includes(query) ||
                    driverName.includes(query) ||
                    driverPhone.includes(query) ||
                    deliveryLocation.includes(query) ||
                    pickupLocation.includes(query);

                const matchesStatus = !this.filters.status || plan.status === this.filters.status;

                const matchesDate =
                    !this.filters.date ||
                    new Date(plan.expected_time).toISOString().split('T')[0] === this.filters.date;

                return matchesSearch && matchesStatus && matchesDate;
            });
        },
        totalPages() {
            return Math.ceil(this.filteredPlans.length / this.itemsPerPage);
        },
        paginatedPlans() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredPlans.slice(start, end);
        },
    },
    watch: {
        'filters.search'() {
            this.currentPage = 1;
        },
        'filters.status'() {
            this.currentPage = 1;
        },
        'filters.date'() {
            this.currentPage = 1;
        },
    },
    methods: {
        async loadTransportPlans() {
            try {
                const response = await axios.get('/api/transport-plans');
                this.transportPlans = response.data.data;
            } catch (error) {
                console.error('Lỗi khi tải danh sách kế hoạch vận chuyển:', error);
                this.toast.error('Có lỗi xảy ra khi tải danh sách');
            }
        },
        handleSearch: debounce(function () {
            this.currentPage = 1;
            this.loadTransportPlans();
        }, 300),
        confirmDelete(plan) {
            this.planToDelete = plan;
            this.showDeleteModal = true;
        },
        async deletePlan() {
            if (!this.planToDelete) return;

            try {
                await axios.delete(`/api/transport-plans/${this.planToDelete.id}`);
                this.toast.success('Xóa kế hoạch vận chuyển thành công!');
                this.loadTransportPlans();
                this.showDeleteModal = false;
                this.planToDelete = null;
            } catch (error) {
                console.error('Lỗi khi xóa kế hoạch vận chuyển:', error);
                this.toast.error('Có lỗi xảy ra khi xóa kế hoạch vận chuyển');
                this.showDeleteModal = false;
            }
        },
        truncate(text, maxLength) {
            if (!text || text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        },
        formatDateTime(datetime) {
            if (!datetime) return '';
            const date = new Date(datetime);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day} ${hours}:${minutes}`;
        },
        getStatusText(status) {
            const statusMap = {
                preparing: 'Đang chuẩn bị',
                in_transit: 'Đang vận chuyển',
                completed: 'Hoàn thành',
                delayed: 'Chậm trễ',
            };
            return statusMap[status] || status;
        },
        getStatusClass(status) {
            const classMap = {
                preparing: 'status-preparing',
                in_transit: 'status-in-transit',
                completed: 'status-completed',
                delayed: 'status-delayed',
            };
            return classMap[status] || '';
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

function debounce(fn, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn.apply(this, args), delay);
    };
}
</script>

<style scoped>
.container {
    max-width: 83vw;
    padding: 20px;
}

.search-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
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

.filter-container {
    display: flex;
    gap: 10px;
    align-items: center;
}

.filter-select,
.filter-date {
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.filter-select:focus,
.filter-date:focus {
    outline: none;
    border-color: #007bff;
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
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border-radius: 4px;
    transition: all 0.3s ease;
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

.table-wrapper {
    position: relative;
    margin-top: 20px;
    overflow-x: auto;
    white-space: nowrap;
}

.transport-table {
    width: 100%;
    min-width: 1300px;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border: 1px solid #ddd;
}

.transport-table th,
.transport-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    border-right: 1px solid #ddd;
}

.transport-table th:last-child,
.transport-table td:last-child {
    border-right: none;
}

.transport-table th {
    background-color: #f8f9fa;
    color: black;
    font-weight: bold;
    border-bottom: 2px solid #dee2e6;
}

.transport-table tr:hover {
    background-color: #f5f5f5;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.status-preparing {
    background-color: #ffc107;
    color: #000;
}

.status-in-transit {
    background-color: #17a2b8;
    color: white;
}

.status-completed {
    background-color: #28a745;
    color: white;
}

.status-delayed {
    background-color: #dc3545;
    color: white;
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

.no-data {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-size: 16px;
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

.btn-danger {
    background-color: #dc3545;
    color: white;
    border: none;
}

.btn-danger:hover {
    background-color: #c82333;
}
</style>