import { createRouter, createWebHistory } from "vue-router";
import EmployeeList from "../views/Employee/EmployeeList.vue";
import EmployeeCreate from "../views/Employee/EmployeeCreate.vue";
import EmployeeEdit from "../views/Employee/EmployeeEdit.vue";
import Attendance from "../views/Attendance.vue";
import DepartmentList from "../views/Department/DepartmentList.vue";
import DepartmentCreate from "../views/Department/DepartmentCreate.vue";
import DepartmentEdit from "../views/Department/DepartmentEdit.vue";
import PositionList from "../views/Position/PositionList.vue";
import PositionCreate from "../views/Position/PositionCreate.vue";
import PositionEdit from "../views/Position/PositionEdit.vue";
import TaskList from "../views/Task/TaskList.vue";
import TaskCreate from "../views/Task/TaskCreate.vue";
import TaskEdit from "../views/Task/TaskEdit.vue";
import Statistics from "../views/Statistics.vue";
import Login from "../views/Login.vue";

const routes = [
  { path: "/", redirect: "/employees" },
  { path: "/login", component: Login },
  { 
    path: "/employees", 
    component: EmployeeList,
    meta: { requiresAuth: true }
  },
  { 
    path: "/create", 
    component: EmployeeCreate,
    meta: { requiresAuth: true }
  },
  { 
    path: "/edit/:id", 
    component: EmployeeEdit,
    meta: { requiresAuth: true }
  },
  // Routes cho Attendance
  { 
    path: "/attendance", 
    name: "Attendance", 
    component: Attendance,
    meta: { requiresAuth: true }
  },
  // Routes cho Department
  { 
    path: "/departments", 
    component: DepartmentList,
    meta: { requiresAuth: true }
  },
  { 
    path: "/departments/create", 
    component: DepartmentCreate,
    meta: { requiresAuth: true }
  },
  { 
    path: "/departments/:id/edit", 
    component: DepartmentEdit,
    meta: { requiresAuth: true }
  },
  // Routes cho Position
  { 
    path: "/positions", 
    component: PositionList,
    meta: { requiresAuth: true }
  },
  { 
    path: "/positions/create", 
    component: PositionCreate,
    meta: { requiresAuth: true }
  },
  { 
    path: "/positions/:id/edit", 
    component: PositionEdit,
    meta: { requiresAuth: true }
  },
  // Routes cho Task
  { 
    path: "/tasks", 
    component: TaskList,
    meta: { requiresAuth: true }
  },
  { 
    path: "/tasks/create", 
    component: TaskCreate,
    meta: { requiresAuth: true }
  },
  { 
    path: "/tasks/:id/edit", 
    component: TaskEdit,
    meta: { requiresAuth: true }
  },
  //Routes cho Statistics
  { 
    path: "/statistics", 
    component: Statistics,
    meta: { requiresAuth: true }
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard
router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // Kiểm tra trạng thái đăng nhập
    if (localStorage.getItem('isAuthenticated') !== 'true') {
      next('/login');
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
