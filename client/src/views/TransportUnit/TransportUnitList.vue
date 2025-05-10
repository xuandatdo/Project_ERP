<template>
  <div class="transport-unit">
    <!-- Filters -->
    <div class="filters">
      <input type="text" v-model="filters.search" placeholder="Biển số xe..." @input="fetchPlans" />
      <select v-model="filters.dateOption" @change="fetchPlans">
        <option value="today">Hôm nay</option>
        <option value="custom">Tùy chọn</option>
      </select>
      <input type="date" v-if="filters.dateOption === 'custom'" v-model="filters.date" @change="fetchPlans" />
      <select v-model="filters.shift" @change="fetchPlans">
        <option value="">Tất cả</option>
        <option value="S1">Ca S1</option>
        <option value="S2">Ca S2</option>
        <option value="C1">Ca C1</option>
        <option value="C2">Ca C2</option>
      </select>
    </div>

    <!-- Progress -->
    <div class="progress">
      <span>Tiến độ {{ completed }}/{{ total }}</span>
      <div class="bar">
        <div class="fill" :style="{ width: progressPercent + '%' }"></div>
      </div>
    </div>

    <!-- Status Columns -->
    <div class="status-columns">
      <div v-for="(plans, status) in groupedPlans" :key="status" class="status-column">
        <h3 :class="'status-title ' + statusClass(status)">
          {{ statusText(status) }}
        </h3>
        <div v-for="plan in plans" :key="plan.id" class="plan-card">
          <div><strong>Mã lô hàng: {{ plan.id }}</strong></div>
          <div>Tải trọng: {{ plan.weight }} KG</div>
          <div>Xe: {{ plan.license_plate }}</div>
          <div>{{ formatTime(plan.expected_time) }}</div>
        </div>
      </div>
    </div>

    <!-- Pagination Controls -->
    <div class="pagination">
      <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages">Next</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      transportPlans: [],
      filters: {
        search: '',
        dateOption: 'today',
        date: '',
        shift: '' // This will hold S1, S2, C1, or C2
      },
      currentPage: 1, // Track the current page
      itemsPerPage: 10 // Number of items per page
    };
  },
  computed: {
    groupedPlans() {
      const groups = {
        preparing: [],
        in_transit: [],
        completed: [],
        delayed: []
      };
      this.paginatedPlans.forEach(plan => {
        groups[plan.status]?.push(plan);
      });
      return groups;
    },
    filteredPlans() {
      const search = this.filters.search.toLowerCase();

      return this.transportPlans.filter(plan => {
        const matchesSearch =
          !search ||
          (plan.code && plan.code.toLowerCase().includes(search)) ||
          (plan.id && String(plan.id).toLowerCase().includes(search)) ||
          (plan.license_plate && plan.license_plate.toLowerCase().includes(search));

        const statusMap = {
          S1: 'preparing',
          S2: 'in_transit',
          C1: 'completed',
          C2: 'delayed'
        };
        const selectedStatus = statusMap[this.filters.shift] || null;
        const planDate = new Date(plan.expected_time).toISOString().split('T')[0];

        return (
          matchesSearch &&
          (!selectedStatus || plan.status === selectedStatus) &&
          (this.filters.dateOption !== 'custom' || planDate === this.filters.date)
        );
      });
    },
    paginatedPlans() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredPlans.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredPlans.length / this.itemsPerPage);
    },
    total() {
      return this.transportPlans.length;
    },
    completed() {
      return this.groupedPlans.completed.length;
    },
    progressPercent() {
      return this.total > 0 ? (this.completed / this.total) * 100 : 0;
    }
  },
  methods: {
    formatTime(datetime) {
      return new Date(datetime).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    },
    statusText(status) {
      return {
        preparing: 'Đang chuẩn bị',
        in_transit: 'Đang vận chuyển',
        completed: 'Hoàn thành',
        delayed: 'Chậm trễ'
      }[status] || status;
    },
    statusClass(status) {
      return {
        preparing: 'yellow',
        delayed: 'brown',
        in_transit: 'blue',
        completed: 'green'
      }[status];
    },
    async fetchPlans() {
      try {
        const response = await axios.get('/api/transport-plans', {
          params: {
            search: this.filters.search,
            date: this.filters.dateOption === 'custom' ? this.filters.date : '',
            shift: this.filters.shift
          }
        });
        this.transportPlans = response.data.data;
      } catch (error) {
        console.error('Error fetching transport plans:', error);
      }
    },
    changePage(page) {
      if (page > 0 && page <= this.totalPages) {
        this.currentPage = page;
      }
    }
  },
  mounted() {
    this.fetchPlans();
  }
};
</script>

<style scoped>
.transport-unit {
  padding: 20px;
}
.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}
.progress {
  margin-bottom: 20px;
}
.bar {
  height: 10px;
  background: #ddd;
  border-radius: 5px;
  overflow: hidden;
}
.fill {
  height: 100%;
  background: #00aaff;
}
.status-columns {
  display: flex;
  gap: 20px; /* Add spacing between columns */
  overflow-x: auto; /* Allow horizontal scrolling for smaller screens */
  padding: 10px 0; /* Add padding for better spacing */
}

.status-column {
  flex: 1; /* Make columns flexible */
  min-width: 300px; /* Ensure a minimum width for each column */
  background: #f9f9f9; /* Light background for columns */
  border: 1px solid #ddd; /* Add a border for separation */
  border-radius: 8px; /* Rounded corners */
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
  padding: 15px; /* Add padding inside the column */
}

.status-title {
  padding: 10px;
  color: white;
  font-weight: bold;
  text-align: center;
  border-radius: 4px;
  margin-bottom: 10px; /* Add spacing below the title */
}

.status-title.yellow {
  background: #f6c23e; /* Preparing */
}

.status-title.brown {
  background: #8b5e3c; /* Delayed */
}

.status-title.blue {
  background: #28c0f0; /* In Transit */
}

.status-title.green {
  background: #1cc88a; /* Completed */
}

.plan-card {
  background: #fff; /* White background for cards */
  border: 1px solid #ddd; /* Border for separation */
  padding: 10px;
  margin-bottom: 10px; /* Add spacing between cards */
  border-radius: 6px; /* Rounded corners */
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
  transition: transform 0.2s, box-shadow 0.2s; /* Add hover effect */
}

.plan-card:hover {
  transform: translateY(-3px); /* Slight lift on hover */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
}

.plan-card div {
  margin-bottom: 5px; /* Add spacing between details */
  font-size: 14px; /* Adjust font size for readability */
}

.plan-card strong {
  font-weight: bold;
  color: #333; /* Darker text for emphasis */
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-top: 20px;
}

.pagination button {
  padding: 5px 10px;
  border: 1px solid #ddd;
  background: #f9f9f9;
  cursor: pointer;
  border-radius: 4px;
}

.pagination button:disabled {
  background: #eee;
  cursor: not-allowed;
}
</style>
