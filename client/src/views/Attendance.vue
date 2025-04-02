<template>
  <div class="attendance-container">
    <div class="header">
      <div class="date-controls">
        <button @click="previousDay" class="btn btn-outline-secondary">
          <font-awesome-icon icon="chevron-left" />
        </button>
        <input
          type="date"
          v-model="selectedDate"
          @change="fetchAttendance"
          class="form-control date-input"
        />
        <button @click="nextDay" class="btn btn-outline-secondary">
          <font-awesome-icon icon="chevron-right" />
        </button>
        <button
          @click="setToday"
          class="btn btn-outline-primary today-btn"
        >
          Today
        </button>
      </div>
      <input
          v-model="searchQuery"
          type="text"
          placeholder="Tìm kiếm nhân viên (mã hoặc tên)"
          class="form-control search-input"
      />
    </div>

    <div v-if="loading" class="loading-spinner">
      <i class="fas fa-spinner fa-spin"></i> Loading...
    </div>

    <div v-else>
      <div class="attendance-table-container">
        <table class="table table-striped attendance-table">
          <thead>
            <tr>
              <th>Employee ID</th>
              <th>Name</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <tr v-for="employee in filteredEmployees" :key="employee.id">
            <td>{{ employee.id }}</td>
            <td>{{ employee.name }}</td>
            <td>
           <span :class="statusClass(employee.status)">
             {{ employee.status || 'Not marked' }}
           </span>
                </td>
                <td>
                  <select
                      v-model="employee.status"
                      @change="updateStatus(employee)"
                      class="form-control status-select"
                  >
                    <option value="">Select Status</option>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                  </select>
                </td>
              </tr>
          </tbody>
        </table>
      </div>

      <div class="action-buttons">
        <button
          @click="saveAttendance"
          class="btn btn-primary save-btn"
          :disabled="saving"
        >
          <span v-if="saving">
            <i class="fas fa-spinner fa-spin"></i> Saving...
          </span>
          <span v-else>
            <i class="fas fa-save"></i> Save Attendance
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Attendance',
  data() {
    return {
      selectedDate: new Date().toISOString().split('T')[0],
      employees: [],
      loading: false,
      saving: false,
      searchQuery: '',
    };
  },
  computed: {
    filteredEmployees() {
      if (!this.searchQuery) {
        return this.employees; // Return all employees if no search query
      }
      const query = this.searchQuery.toLowerCase(); // Normalize search term
      return this.employees.filter(employee =>
          employee.name.toLowerCase().includes(query) || // Match employee name
          String(employee.id).includes(query)           // Match employee ID
      );
    }
  },
  created() {
    this.fetchAttendance();
  },
  methods: {
    async fetchAttendance() {
      this.loading = true;
      try {
        const response = await axios.get('/api/attendance', {
          params: { date: this.selectedDate }
        });
        this.employees = response.data;
      } catch (error) {
        console.error('Error fetching attendance:', error);
        this.$toast.error('Failed to load attendance data');
      } finally {
        this.loading = false;
      }
    },
    updateStatus(employee) {
      // Immediate feedback for the user
      this.$toast.success(`Status updated for ${employee.name}`);
    },
    async saveAttendance() {
      this.saving = true;
      try {
        const attendanceData = this.employees.map(employee => ({
          employee_id: employee.id,
          attendance_date: this.selectedDate,
          status: employee.status || '',
        }));

        // Log the attendance data
        console.log(attendanceData);

        await axios.post('/api/attendance', { attendance: attendanceData });

        this.$toast.success('Attendance saved successfully');
      } catch (error) {
        console.error('Error saving attendance:', error);
        this.$toast.error('Failed to save attendance');
      } finally {
        this.saving = false;
      }
    },
    setToday() {
      this.selectedDate = new Date().toISOString().split('T')[0];
      this.fetchAttendance();
    },
    previousDay() {
      const date = new Date(this.selectedDate);
      date.setDate(date.getDate() - 1);
      this.selectedDate = date.toISOString().split('T')[0];
      this.fetchAttendance();
    },
    nextDay() {
      const date = new Date(this.selectedDate);
      date.setDate(date.getDate() + 1);
      this.selectedDate = date.toISOString().split('T')[0];
      this.fetchAttendance();
    },
    statusClass(status) {
      return {
        'status-present': status === 'present',
        'status-absent': status === 'absent',
        'status-late': status === 'late',
        'status-half-day': status === 'half-day',
        'status-on-leave': status === 'on-leave',
        'status-unmarked': !status
      };
    }
  }
};
</script>

<style scoped>
/* General Container Styling */
.attendance-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Header Styling */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 10px;
  border-bottom: 2px solid #ddd;
}

.header h2 {
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
  margin: 0;
}

.date-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.date-controls .date-input {
  width: 150px;
}

/* Loading Spinner Styling */
.loading-spinner {
  text-align: center;
  font-size: 1rem;
  color: #555;
  padding: 20px 0;
}

/* Table Styling */
.attendance-table-container {
  margin-top: 15px;
  overflow-x: auto;
}

.attendance-table {
  width: 100%;
  border-collapse: collapse;
}

.attendance-table th, .attendance-table td {
  text-align: left;
  padding: 12px;
  border-bottom: 1px solid #ddd;
}

.attendance-table th {
  background-color: #f1f1f1;
  font-weight: bold;
  color: #444;
}

.attendance-table tr:hover {
  background-color: #f9f9f9;
}

.attendance-table .status-select {
  padding: 5px;
  font-size: 0.9rem;
}

/* Status Labels */
span.status-present {
  color: #28a745;
  font-weight: bold;
}

span.status-absent {
  color: #dc3545;
  font-weight: bold;
}

span.status-not-marked {
  color: #6c757d;
  font-style: italic;
}

/* Buttons */
.button, .btn {
  display: inline-block;
  padding: 8px 12px;
  font-size: 0.9rem;
  border-radius: 4px;
  text-decoration: none;
  text-align: center;
  cursor: pointer;
  transition: background-color 0.3s ease, box-shadow 0.2s ease;
}

.btn {
  border: 1px solid transparent;
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
  color: #fff;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #004085;
}

.btn-outline-secondary {
  background-color: #fff;
  border: 1px solid #6c757d;
  color: #6c757d;
}

.btn-outline-secondary:hover {
  background-color: #6c757d;
  color: #fff;
}

.btn-outline-primary.today-btn {
  background-color: #0069d9;
  color: #fff;
}

.today-btn:hover {
  background-color: #004085;
}

.save-btn {
  background-color: #28a745;
  border-color: #28a745;
}

.save-btn:hover {
  background-color: #1e7e34;
  border-color: #1c7430;
}

.action-buttons {
  text-align: right;
  margin-top: 15px;
}

.search-input {
  margin-left: 5px;
  padding: 5px;
  font-size: 1rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  width: 100%;
  max-width: 400px;
}

</style>