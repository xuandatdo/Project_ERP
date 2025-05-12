<template>
    <div class="dashboard-container">

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="card">
                <h3>Tổng số xe</h3>
                <p>{{ totalVehicles }}</p>
            </div>
            <div class="card">
                <h3>Đối tác hoạt động</h3>
                <p>{{ activePartners }}</p>
            </div>
            <div class="card">
                <h3>Đơn vận chuyển</h3>
                <p>{{ totalTransports }}</p>
            </div>
            <div class="card">
                <h3>Tỷ lệ hoàn thành</h3>
                <p>{{ completionRate }}%</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="date-filter">
                <label>Thời gian:</label>
                <select v-model="timeRange" @change="fetchData">
                    <option value="month">Tháng này</option>
                    <option value="quarter">Quý này</option>
                    <option value="year">Năm này</option>
                </select>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid">
            <!-- Vehicle Statistics -->
            <div class="chart-container">
                <h2>Phân loại phương tiện</h2>
                <canvas ref="vehicleTypeChart"></canvas>
            </div>

            <!-- Transport Plan Status -->
            <div class="chart-container">
                <h2>Trạng thái vận chuyển</h2>
                <canvas ref="transportStatusChart"></canvas>
            </div>

            <!-- Monthly Transport Trends -->
            <div class="chart-container wide">
                <h2>Xu hướng vận chuyển theo tháng</h2>
                <canvas ref="monthlyTrendsChart"></canvas>
            </div>

            <!-- Partner Distribution -->
            <div class="chart-container">
                <h2>Phân bố đối tác</h2>
                <canvas ref="partnerDistChart"></canvas>
            </div>

            <!-- Vehicle Maintenance -->
            <div class="chart-container">
                <h2>Lịch bảo dưỡng xe</h2>
                <canvas ref="maintenanceChart"></canvas>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';
import axios from 'axios';

export default {
    name: 'TransportDataList',
    setup() {
        const timeRange = ref('month');
        const vehicleTypeChart = ref(null);
        const transportStatusChart = ref(null);
        const monthlyTrendsChart = ref(null);
        const partnerDistChart = ref(null);
        const maintenanceChart = ref(null);

        const totalVehicles = ref(0);
        const activePartners = ref(0);
        const totalTransports = ref(0);
        const completionRate = ref(0);

        let charts = {
            vehicleType: null,
            transportStatus: null,
            monthlyTrends: null,
            partnerDist: null,
            maintenance: null,
        };

        const fetchData = async () => {
            try {
                // Fetch vehicles data
                const vehiclesResponse = await axios.get('/api/vehicles');
                const vehicles = vehiclesResponse.data;
                totalVehicles.value = vehicles.length;

                // Fetch partners data
                const partnersResponse = await axios.get('/api/partners');
                const partners = partnersResponse.data;
                activePartners.value = partners.length;

                // Fetch transport plans data
                const transportResponse = await axios.get('/api/transport-plans');
                const transportPlans = transportResponse.data.data;
                totalTransports.value = transportPlans.length;

                // Calculate completion rate
                const completed = transportPlans.filter((plan) => plan.status === 'completed').length;
                completionRate.value = Math.round((completed / transportPlans.length) * 100) || 0;

                updateCharts(vehicles, partners, transportPlans);
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        };

        const updateCharts = (vehicles, partners, transportPlans) => {
            // Vehicle Type Chart (Pie)
            const vehicleTypes = groupByProperty(vehicles, 'type');
            const mappedVehicleTypes = {
                truck: 'Xe tải',
                van: 'Xe van',
                container: 'Container',
                pickup: 'Xe bán tải',
                other: 'Khác',
            };

            updatePieChart(charts.vehicleType, vehicleTypeChart.value, {
                labels: Object.keys(vehicleTypes).map((type) => mappedVehicleTypes[type] || 'Chưa phân loại'),
                data: Object.values(vehicleTypes).map((v) => v.length),
            });

            // Transport Status Chart (Doughnut)
            const transportStatus = groupByProperty(transportPlans, 'status');
            updateDoughnutChart(charts.transportStatus, transportStatusChart.value, {
                labels: ['Đang chuẩn bị', 'Đang vận chuyển', 'Hoàn thành', 'Chậm trễ'],
                data: [
                    transportStatus.preparing?.length || 0,
                    transportStatus.in_transit?.length || 0,
                    transportStatus.completed?.length || 0,
                    transportStatus.delayed?.length || 0,
                ],
            });

            // Monthly Trends Chart (Line)
            const monthlyData = groupByMonth(transportPlans);
            updateLineChart(charts.monthlyTrends, monthlyTrendsChart.value, {
                labels: monthlyData.labels,
                data: monthlyData.data,
            });

            // Partner Distribution Chart (Bar)
            const partnerTypes = {
                supplier: 'Nhà cung cấp',
                customer: 'Khách hàng',
                carrier: 'Đơn vị vận tải',
                warehouse: 'Kho hàng',
                other: 'Đối tác khác',
            };
            const partnerData = groupByProperty(partners, 'type');
            updateBarChart(charts.partnerDist, partnerDistChart.value, {
                labels: Object.keys(partnerData).map((type) => partnerTypes[type] || 'Chưa phân loại'),
                data: Object.values(partnerData).map((p) => p.length),
            });

            // Maintenance Schedule Chart (Bar)
            const maintenanceData = groupMaintenanceData(vehicles);
            updateBarChart(charts.maintenance, maintenanceChart.value, {
                labels: ['Cần bảo dưỡng', 'Sắp đến hạn', 'Mới bảo dưỡng'],
                data: maintenanceData.data,
            });
        };

        const initializeCharts = () => {
            charts.vehicleType = createPieChart(vehicleTypeChart.value);
            charts.transportStatus = createDoughnutChart(transportStatusChart.value);
            charts.monthlyTrends = createLineChart(monthlyTrendsChart.value);
            charts.partnerDist = createBarChart(partnerDistChart.value);
            charts.maintenance = createBarChart(maintenanceChart.value);
        };

        onMounted(() => {
            initializeCharts();
            fetchData();
        });

        return {
            timeRange,
            vehicleTypeChart,
            transportStatusChart,
            monthlyTrendsChart,
            partnerDistChart,
            maintenanceChart,
            totalVehicles,
            activePartners,
            totalTransports,
            completionRate,
            fetchData,
        };
    },
};

// Helper functions for chart creation and data manipulation
function createPieChart(canvas) {
    return new Chart(canvas, {
        type: 'pie',
        data: {
            labels: [],
            datasets: [
                {
                    data: [],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 10,
                    top: 0,
                    bottom: 10,
                },
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            size: 12,
                        },
                    },
                },
            },
        },
    });
}

function createDoughnutChart(canvas) {
    return new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [
                {
                    data: [],
                    backgroundColor: ['#FFB1C1', '#89CFF0', '#90EE90', '#FFB347'],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 10,
                    top: 0,
                    bottom: 10,
                },
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            size: 12,
                        },
                    },
                },
            },
        },
    });
}

function createLineChart(canvas) {
    return new Chart(canvas, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Số lượng vận chuyển',
                    data: [],
                    borderColor: '#36A2EB',
                    tension: 0.1,
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 20,
                    top: 10,
                    bottom: 10,
                },
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            size: 12,
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 11,
                        },
                        maxTicksLimit: 5,
                    },
                },
                x: {
                    ticks: {
                        font: {
                            size: 11,
                        },
                        maxRotation: 45,
                        minRotation: 45,
                    },
                },
            },
        },
    });
}

function createBarChart(canvas) {
    return new Chart(canvas, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [
                {
                    data: [],
                    backgroundColor: '#36A2EB',
                    borderRadius: 5,
                    maxBarThickness: 50,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 20,
                    top: 10,
                    bottom: 10,
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 11,
                        },
                        maxTicksLimit: 5,
                    },
                },
                x: {
                    ticks: {
                        font: {
                            size: 11,
                        },
                        maxRotation: 45,
                        minRotation: 45,
                    },
                },
            },
        },
    });
}

function groupByProperty(array, property) {
    return array.reduce((acc, item) => {
        const key = item[property];
        if (!acc[key]) {
            acc[key] = [];
        }
        acc[key].push(item);
        return acc;
    }, {});
}

function groupByMonth(transportPlans) {
    const months = Array.from({ length: 12 }, (_, i) => {
        const date = new Date();
        date.setMonth(i);
        return date.toLocaleString('vi-VN', { month: 'long' });
    });

    const data = new Array(12).fill(0);

    transportPlans.forEach((plan) => {
        const date = new Date(plan.expected_time);
        const month = date.getMonth();
        data[month]++;
    });

    return {
        labels: months,
        data: data,
    };
}

function groupMaintenanceData(vehicles) {
    const nextMonth = new Date();
    nextMonth.setMonth(nextMonth.getMonth() + 1);

    const maintenanceCount = {
        thisMonth: 0,
        nextMonth: 0,
        later: 0,
    };

    vehicles.forEach((vehicle) => {
        const maintenanceDate = new Date(vehicle.maintenance_date);
        const now = new Date();

        if (maintenanceDate.getMonth() === now.getMonth()) {
            maintenanceCount.thisMonth++;
        } else if (maintenanceDate.getMonth() === nextMonth.getMonth()) {
            maintenanceCount.nextMonth++;
        } else {
            maintenanceCount.later++;
        }
    });

    return {
        labels: ['Cần bảo dưỡng', 'Sắp đến hạn', 'Mới bảo dưỡng'],
        data: [maintenanceCount.thisMonth, maintenanceCount.nextMonth, maintenanceCount.later],
    };
}

function updatePieChart(chart, canvas, data) {
    if (!chart) return;
    chart.data.labels = data.labels;
    chart.data.datasets[0].data = data.data;
    chart.update();
}

function updateDoughnutChart(chart, canvas, data) {
    if (!chart) return;
    chart.data.labels = data.labels;
    chart.data.datasets[0].data = data.data;
    chart.update();
}

function updateLineChart(chart, canvas, data) {
    if (!chart) return;
    chart.data.labels = data.labels;
    chart.data.datasets[0].data = data.data;
    chart.update();
}

function updateBarChart(chart, canvas, data) {
    if (!chart) return;
    chart.data.labels = data.labels;
    chart.data.datasets[0].data = data.data;
    chart.update();
}
</script>

<style scoped>
.dashboard-container {
    padding: 20px;
    background-color: #f8f9fa;
    min-height: 100vh;
}

h1 {
    margin-bottom: 25px;
    color: #2c3e50;
    font-size: 24px;
    font-weight: 600;
}

.summary-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.card {
    background: white;
    padding: 20px 15px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    text-align: center;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-3px);
}

.card h3 {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 12px;
    font-weight: 500;
}

.card p {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.filters {
    margin-bottom: 25px;
    display: flex;
    gap: 20px;
    background-color: white;
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.date-filter {
    display: flex;
    align-items: center;
    gap: 12px;
}

.date-filter label {
    font-weight: 500;
    color: #2c3e50;
    white-space: nowrap;
}

.date-filter select {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background-color: white;
    font-size: 13px;
    color: #2c3e50;
    cursor: pointer;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.chart-container {
    background: white;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    height: 350px;
    display: flex;
    flex-direction: column;
    position: relative;
}

.chart-container.wide {
    grid-column: span 2;
    height: 400px;
}

.chart-container h2 {
    margin: 0 0 15px 0;
    font-size: 16px;
    color: #2c3e50;
    font-weight: 600;
    padding: 0 15px;
}

canvas {
    flex: 1;
    width: 100% !important;
    max-height: calc(100% - 40px) !important;
}

@media (max-width: 1200px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }

    .chart-container.wide {
        grid-column: auto;
        height: 350px;
    }

    .summary-cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .chart-container {
        height: 300px;
    }
}

@media (max-width: 768px) {
    .summary-cards {
        grid-template-columns: 1fr;
    }

    .dashboard-container {
        padding: 15px;
    }

    .chart-container {
        height: 280px;
    }

    .chart-container.wide {
        height: 300px;
    }

    h2 {
        font-size: 14px;
    }
}
</style>
