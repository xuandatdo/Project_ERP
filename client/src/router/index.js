import { createRouter, createWebHistory } from "vue-router";
import EmployeeList from "../views/EmployeeList.vue";
import EmployeeCreate from "../views/EmployeeCreate.vue";
import EmployeeEdit from "../views/EmployeeEdit.vue";
import Attendance from "../views/Attendance.vue";

const routes = [
  { path: "/", component: EmployeeList },
  { path: "/create", component: EmployeeCreate },
  { path: "/edit/:id", component: EmployeeEdit },
  {
    path: "/attendance",
    name: "Attendance",
    component: Attendance, // Chấm công
  },
  //   {
  //     path: "/payroll",
  //     name: "Payroll",
  //     component: () => import("../components/Payroll.vue"), // Tính lương
  //   },
  //   {
  //     path: "/tasks",
  //     name: "Tasks",
  //     component: () => import("../components/Tasks.vue"), // Công việc
  //   },
  //   {
  //     path: "/statistics",
  //     name: "Statistics",
  //     component: () => import("../components/Statistics.vue"), // Thống kê
  //   },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
