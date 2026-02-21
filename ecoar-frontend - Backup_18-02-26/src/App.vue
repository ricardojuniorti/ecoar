<template>
  <nav class="navbar navbar-dark mb-4 shadow-sm" style="background-color: var(--ecoar-verde);">
    <div class="container">
      <router-link class="navbar-brand d-flex align-items-center" to="/">
        <img 
          src="@/assets/logo-ecoar.jpeg" 
          alt="Ecoar Logo" 
          class="logo-navbar"
        >
      </router-link>
      
      <button 
        class="navbar-toggler border-0" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#menuPrincipal"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="menuPrincipal">
        <div class="navbar-nav pt-2 border-top border-secondary mt-2">
          <router-link class="nav-link text-white" to="/" @click="fecharMenu">
            <i class="bi bi-music-note-list me-2"></i>Músicas
          </router-link>
          <router-link class="nav-link text-white" to="/artistas" @click="fecharMenu">
            <i class="bi bi-plus-circle me-2"></i>Artista / Referência
          </router-link>
          <router-link class="nav-link text-white" to="/estilos" @click="fecharMenu">
            <i class="bi bi-plus-circle me-2"></i>Estilos
          </router-link>
          <router-link class="nav-link text-white" to="/momentos" @click="fecharMenu">
            <i class="bi bi-plus-circle me-2"></i>Momentos
          </router-link>
        </div>
      </div>
    </div>
  </nav>

  <router-view />
</template>

<script setup>
/**
 * Função para fechar o menu sanduíche automaticamente.
 */
const fecharMenu = () => {
  const menu = document.getElementById('menuPrincipal');
  if (menu && menu.classList.contains('show')) {
    menu.classList.remove('show');
  }
}
</script>

<style scoped>
/* Estilização da Logo para manter o tamanho do menu */
.logo-navbar {
  /* Altere de 40px para 60px ou 80px conforme sua preferência */
  height: 65px; 
  
  width: auto;         /* Mantém a proporção correta */
  object-fit: contain; /* Garante que a imagem não distorça */
  transition: transform 0.3s ease;
}

/* Opcional: Ajuste o preenchimento da navbar se a logo ficar encostada nas bordas */
.navbar {
  padding-top: 10px;
  padding-bottom: 10px;
}

.nav-link {
  transition: all 0.3s;
  padding: 10px 0;
}

.nav-link:hover {
  padding-left: 10px;
  color: var(--ecoar-dourado) !important; /* Dourado da identidade visual */
}

/* Estilo para o link da página atual */
.router-link-active {
  color: var(--ecoar-dourado) !important;
  font-weight: bold;
}
</style>