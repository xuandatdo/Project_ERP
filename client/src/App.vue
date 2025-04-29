<template>
  <div v-if="$route.path === '/login'">
    <router-view></router-view>
  </div>
  <div v-else class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="menu">
        <div class="logo">
          <img src="../src/assets/img-logo.png" alt="Logo" />
        </div>
        <!-- Menu Quản lý nhân sự -->
        <div class="menu-item" @click="toggleHRMenu">
          <span>Quản lý nhân sự</span>
          <i :class="hrMenuOpen ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
        </div>

        <div class="sub-menu" v-show="hrMenuOpen">
          <router-link to="/employees" class="sub-menu-item">Nhân sự</router-link>
          <router-link to="/departments" class="sub-menu-item">Phòng ban</router-link>
          <router-link to="/positions" class="sub-menu-item">Vị trí</router-link>
          <router-link to="/attendance" class="sub-menu-item">Chấm công</router-link>
          <router-link to="/payroll" class="sub-menu-item">Tính lương</router-link>
          <router-link to="/tasks" class="sub-menu-item">Công việc</router-link>
          <router-link to="/statistics" class="sub-menu-item">Thống kê</router-link>
        </div>

        <!-- Menu Quản lý vận tải -->
        <div class="menu-item" @click="toggleTransportMenu">
          <span>Quản lý vận tải</span>
          <i :class="transportMenuOpen ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
        </div>

        <div class="sub-menu" v-show="transportMenuOpen">
          <router-link to="/data" class="sub-menu-item">Dữ liệu</router-link>
          <router-link to="/transport" class="sub-menu-item">Kế hoạch vận chuyển</router-link>
          <router-link to="/units" class="sub-menu-item">Đơn vị vận chuyển</router-link>
          <router-link to="/partners" class="sub-menu-item">Đối tác vận chuyển</router-link>
          <router-link to="/vehicles" class="sub-menu-item">Phương tiện vận chuyển</router-link>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Topbar -->
      <div class="topbar">
        <h2>{{ currentPageTitle }}</h2>
      </div>
      <!-- Router View -->
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      hrMenuOpen: false,
      transportMenuOpen: false,
    };
  },
  computed: {
    currentPageTitle() {
      switch (this.$route.path) {
        case '/': return 'Danh sách nhân sự';
        case '/create': return 'Thêm nhân viên';
        case '/edit/:id': return 'Sửa nhân viên';
        case '/departments': return 'Quản lý phòng ban';
        case '/departments/create': return 'Thêm phòng ban mới';
        case '/positions': return 'Quản lý vị trí';
        case '/positions/create': return 'Thêm vị trí mới';
        case '/attendance': return 'Chấm công';
        case '/payroll': return 'Tính lương';
        case '/tasks': return 'Công việc';
        case '/statistics': return 'Thống kê';
        case '/data': return 'Dữ liệu vận tải';
        case '/transport': return 'Kế hoạch vận chuyển';
        case '/transport/create': return 'Tạo kế hoạch vận chuyển';
        case '/transport/:id/edit': return 'Sửa kế hoạch vận chuyển';
        case '/units': return 'Đơn vị vận chuyển';
        case '/partners': return 'Đối tác vận chuyển';
        case '/vehicles': return 'Phương tiện vận chuyển';
        default: return 'Quản lý nhân sự';
      }
    },
  },
  methods: {
    toggleHRMenu() {
      this.hrMenuOpen = !this.hrMenuOpen;
      if (this.hrMenuOpen) this.transportMenuOpen = false;
    },
    toggleTransportMenu() {
      this.transportMenuOpen = !this.transportMenuOpen;
      if (this.transportMenuOpen) this.hrMenuOpen = false;
    },
  },
};
</script>

<style>
/* Reset CSS để loại bỏ margin và padding mặc định */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}
</style>

<style scoped>
.dashboard {
  display: flex;
  height: 100vh;
  width: 100%;
  margin: 0;
  padding: 0;
}

.sidebar {
  width: 250px;
  background-color: #2c3e50;
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 0;
  margin: 0;
  position: relative;
  left: 0;
}

.menu {
  flex-grow: 1;
}

.menu-item {
  padding: 15px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.menu-item:hover {
  background-color: #34495e;
}

.sub-menu {
  background-color: #34495e;
}

.sub-menu-item {
  display: block;
  padding: 10px 30px;
  color: white;
  text-decoration: none;
}

.sub-menu-item:hover {
  background-color: #3e5c76;
}

.logo {
  text-align: center;
  padding: 20px 0;
}

.logo img {
  max-width: 100px;
}

.main-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.topbar {
  background-color: #fff;
  padding: 15px 20px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.topbar h2 {
  margin: 0;
  color: #333;
}
</style>