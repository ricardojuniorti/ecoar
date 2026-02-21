
// src/main.js
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap'
import 'vue-multiselect/dist/vue-multiselect.css'

// Importe seu CSS Global aqui (depois do Bootstrap para poder sobrescrevê-lo)
import './assets/global.css'

const app = createApp(App)
app.use(router)
app.mount('#app')