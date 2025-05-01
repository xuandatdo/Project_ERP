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
import TransportPlanList from "../views/TransportPlan/TransportPlanList.vue";
import TransportPlanCreate from "../views/TransportPlan/TransportPlanCreate.vue";
import TransportPlanEdit from "../views/TransportPlan/TransportPlanEdit.vue";
import TransportUnitList from "../views/TransportUnit/TransportUnitList.vue";
import PartnerList from "../views/Partners/PartnerList.vue";
import PartnerCreate from "../views/Partners/PartnerCreate.vue";
import PartnerEdit from "../views/Partners/PartnerEdit.vue";
import VehicleList from "../views/Vehicle/VehicleList.vue";
import VehicleCreate from "../views/Vehicle/VehicleCreate.vue";
import VehicleEdit from "../views/Vehicle/VehicleEdit.vue";
import TransportDataList from "../views/TransportData/TransportDataList.vue";

const routes = [
  { path: "/", redirect: "/employees" },
  { path: "/login", component: Login },
  {
    path: "/employees",
    component: EmployeeList,
    meta: { requiresAuth: true },
  },
  {
    path: "/create",
    component: EmployeeCreate,
    meta: { requiresAuth: true },
  },
  {
    path: "/edit/:id",
    component: EmployeeEdit,
    meta: { requiresAuth: true },
  },
  // Routes cho Attendance
  {
    path: "/attendance",
    name: "Attendance",
    component: Attendance,
    meta: { requiresAuth: true },
  },
  // Routes cho Department
  {
    path: "/departments",
    component: DepartmentList,
    meta: { requiresAuth: true },
  },
  {
    path: "/departments/create",
    component: DepartmentCreate,
    meta: { requiresAuth: true },
  },
  {
    path: "/departments/:id/edit",
    component: DepartmentEdit,
    meta: { requiresAuth: true },
  },
  // Routes cho Position
  {
    path: "/positions",
    component: PositionList,
    meta: { requiresAuth: true },
  },
  {
    path: "/positions/create",
    component: PositionCreate,
    meta: { requiresAuth: true },
  },
  {
    path: "/positions/:id/edit",
    component: PositionEdit,
    meta: { requiresAuth: true },
  },
  // Routes cho Task
  {
    path: "/tasks",
    component: TaskList,
    meta: { requiresAuth: true },
  },
  {
    path: "/tasks/create",
    component: TaskCreate,
    meta: { requiresAuth: true },
  },
  {
    path: "/tasks/:id/edit",
    component: TaskEdit,
    meta: { requiresAuth: true },
  },
  //Routes cho Statistics
  {
    path: "/statistics",
    component: Statistics,
    meta: { requiresAuth: true },
  },
  // Routes cho Transport Plan
  {
    path: "/transport",
    component: TransportPlanList,
    meta: { requiresAuth: true },
  },
  {
    path: "/transport/create",
    component: TransportPlanCreate,
    meta: { requiresAuth: true },
  },
  {
    path: "/transport/:id/edit",
    component: TransportPlanEdit,
    meta: { requiresAuth: true },
  },
  // Routes cho Unit
  {
    path: "/units",
    component: TransportUnitList,
    meta: { requiresAuth: true },
  },
  // Routes cho Partner
  {
    path: "/partners",
    component: PartnerList,
    meta: { requiresAuth: true },
  },
  {
    path: "/partners/create",
    component: PartnerCreate,
    meta: { requiresAuth: true },
  },
  {
    path: "/partners/:id/edit",
    component: PartnerEdit,
    meta: { requiresAuth: true },
  },
  // Routes cho Vehicle
  {
    path: "/vehicles",
    component: VehicleList,
    meta: { requiresAuth: true },
  },
  {
    path: "/vehicles/create",
    component: VehicleCreate,
    meta: { requiresAuth: true },
  },
  {
    path: "/vehicles/:id/edit",
    component: VehicleEdit,
    meta: { requiresAuth: true },
  },
  // Routes cho Vehicle
  {
    path: "/data",
    component: TransportDataList,
    meta: { requiresAuth: true },
  },
  // Routes cho Login
  {
    path: "/login",
    component: Login,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard
router.beforeEach((to, from, next) => {
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    // Kiểm tra trạng thái đăng nhập
    if (localStorage.getItem("isAuthenticated") !== "true") {
      next("/login");
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
