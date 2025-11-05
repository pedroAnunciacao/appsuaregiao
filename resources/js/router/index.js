import { createRouter, createWebHistory } from "vue-router"
import AuthLogin from "../AuthLogin.vue"
import AuthRegister from "../AuthRegister.vue"
import AdminDashboard from "../AdminDashboard.vue"
import Dashboard from "../Dashboard.vue"

const routes = [
  { path: "/", redirect: "/dashboard" },
  { path: "/login", component: AuthLogin },
  { path: "/register", component: AuthRegister },
  { path: "/dashboard", name: "Dashboard", component: Dashboard, meta: { requiresAuth: true } },
  { path: "/admindashboard", name: "AdminDashboard", component: AdminDashboard, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token")

  console.log("[v0] Router - Navegando de:", from.path, "para:", to.path)
  console.log("[v0] Router - Token presente:", token ? "Sim" : "Não")
  console.log("[v0] Router - Requer autenticação:", to.meta.requiresAuth)

  if (to.meta.requiresAuth && !token) {
    console.log("[v0] Router - Redirecionando para login (sem token)")
    next("/login")
  } else if ((to.path === "/login" || to.path === "/register") && token) {
    console.log("[v0] Router - Redirecionando para dashboard (já autenticado)")
    next("/dashboard")
  } else {
    console.log("[v0] Router - Permitindo navegação")
    next()
  }
})

export default router
