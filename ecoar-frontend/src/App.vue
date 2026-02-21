<template>
  <nav class="navbar navbar-dark mb-4 shadow-sm position-relative" style="background-color: #233B3B;">
    <div class="container d-flex align-items-center justify-content-between">
      
      <div class="navbar-brand-centered">
        <router-link class="navbar-brand d-flex align-items-center m-0" :to="{ path: '/', query: route.query }">
          <img 
            src="@/assets/logo-ecoar.jpeg" 
            alt="Ecoar Logo" 
            class="logo-navbar"
          >
        </router-link>
      </div>
      
      <div class="menu-spacer"></div>

      <button 
        v-if="isAdmin"
        class="navbar-toggler border-0 shadow-none" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#menuPrincipal"
        aria-controls="menuPrincipal"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div v-if="isAdmin" class="collapse navbar-collapse w-100" id="menuPrincipal">
        <div class="navbar-nav pt-2 border-top border-secondary mt-2">
          <router-link class="nav-link text-white" :to="{ path: '/', query: route.query }" @click="fecharMenu">
            <i class="bi bi-music-note-list me-2"></i>Gerenciar Músicas
          </router-link>
          
          <router-link class="nav-link text-white" :to="{ path: '/artistas', query: route.query }" @click="fecharMenu">
            <i class="bi bi-person-badge me-2"></i>Gerenciar Artista / Referência
          </router-link>
          
          <router-link class="nav-link text-white" :to="{ path: '/estilos', query: route.query }" @click="fecharMenu">
            <i class="bi bi-tags me-2"></i>Gerenciar Estilos
          </router-link>
          
          <router-link class="nav-link text-white" :to="{ path: '/momentos', query: route.query }" @click="fecharMenu">
            <i class="bi bi-calendar-event me-2"></i>Gerenciar Momentos
          </router-link>
        </div>
      </div>
    </div>
  </nav>

  <router-view />
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const TOKEN_MESTRE = import.meta.env.VITE_TOKEN_ADMIN;

// Verifica se o token na URL é o correto
const isAdmin = computed(() => route.query.token === TOKEN_MESTRE);

const fecharMenu = () => {
  const menu = document.getElementById('menuPrincipal');
  if (menu && menu.classList.contains('show')) {
    const bootstrap = window.bootstrap;
    if (bootstrap) {
      const bsCollapse = bootstrap.Collapse.getInstance(menu);
      if (bsCollapse) bsCollapse.hide();
    } else {
      menu.classList.remove('show');
    }
  }
}
</script>

<style scoped>
/* CONFIGURAÇÕES DA NAVBAR */
.navbar {
  padding-top: 10px;
  padding-bottom: 10px;
  min-height: 110px;
  border-bottom: 1px solid rgba(197, 141, 43, 0.1);
  transition: all 0.3s ease;
}

/* LOGO CENTRALIZADA */
.navbar-brand-centered {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  z-index: 10;
  transition: opacity 0.3s ease;
}

.logo-navbar {
  height: 85px; 
  width: auto;         
  object-fit: contain; 
  transition: transform 0.3s ease, filter 0.3s ease;
  border-radius: 8px;
}

.logo-navbar:hover {
  transform: scale(1.05);
  filter: drop-shadow(0 0 10px rgba(197, 141, 43, 0.4));
}

/* LINKS DO MENU */
.nav-link {
  transition: all 0.3s ease;
  padding: 12px 15px;
  display: flex;
  align-items: center;
  font-size: 1rem;
  color: white !important; /* Cor padrão */
}

.nav-link i {
  color: #c58d2b; 
  font-size: 1.2rem;
  transition: color 0.3s ease;
}

/* HOVER - Efeito de empurrar para o lado e mudar cor */
.nav-link:hover {
  padding-left: 25px !important;
  color: #c58d2b !important;
}

/* ESTILO PARA PÁGINA ATIVA (Vue Router) */
/* Usamos tanto a classe exata quanto a parcial para garantir o destaque */
.router-link-active, .router-link-exact-active {
  color: #c58d2b !important;
  font-weight: bold !important;
  background-color: rgba(197, 141, 43, 0.05);
}

.menu-spacer {
  width: 45px;
}

/* MENU COLLAPSE */
.navbar-collapse {
  background-color: #233B3B;
  border-radius: 0 0 8px 8px;
}

/* RESPONSIVIDADE */
@media (max-width: 991px) {
  /* Esconde logo ao abrir menu no mobile */
  .navbar:has(.navbar-collapse.show) .navbar-brand-centered {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
  }
}

@media (max-width: 576px) {
  .logo-navbar {
    height: 60px;
  }
  .navbar {
    min-height: 85px;
  }
}

.navbar-toggler {
  z-index: 20;
}
</style>