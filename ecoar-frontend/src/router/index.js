import { createRouter, createWebHistory } from 'vue-router'
import MusicasView from '../views/MusicasView.vue'
import ArtistasView from '../views/ArtistasView.vue'
import EstilosView from '../views/EstilosView.vue'
import MomentosView from '../views/MomentosView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: MusicasView
  },
  {
    path: '/novo',
    name: 'novo',
    // Usando import dinâmico para carregar apenas quando necessário
    component: () => import('../views/CadastroView.vue')
  },
  {
    path: '/editar/:id',
    name: 'editar',
    // Reutiliza o mesmo componente de cadastro para edição
    component: () => import('../views/CadastroView.vue'),
    props: true // Permite que o componente receba o ID como uma prop
  },
  {
    path: '/artistas',
    name: 'artistas',
    component: ArtistasView
  },
  {
    path: '/estilos',
    name: 'estilos',
    component: EstilosView
  },
  {
    path: '/momentos',
    name: 'momentos',
    component: MomentosView
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router