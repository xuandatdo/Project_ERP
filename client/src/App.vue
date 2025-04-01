<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="menu">
        <!-- <div class="logo">
          <img src="../client/src/assets/logo.jpg" alt="Logo" />
        </div> -->
        <div class="menu-item" @click="toggleSubMenu">
          <span>Quản lý nhân sự</span>
          <i :class="subMenuOpen ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
        </div>

        <div class="sub-menu" v-show="subMenuOpen">
          <router-link to="/" class="sub-menu-item">Danh sách nhân sự</router-link>
          <router-link to="/attendance" class="sub-menu-item">Chấm công</router-link>
          <router-link to="/payroll" class="sub-menu-item">Tính lương</router-link>
          <router-link to="/tasks" class="sub-menu-item">Công việc</router-link>
          <router-link to="/statistics" class="sub-menu-item">Thống kê</router-link>
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
      subMenuOpen: false,
    };
  },
  computed: {
    currentPageTitle() {
      switch (this.$route.path) {
        case '/': return 'Danh sách nhân sự';
        case '/create': return 'Thêm nhân viên';
        case '/edit/:id': return 'Sửa nhân viên';
        case '/attendance': return 'Chấm công';
        case '/payroll': return 'Tính lương';
        case '/tasks': return 'Công việc';
        case '/statistics': return 'Thống kê';
        default: return 'Quản lý nhân sự';
      }
    },
  },
  methods: {
    toggleSubMenu() {
      this.subMenuOpen = !this.subMenuOpen;
    },
  },
};
</script>

<style scoped>
.dashboard {
  display: flex;
  height: 100vh;
}

.sidebar {
  width: 250px;
  background-color: #2c3e50;
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 20px 0;
}

.menu {
  flex-grow: 1;
}

.menu-item {
  padding: 15px 20px;
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
  padding: 10px 40px;
  color: white;
  text-decoration: none;
}

.sub-menu-item:hover {
  background-color: #3e5c76;
}

.logo {
  text-align: center;
  padding: 20px;
}

.logo img {
  max-width: 100px;
}

.main-content {
  flex-grow: 1;
  background-color: #f4f6f9;
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