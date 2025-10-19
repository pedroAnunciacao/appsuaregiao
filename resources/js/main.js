import { createApp } from 'vue'
import App from './App.vue'
import router from './router/index.js'
import './style.css'; // ajusta o caminho conforme necessário

const app = createApp(App)
app.use(router)
console.log(router.getRoutes())
app.mount('#app')
