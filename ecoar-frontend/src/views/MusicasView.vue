<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="ecoar-title d-flex align-items-center m-0">
        <img src="@/assets/img/violino.png" alt="Violino" class="title-icon me-2">
        CATÁLOGO
      </h3>
      <router-link 
        v-if="isAdmin" 
        :to="{ path: '/novo', query: route.query }" 
        class="btn btn-gold shadow-sm">
        <i class="bi bi-plus-lg"></i> Adicionar Música
      </router-link>
    </div>

    <div class="card shadow-sm border-0 mb-4 ecoar-search-bg">
      <div class="card-body p-3">
        <div class="row align-items-center">
          <div class="col-md-9">
            <div class="input-group">
              <span class="input-group-text bg-transparent border-gold text-gold">
                <i class="bi bi-search"></i>
              </span>
              <input 
                v-model="filtros.texto" 
                type="text" 
                class="form-control border-gold custom-input" 
                placeholder="Buscar por música ou artista..."
              >
            </div>
          </div>
          <div class="col-md-3">
            <div class="d-flex align-items-center justify-content-end gap-2 mt-2 mt-md-0">
              <span class="small text-white-50 text-nowrap">Exibir:</span>
              <select v-model="itensPorPagina" class="form-select form-select-sm border-gold bg-transparent text-white w-auto shadow-none custom-select-dark h-38">
                <option v-for="qtd in opcoesPagina" :key="qtd" :value="qtd" class="text-dark">{{ qtd }}</option>
              </select>
            </div>
          </div>
        </div>

        <hr class="border-gold opacity-25 my-3">

        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="small text-white-50 mb-1 fw-bold">ARTISTAS</label>
            <VueMultiselect 
              v-model="filtros.artistas" 
              :options="artistas" 
              :multiple="true" 
              label="nome" 
              track-by="id" 
              placeholder="Selecionar artistas..."
              select-label="Adicionar"
              deselect-label="Remover"
              selected-label="Selecionado"
            />
          </div>
          <div class="col-md-4">
            <label class="small text-white-50 mb-1 fw-bold">ESTILO/VIBE</label>
            <VueMultiselect 
              v-model="filtros.estilos" 
              :options="listaEstilos" 
              :multiple="true" 
              label="descricao" 
              track-by="id" 
              placeholder="Selecionar estilos..."
              select-label="Adicionar"
              deselect-label="Remover"
              selected-label="Selecionado"
            />
          </div>
          <div class="col-md-4">
            <label class="small text-white-50 mb-1 fw-bold">MOMENTOS SUGERIDOS</label>
            <div class="d-flex gap-2 align-items-center">
              <div class="flex-grow-1">
                <VueMultiselect 
                  v-model="filtros.momentos" 
                  :options="listaMomentos" 
                  :multiple="true" 
                  label="descricao" 
                  track-by="id" 
                  placeholder="Selecionar momentos..."
                  select-label="Adicionar"
                  deselect-label="Remover"
                  selected-label="Selecionado"
                />
              </div>
              <button v-if="temFiltroAtivo" @click="limparFiltros" class="btn-icon-only text-gold h-38 ms-2">
                <i class="bi bi-eraser-fill"></i> 
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="carregando" class="text-center py-5 my-5">
      <div class="spinner-border text-gold" role="status" style="width: 3.5rem; height: 3.5rem;">
        <span class="visually-hidden">Carregando...</span>
      </div>
      <p class="text-gold mt-3 fw-bold animate-pulse">Sintonizando o catálogo...</p>
    </div>

    <div v-else class="card shadow border-0 overflow-hidden animate-fade-in">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="ecoar-header text-white">
            <tr>
              <th class="ps-4 cursor-pointer" @click="ordenar('titulo')">
                NOME DA MÚSICA <i class="bi ms-1" :class="getIconeOrdenacao('titulo')"></i>
              </th>
              <th>
                ARTISTA / REFERÊNCIA
              </th>
              <th class="text-center">OUVIR</th>
              <th v-if="isAdmin" class="text-end pe-4">AÇÕES</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="musica in musicasPaginadas" :key="musica.id">
              <td class="ps-4 fw-bold">
                <a href="#" @click.prevent="abrirDetalhes(musica)" class="text-dark music-link">{{ musica.titulo }}</a>
              </td>
              <td>{{ musica.artista?.nome || '---' }}</td>
              <td class="text-center">
                <button @click="abrirDetalhes(musica)" class="btn btn-sm btn-outline-gold rounded-pill px-3">
                  <i class="bi bi-headphones text-gold"></i>
                </button>
              </td>
              <td v-if="isAdmin" class="text-end pe-4">
                <div class="d-flex justify-content-end gap-3">
                  <button @click="editarMusica(musica.id)" class="btn-icon-only text-primary"><i class="bi bi-pencil-square"></i></button>
                  <button @click="excluirMusica(musica)" class="btn-icon-only text-danger"><i class="bi bi-trash"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal fade" id="modalDetalhes" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
          
          <div v-if="musicaSelecionada && getVideoId(musicaSelecionada.link)" class="ratio ratio-16x9">
            <iframe 
              :src="`https://www.youtube.com/embed/${getVideoId(musicaSelecionada.link)}?rel=0&modestbranding=1`" 
              frameborder="0" 
              allowfullscreen>
            </iframe>
          </div>
          
          <div class="ecoar-header p-4 text-white position-relative">
            <h4 class="mb-0 text-gold">{{ musicaSelecionada?.titulo }}</h4>
            <p class="mb-0 small opacity-75">{{ musicaSelecionada?.artista?.nome || '---' }}</p>
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 shadow-none" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body p-4 bg-white">
            <div class="mb-3">
               <label class="fw-bold text-muted small d-block mb-2 text-uppercase">Estilos / Vibe</label>
               <div class="d-flex flex-wrap gap-2">
                 <span v-for="estilo in musicaSelecionada?.estilos" :key="estilo.id" class="badge ecoar-badge px-3 py-2">
                   {{ estilo.descricao }}
                 </span>
               </div>
            </div>
            <div class="mb-4">
               <label class="fw-bold text-muted small d-block mb-2 text-uppercase">Momentos Sugeridos</label>
               <div class="d-flex flex-wrap gap-2">
                 <span v-for="momento in musicaSelecionada?.momentos" :key="momento.id" class="badge bg-light text-dark border px-3 py-2">
                   {{ momento.descricao }}
                 </span>
               </div>
            </div>
            <div v-if="musicaSelecionada?.link" class="d-grid mt-2">
              <a :href="musicaSelecionada.link" target="_blank" class="btn btn-gold py-2 shadow-sm fw-bold">
                <i class="bi bi-box-arrow-up-right me-2"></i> Abrir link original no YouTube
              </a>
            </div>
          </div>
          <div class="modal-footer bg-light border-0">
             <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../services/api';
import { Modal } from 'bootstrap';
import VueMultiselect from 'vue-multiselect';

const router = useRouter();
const route = useRoute();
const TOKEN_MESTRE = import.meta.env.VITE_TOKEN_ADMIN;
const isAdmin = computed(() => route.query.token === TOKEN_MESTRE);

const musicas = ref([]);
const artistas = ref([]);
const listaEstilos = ref([]);
const listaMomentos = ref([]);
const carregando = ref(false);
const filtros = ref({ texto: '', artistas: [], estilos: [], momentos: [] });
const paginaAtual = ref(1);
const itensPorPagina = ref(30);
const opcoesPagina = [30, 50, 100];
const colunaOrdenacao = ref('titulo');
const ordemAscendente = ref(true);
const musicaSelecionada = ref(null);

onMounted(() => {
  carregarDados();
  const modalEl = document.getElementById('modalDetalhes');
  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', () => { musicaSelecionada.value = null; });
  }
});

const carregarDados = async () => {
  carregando.value = true;
  try {
    const [resMusicas, resArtistas, resEstilos, resMomentos] = await Promise.all([
      api.get('/musicas'), api.get('/artistas'), api.get('/estilos'), api.get('/momentos')
    ]);
    musicas.value = resMusicas.data;
    artistas.value = resArtistas.data;
    listaEstilos.value = resEstilos.data;
    listaMomentos.value = resMomentos.data;
  } catch (error) { console.error(error); }
  finally { setTimeout(() => { carregando.value = false; }, 400); }
};

const ordenar = (col) => {
  if (colunaOrdenacao.value === col) ordemAscendente.value = !ordemAscendente.value;
  else { colunaOrdenacao.value = col; ordemAscendente.value = true; }
};

const getIconeOrdenacao = (col) => {
  if (colunaOrdenacao.value !== col) return 'bi-arrow-down-up text-muted opacity-50';
  return ordemAscendente.value ? 'bi-sort-alpha-down text-gold' : 'bi-sort-alpha-up-alt text-gold';
};

const musicasFiltradas = computed(() => {
  let res = musicas.value.filter(m => {
    const txt = filtros.value.texto.toLowerCase();
    return (m.titulo.toLowerCase().includes(txt) || (m.artista?.nome || '').toLowerCase().includes(txt)) &&
           (!filtros.value.artistas.length || filtros.value.artistas.some(f => m.artista_id === f.id)) &&
           (!filtros.value.estilos.length || filtros.value.estilos.every(f => m.estilos.some(e => e.id === f.id))) &&
           (!filtros.value.momentos.length || filtros.value.momentos.every(f => m.momentos.some(mo => mo.id === f.id)));
  });
  return res.sort((a, b) => {
    let vA = colunaOrdenacao.value === 'artista' ? (a.artista?.nome || '') : a[colunaOrdenacao.value];
    let vB = colunaOrdenacao.value === 'artista' ? (b.artista?.nome || '') : b[colunaOrdenacao.value];
    return ordemAscendente.value ? (vA || '').localeCompare(vB || '') : (vB || '').localeCompare(vA || '');
  });
});

const musicasPaginadas = computed(() => {
  const i = (paginaAtual.value - 1) * itensPorPagina.value;
  return musicasFiltradas.value.slice(i, i + itensPorPagina.value);
});

const totalPaginas = computed(() => Math.ceil(musicasFiltradas.value.length / itensPorPagina.value));
const temFiltroAtivo = computed(() => filtros.value.texto || filtros.value.artistas.length || filtros.value.estilos.length || filtros.value.momentos.length);
const limparFiltros = () => { filtros.value = { texto: '', artistas: [], estilos: [], momentos: [] }; };
const getVideoId = (url) => { if (!url) return null; const m = url.match(/^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/); return m?.[2]; };
const abrirDetalhes = (m) => { musicaSelecionada.value = m; Modal.getOrCreateInstance(document.getElementById('modalDetalhes')).show(); };
const editarMusica = (id) => router.push({ path: `/editar/${id}`, query: route.query });
const excluirMusica = async (m) => { if (confirm(`Excluir ${m.titulo}?`)) { await api.delete(`/musicas/${m.id}`); carregarDados(); } };

watch([filtros, itensPorPagina, colunaOrdenacao, ordemAscendente], () => { paginaAtual.value = 1; }, { deep: true });
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
/* --- PADRÃO ECOAR --- */
.ecoar-title { color: #41403f; font-weight: bold; letter-spacing: 1px; display: flex; align-items: center; }
.title-icon { height: 60px; width: auto; object-fit: contain; }
.ecoar-search-bg { background-color: #1a302e; border-radius: 12px; }
.border-gold { border-color: #c58d2b !important; }
.ecoar-header { background-color: #1a302e; }
.text-gold { color: #c58d2b !important; }
.cursor-pointer { cursor: pointer; user-select: none; }

/* --- INPUT DE BUSCA --- */
.custom-input { background-color: rgba(255, 255, 255, 0.05); color: white; }
.custom-input::placeholder { color: #999 !important; opacity: 1; }
.custom-input:focus { background-color: white; color: #1a302e; }

/* --- FILTROS (VUE-MULTISELECT) --- */
:deep(.multiselect__tags) { background: rgba(255, 255, 255, 0.05) !important; border: 1px solid #c58d2b !important; color: white !important; }
:deep(.multiselect__single), :deep(.multiselect__input) { background: transparent !important; color: white !important; }
:deep(.multiselect__placeholder) { color: #999 !important; }
:deep(.multiselect__option--highlight) { background: #c58d2b !important; }
:deep(.multiselect__option--highlight::after) { background: #c58d2b !important; }
:deep(.multiselect__tag) { background: #c58d2b !important; }
:deep(.multiselect__content-wrapper) { background: #1a302e !important; border-color: #c58d2b !important; }
:deep(.multiselect__option) { background: #1a302e; color: white; }

/* --- BOTÕES E TABELA --- */
.btn-gold { background-color: #c58d2b !important; color: white !important; border: none; }
.btn-icon-only { border: none !important; background: transparent !important; transition: transform 0.2s; outline: none !important; box-shadow: none !important; }
.btn-icon-only:hover { transform: scale(1.15); opacity: 0.8; }
.music-link { text-decoration: none; transition: 0.3s; }
.music-link:hover { color: #c58d2b !important; }
.pagination .page-link { background-color: transparent; border-color: #c58d2b; color: #c58d2b; }
.page-item.active .page-link { background-color: #c58d2b !important; color: white !important; }

/* --- MODAL E VÍDEO (RESTAURADO) --- */
.modal-content { border-radius: 20px; border: none; background-color: #fff; }
.ratio-16x9 { 
  border-top-left-radius: 20px; border-top-right-radius: 20px; 
  overflow: hidden; background-color: #000;
}
.ratio-16x9 iframe { border: none !important; width: 100%; height: 100%; display: block; }
.ecoar-badge { background-color: #1a302e; color: #c58d2b; border: 1px solid #c58d2b; font-weight: 500; }

/* ANIMAÇÕES */
.animate-pulse { animation: pulse 1.5s infinite; }
@keyframes pulse { 0%, 100% { opacity: 0.5; } 50% { opacity: 1; } }
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; } }
</style>