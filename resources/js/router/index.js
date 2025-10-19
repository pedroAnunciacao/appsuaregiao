import { createRouter, createWebHistory } from 'vue-router'
import AuthLogin from '../AuthLogin.vue'
import AuthRegister from '../AuthRegister.vue'
import AdminDashboard from '../AdminDashboard.vue'

const routes = [
  { path: '/', redirect: '/login' },  // redireciona raiz para /login
  { path: '/login', component: AuthLogin },
  { path: '/register', component: AuthRegister },
  { path: '/admindashboard', name: 'AdminDashboard', component: AdminDashboard }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router