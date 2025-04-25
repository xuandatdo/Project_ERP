<template>
  <div class="attendance-container">
    <div class="header">
      <div class="date-controls">
        <button @click="previousDay" class="btn btn-outline-secondary">
          <font-awesome-icon icon="chevron-left" />
        </button>
        <input type="date" v-model="selectedDate" @change="fetchAttendance" class="form-control date-input" />
        <button @click="nextDay" class="btn btn-outline-secondary">
          <font-awesome-icon icon="chevron-right" />
        </button>
        <button @click="setToday" class="btn btn-outline-primary today-btn">
          Hôm nay
        </button>
      </div>
      <input v-model="searchQuery" type="text" placeholder="Tìm kiếm nhân viên (tên)"
        class="form-control search-input" />
    </div>

    <div v-if="loading" class="loading-spinner">
      <i class="fas fa-spinner fa-spin"></i> Loading...
    </div>

    <div v-else>
      <div class="attendance-table-container">
        <table class="table table-striped attendance-table">
          <thead>
            <tr>
            <th>Mã số nhân viên</th>
            <th>Họ và tên</th>
            <th>Trạng thái</th>
            <th>Có mặt</th>
            <th colspan="2">Vắng mặt</th> <!-- Parent column for Absent -->
            <th>Đi trễ</th>
            <th>Số lần trễ</th>
            <th>Ghi chú</th>
          </tr>
          <tr>
            <th></th> <!-- Empty cell for Employee ID -->
            <th></th> <!-- Empty cell for Name -->
            <th></th> <!-- Empty cell for Status -->
            <th></th> <!-- Empty cell for Present -->
            <th class="with-permission">Có phép</th> <!-- Sub-column for "With Permission" -->
            <th class="without-permission">Không phép</th> <!-- Sub-column for "Without Permission" -->
            <th></th> <!-- Empty cell for Late -->
            <th></th> <!-- Empty cell for Late Count -->
            <th></th> <!-- Empty cell for Note -->
          </tr>
          </thead>
          <tbody>
            <tr v-for="employee in filteredEmployees" :key="employee.id">
              <td>{{ "NV" + employee.id }}</td>
              <td>{{ employee.name }}</td>
              <td>
                <span :class="statusClass(employee.status)">
                  {{ employee.status || 'Not marked' }}
                </span>
              </td>
              <td>
                <input
                  type="checkbox"
                  :checked="employee.status === 'present'"
                  @change="updateStatus(employee, 'present', $event.target.checked)"
                />
              </td>
              <td class="with-permission">
                <input
                  type="checkbox"
                  :checked="employee.status === 'absent_with_permission'"
                  @change="updateStatus(employee, 'absent_with_permission', $event.target.checked)"
                />
              </td>
              <td class="without-permission">
                <input
                  type="checkbox"
                  :checked="employee.status === 'absent_without_permission'"
                  @change="updateStatus(employee, 'absent_without_permission', $event.target.checked)"
                />
              </td>
              <td>
                <input
                  type="checkbox"
                  :checked="employee.status === 'late'"
                  @change="updateStatus(employee, 'late', $event.target.checked)"
                />
              </td>
              <td>{{ employee.late_count || 0 }}</td>
              <td>
                <input
                  type="text"
                  v-model="employee.note"
                  placeholder="Ghi chú"
                  class="form-control"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="action-buttons">
        <button @click="saveAttendance" class="btn btn-primary save-btn" :disabled="saving">
          <span v-if="saving">
            <i class="fas fa-spinner fa-spin"></i> Saving...
          </span>
          <span v-else>
            <i class="fas fa-save"></i> Lưu điểm danh
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useToast } from "vue-toastification";

export default {
  name: 'Attendance',
  setup() {
    const toast = useToast(); // Access the toast instance
    return { toast };
  },
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
        this.toast.error('Failed to load attendance data'); // Use this.toast
      } finally {
        this.loading = false;
      }
    },
    updateStatus(employee, status, isChecked) {
      if (!isChecked) {
        // Reset all statuses
        employee.status = '';
        return;
      }
      
        // Set the selected status
        if (status === 'present') {
          employee.status = 'present';
        } else if (status === 'absent_with_permission') {
          employee.status = 'absent_with_permission';
        } else if (status === 'absent_without_permission') {
          employee.status = 'absent_without_permission';
        } else if (status === 'late') {
          if (employee.status !== 'late') {
          employee.late_count = (employee.late_count || 0) + 1; // Increment late count only if it's the first late check
          }
          employee.status = 'late';
        }

      this.toast.success(`Status updated for ${employee.name}`);
    },
    async saveAttendance() {
      this.saving = true;
      try {
        const attendanceData = this.employees.map(employee => ({
          employee_id: employee.id,
          attendance_date: this.selectedDate,
          status: employee.status || '',
          note: employee.note || '',
        }));

        // Log the attendance data
        console.log(attendanceData);

        await axios.post('/api/attendance', { attendance: attendanceData });

        this.toast.success('Attendance saved successfully');
      } catch (error) {
        console.error('Error saving attendance:', error);
        this.toast.error('Failed to save attendance');
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
        'status-absent_with_permission': status === 'absent_with_permission',
        'status-absent_without_permission': status === 'absent_without_permission',
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
  max-width: 1800px;
  /* margin: 0 auto; */
  padding: 20px;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  margin: 10px;
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

.attendance-table th,
.attendance-table td {
  text-align: center;
  padding: 12px;
  border: 1px solid #ddd;
  
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

span.status-absent_with_permission {
  color: #dc3545;
  font-weight: bold;
}
span.status-absent_without_permission {
  color: #dc3545;
  font-weight: bold;
}
span.status-late {
  color: #ffc107;
  font-weight: bold;
}

span.status-not-marked {
  color: #6c757d;
  font-style: italic;
}

/* Buttons */
.button,
.btn {
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