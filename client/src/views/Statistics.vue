<template>
    <div class="container">
        <div class="search-wrapper">
            <div class="search-container">
                <input v-model="searchQuery" type="text" placeholder="Tìm kiếm theo ID, tên"
                    class="search-input">
            </div>
        </div>

        <div class="table-wrapper">
            <table class="employee-table">
                <thead>
                    <tr>
                        <th>Mã số nhân viên</th>
                        <th>Họ và Tên</th>
                        <th>Ngày vắng</th>
                        <th>Vắng có phép</th>
                        <th>Vắng không phép</th>
                        <th>Tổng số ngày vắng</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="employee in paginatedEmployees" :key="employee.id">
                        <td>{{ "NV" + employee.id }}</td>
                        <td>{{ employee.name }}</td>
                        <td>
                            <vue-cal
                                :events="formatCalendarData(employee.calendar_data)"
                                :disable-views="['years', 'months']"
                                default-view="month"
                                :time="false"
                                :transitions="false"
                                style="height: 250px;"
                                class="compact-calendar"
                            />
                        </td>
                        <td>
                            <span class="absence-count absence-with-permission">{{ employee.total_absent_with_permission }}</span>
                        </td>
                        <td>
                            <span class="absence-count absence-without-permission">{{ employee.total_absent_without_permission }}</span>
                        </td>
                        <td>
                            <span class="absence-count total-absence">{{ employee.total_absent_days }}</span>
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
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';

export default {
    components: {
        VueCal,
    },
    data() {
        return {
            employees: [],
            currentPage: 1,
            itemsPerPage: 5, // Giới hạn 5 thông tin mỗi trang
            searchQuery: '',
            statistics: [],
        };
    },
    mounted() {
        this.fetchEmployees();
        this.fetchStatistics();
    },
    computed: {
        filteredEmployees() {
            if (!this.searchQuery) {
                return this.statistics; // Filter statistics instead of employees
            }

            const query = this.searchQuery.toLowerCase();
            return this.statistics.filter(employee => {
                const employeeId = `nv${employee.id}`.toLowerCase();
                const employeeName = employee.name.toLowerCase();
                const employeeDepartment = employee.department?.toLowerCase() || '';
                const employeePosition = employee.position?.toLowerCase() || '';
                const employeeEducationLevel = employee.education_level?.toLowerCase() || '';

                return (
                    employeeId.includes(query) ||
                    employeeName.includes(query) ||
                    employeeDepartment.includes(query) ||
                    employeePosition.includes(query) ||
                    employeeEducationLevel.includes(query)
                );
            });
        },
        totalPages() {
            return Math.ceil(this.filteredEmployees.length / this.itemsPerPage);
        },
        paginatedEmployees() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredEmployees.slice(start, end);
        },
    },
    watch: {
        searchQuery() {
            this.currentPage = 1; // Reset về trang 1 khi thay đổi tìm kiếm
        }
    },
    methods: {
        async fetchEmployees() {
            try {
                const response = await axios.get('/api/employees');
                this.employees = response.data;
            } catch (error) {
                console.error('Error fetching employees:', error);
            }
        },
        async fetchStatistics() {
            try {
                const response = await axios.get('/api/statistics');
                this.statistics = response.data;
            } catch (error) {
                console.error('Error fetching statistics:', error);
            }
        },
        async deleteEmployee(id) {
            if (confirm('Bạn có chắc muốn xóa nhân viên này?')) {
                try {
                    await axios.delete(`/api/employees/${id}`);
                    this.fetchEmployees();
                } catch (error) {
                    console.error('Error deleting employee:', error);
                }
            }
        },
        truncate(text, maxLength) {
            if (!text || text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        },
        formatDate(date) {
            if (!date) return '';
            const [year, month, day] = date.split('-');
            return `${day}/${month}/${year}`;
        },
        formatCalendarData(calendarData) {
            return Object.entries(calendarData).map(([date, count]) => ({
                start: date,
                end: date,
                title: count > 0 ? `${count}` : '', // Very compact display
                class: count > 0 ? 'absent-day' : '',
                count: count
            }));
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
</script>

<style scoped>
/* Base Styles */
.container {
    max-width: 100%;
    margin: 0 auto;
    padding: 1rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

/* Search Section */
.search-wrapper {
    margin-bottom: 2.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
}

.search-container {
    position: relative;
    flex-grow: 1;
    max-width: 600px;
}

.search-input {
    width: 100%;
    padding: 0.8rem 1.5rem;
    border: 2px solid #e0e3e7;
    border-radius: 50px;
    font-size: 1rem;
    background-color: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.search-input:focus {
    outline: none;
    border-color: #5c7cfa;
    box-shadow: 0 0 0 3px rgba(92, 124, 250, 0.2);
}

.search-input::placeholder {
    color: #a0aec0;
}

/* Table Styles */
.table-wrapper {
    margin: 2rem 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    background: white;
    width: 100%;
}

.employee-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1500px;
}

.employee-table th,
.employee-table td {
    padding: 1.2rem 1.5rem;
    text-align: left;
    border-bottom: 1px solid #edf2f7;
}

.employee-table th {
    background-color: #f8fafc;
    color: #4a5568;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
}

.employee-table tr:not(:last-child) {
    border-bottom: 1px solid #edf2f7;
}

.employee-table tr:hover {
    background-color: #f8fafc;
}

/* Calendar Styles */
.vue-cal {
    border: none !important;
    box-shadow: none !important;
    border-radius: 8px;
    overflow: hidden;
}

.vue-cal__header {
    background-color: #f8fafc !important;
    border-bottom: 1px solid #edf2f7 !important;
}

.vue-cal__weekdays-headings {
    background-color: #f8fafc !important;
}

.vue-cal__event {
    background-color: #fff5f5 !important;
    border-left: 3px solid #fc8181 !important;
    color: #c53030 !important;
    font-size: 0.8rem !important;
    padding: 2px 4px !important;
}

/* Pagination Styles */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 2.5rem;
    gap: 1rem;
}

.btn-page {
    padding: 0.6rem 1.2rem;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.btn-page:not(:disabled) {
    background-color: #5c7cfa;
    color: white;
}

.btn-page:not(:disabled):hover {
    background-color: #3d5af1;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.btn-page:disabled {
    background-color: #e2e8f0;
    color: #a0aec0;
    cursor: not-allowed;
}

.pagination span {
    font-size: 0.95rem;
    color: #4a5568;
    min-width: 100px;
    text-align: center;
}

/* Status Indicators */
.absent-day {
    background-color: #fff5f5;
    color: #c53030;
    border-left: 3px solid #fc8181;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .container {
        padding: 1.5rem;
    }
    
    .search-wrapper {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-container {
        max-width: 100%;
    }
}

/* Animation for smoother transitions */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.employee-table tr {
    animation: fadeIn 0.3s ease forwards;
}

.employee-table tr:nth-child(odd) {
    animation-delay: 0.05s;
}

.employee-table th:nth-child(3),
.employee-table td:nth-child(3) {
    min-width: 500px; /* More space for calendar */
}

.employee-table th:nth-child(4), /* With permission */
.employee-table th:nth-child(5), /* Without permission */
.employee-table th:nth-child(6) { /* Total */
    width: 80px;
    min-width: 80px;
}

/* Compact number display */
.employee-table td:nth-child(4),
.employee-table td:nth-child(5),
.employee-table td:nth-child(6) {
    text-align: center;
    font-size: 0.9em;
    font-weight: 500;
}

/* Compact number display */
.absence-count {
    font-weight: 600;
    display: block;
    margin: 0 auto;
    padding: 4px 0;
}

/* Color coding for different absence types */
.absence-with-permission {
    color: #38a169; /* Green for permitted absence */
}

.absence-without-permission {
    color: #e53e3e; /* Red for unpermitted absence */
}

.total-absence {
    color: #2b6cb0; /* Blue for total */
    font-weight: 700;
}
.vuecal {
    height: 200px !important;
}
/* Hide time-related elements in calendar */
.vuecal__event-time {
    display: none !important;
}

.vuecal__time-cell {
    display: none !important;
}

.vuecal__time-column {
    display: none !important;
    width: 0 !important;
}

.vuecal__header {
    padding: 5px 0 !important;
    height: 30px !important;
}

.vue-cal {
    border: none !important;
    font-size: 12px !important; /* Smaller base font size */
}

/* Weekday headings */
.vuecal__weekdays-headings {
    padding: 0 !important;
}

.vuecal__weekday-heading {
    padding: 5px 0 !important;
    font-size: 11px !important;
}

/* Day cells */
.vuecal__cell {
    height: 32px !important; /* Reduced from default */
    min-height: 32px !important;
    padding: 5 !important;
}

/* Date number */
.vuecal__cell-date {
    padding: 1px 3px !important;
    font-size: 10px !important;
    line-height: 1.2 !important;
}

/* Events in cells */
.vuecal__event {
    padding: 0 !important;
    margin: 0 !important;
    height: 14px !important;
    min-height: 14px !important;
    line-height: 14px !important;
    font-size: 10px !important;
}

/* Remove cell content padding */
.vuecal__cell-content {
    padding: 0 !important;
    justify-content: flex-start !important;
}

/* Today highlight */
.vuecal__cell--today {
    background-color: transparent !important;
}

.vuecal__cell--today .vuecal__cell-date {
    font-weight: bold;
    color: #2c3e50;
}

/* Event title styling */
.vuecal__event-title {
    display: none; /* Hide text, we'll just use background color */
}

/* Color coding for absent days */
.absent-day {
    background-color: #ffebee !important; /* Light red background */
    border-left: 2px solid #f44336 !important;
}

/* Hover effect for events */
.vuecal__event:hover {
    z-index: 1;
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
}

/* Show full count on hover */
.vuecal__event {
    position: relative;
}

.vuecal__event:hover:after {
    content: attr(data-count) " ngày vắng";
    position: absolute;
    background: white;
    border: 1px solid #eee;
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 0.8em;
    z-index: 1;
    white-space: nowrap;
}
</style>
