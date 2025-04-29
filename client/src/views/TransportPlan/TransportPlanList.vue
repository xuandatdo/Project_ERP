<template>
    <div class="container">
        <div class="header">
            <h1>Danh sách kế hoạch vận chuyển</h1>
            <router-link to="/transport/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm kế hoạch vận chuyển
            </router-link>
        </div>

        <!-- Bộ lọc -->
        <div class="filters">
            <div class="filter-group">
                <input v-model="filters.search" type="text" placeholder="Tìm kiếm..." @input="handleSearch" />
            </div>
            <div class="filter-group">
                <select v-model="filters.status" @change="handleSearch">
                    <option value="">Tất cả trạng thái</option>
                    <option value="preparing">Đang chuẩn bị</option>
                    <option value="in_transit">Đang vận chuyển</option>
                    <option value="completed">Hoàn thành</option>
                    <option value="delayed">Chậm trễ</option>
                </select>
            </div>
            <div class="filter-group">
                <input type="date" v-model="filters.date" @change="handleSearch" />
            </div>
        </div>

        <!-- Bảng danh sách -->
        <div class="table-responsive">
            <table v-if="transportPlans.length > 0">
                <thead>
                    <tr>
                        <th>Mã lô hàng</th>
                        <th>Biển số</th>
                        <th>Tài xế</th>
                        <th>Thời gian dự kiến</th>
                        <th>Trạng thái</th>
                        <th>Địa điểm giao</th>
                        <th>Địa điểm nhận</th>
                        <th>Số lượng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="plan in transportPlans" :key="plan.id">
                        <td>{{ plan.id }}</td>
                        <td>{{ plan.license_plate }}</td>
                        <td>
                            {{ plan.driver_name }}
                            <div class="small text-muted">{{ plan.driver_phone }}</div>
                        </td>
                        <td>{{ formatDateTime(plan.expected_time) }}</td>
                        <td>
                            <span :class="['status-badge', getStatusClass(plan.status)]">
                                {{ getStatusText(plan.status) }}
                            </span>
                        </td>
                        <td>{{ plan.delivery_location }}</td>
                        <td>{{ plan.pickup_location }}</td>
                        <td>{{ plan.quantity }}</td>
                        <td>
                            <div class="actions">
                                <router-link :to="'/transport/' + plan.id" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </router-link>
                                <router-link :to="'/transport/' + plan.id + '/edit'" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </router-link>
                                <button @click="deletePlan(plan.id)" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="no-data">
                Không có kế hoạch vận chuyển nào
            </div>
        </div>

        <!-- Phân trang -->
        <div class="pagination" v-if="totalPages > 1">
            <button :disabled="currentPage === 1" @click="changePage(currentPage - 1)" class="btn btn-secondary">
                Trước
            </button>
            <span>Trang {{ currentPage }} / {{ totalPages }}</span>
            <button :disabled="currentPage === totalPages" @click="changePage(currentPage + 1)"
                class="btn btn-secondary">
                Sau
            </button>
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
                date: ''
            },
            currentPage: 1,
            totalPages: 1,
            perPage: 10
        };
    },
    created() {
        this.loadTransportPlans();
    },
    methods: {
        async loadTransportPlans() {
            try {
                const response = await axios.get('/api/transport-plans', {
                    params: {
                        page: this.currentPage,
                        per_page: this.perPage,
                        search: this.filters.search,
                        status: this.filters.status,
                        date: this.filters.date
                    }
                });

                this.transportPlans = response.data.data;
                this.totalPages = Math.ceil(response.data.total / this.perPage);
            } catch (error) {
                console.error('Lỗi khi tải danh sách kế hoạch vận chuyển:', error);
                // Xóa thông báo lỗi
                // this.toast.error('Có lỗi xảy ra khi tải danh sách');
            }
        },

        handleSearch: debounce(function () {
            this.currentPage = 1;
            this.loadTransportPlans();
        }, 300),

        async deletePlan(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa kế hoạch vận chuyển này?')) return;

            try {
                await axios.delete(`/api/transport-plans/${id}`);
                // Xóa thông báo thành công
                // this.toast.success('Xóa kế hoạch vận chuyển thành công!');
                this.loadTransportPlans();
            } catch (error) {
                console.error('Lỗi khi xóa kế hoạch vận chuyển:', error);
                // Xóa thông báo lỗi
                // this.toast.error('Có lỗi xảy ra khi xóa kế hoạch vận chuyển');
            }
        },

        changePage(page) {
            this.currentPage = page;
            this.loadTransportPlans();
        },

        formatDateTime(datetime) {
            return new Date(datetime).toLocaleString('vi-VN');
        },

        getStatusText(status) {
            const statusMap = {
                'preparing': 'Đang chuẩn bị',
                'in_transit': 'Đang vận chuyển',
                'completed': 'Hoàn thành',
                'delayed': 'Chậm trễ'
            };
            return statusMap[status] || status;
        },

        getStatusClass(status) {
            const classMap = {
                'preparing': 'status-preparing',
                'in_transit': 'status-in-transit',
                'completed': 'status-completed',
                'delayed': 'status-delayed'
            };
            return classMap[status] || '';
        }
    }
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
    max-width: 100vw;
    padding: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.filters {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.filter-group {
    flex: 1;
}

.filter-group input,
.filter-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th,
td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.actions {
    display: flex;
    gap: 5px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-weight: bold;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}

.btn-primary {
    background-color: #4CAF50;
    color: white;
}

.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-warning {
    background-color: #ffc107;
    color: #000;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn:hover {
    opacity: 0.9;
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
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin-top: 20px;
}

.no-data {
    text-align: center;
    padding: 20px;
    color: #6c757d;
}

.small {
    font-size: 12px;
}

.text-muted {
    color: #6c757d;
}
</style>