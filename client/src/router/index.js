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

const routes = [
  { path: "/", component: EmployeeList },
  { path: "/create", component: EmployeeCreate },
  { path: "/edit/:id", component: EmployeeEdit },
  // Routes cho Attendance
  { path: "/attendance", name: "Attendance", component: Attendance },
  // Routes cho Department
  { path: "/departments", component: DepartmentList },
  { path: "/departments/create", component: DepartmentCreate },
  { path: "/departments/:id/edit", component: DepartmentEdit },
  // Routes cho Position
  { path: "/positions", component: PositionList },
  { path: "/positions/create", component: PositionCreate },
  { path: "/positions/:id/edit", component: PositionEdit },
  // Routes cho Task
  { path: "/tasks", component: TaskList },
  { path: "/tasks/create", component: TaskCreate },
  { path: "/tasks/:id/edit", component: TaskEdit },
  //Routes cho Statistics
  { path: "/statistics", component: Statistics},
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
