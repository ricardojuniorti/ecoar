<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="ecoar-title">🎵 Catálogo</h2>
      <router-link 
        v-if="isAdmin" 
        :to="{ path: '/novo', query: route.query }" 
        class="btn btn-gold shadow-sm"
      >
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
              selectLabel="Adicionar"
              deselectLabel="Remover"
              selectedLabel="Selecionado"
            />
          </div>

          <div class="col-md-4">
            <label class="small text-white-50 mb-1 fw-bold">ESTILOS</label>
            <VueMultiselect 
              v-model="filtros.estilos" 
              :options="listaEstilos" 
              :multiple="true" 
              label="descricao" 
              track-by="id" 
              placeholder="Selecionar estilos..."
              selectLabel="Adicionar"
              deselectLabel="Remover"
              selectedLabel="Selecionado"
            />
          </div>

          <div class="col-md-4">
            <label class="small text-white-50 mb-1 fw-bold">MOMENTOS</label>
            <div class="d-flex gap-2 align-items-center">
              <div class="flex-grow-1">
                <VueMultiselect 
                  v-model="filtros.momentos" 
                  :options="listaMomentos" 
                  :multiple="true" 
                  label="descricao" 
                  track-by="id" 
                  placeholder="Selecionar momentos..."
                  selectLabel="Adicionar"
                  deselectLabel="Remover"
                  selectedLabel="Selecionado"
                />
              </div>
              <button v-if="temFiltroAtivo" @click="limparFiltros" class="btn-icon-only text-gold h-38 ms-2" title="Limpar Filtros">
                <i class="bi bi-eraser-fill"></i> 
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow border-0 overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="ecoar-header text-white">
            <tr>
              <th class="ps-4">NOME DA MÚSICA</th>
              <th>ARTISTA / REFERÊNCIA</th>
              <th class="text-center">
                <span class="d-none d-md-inline">OUVIR</span>
                <i class="bi bi-headphones d-md-none text-gold"></i>
              </th>
              <th v-if="isAdmin" class="text-end pe-4">AÇÕES</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="musica in musicasPaginadas" :key="musica.id">
              <td class="ps-4 fw-bold">
                <a href="#" @click.prevent="abrirDetalhes(musica)" class="text-dark music-link">
                  {{ musica.titulo }}
                </a>
              </td>
              <td>{{ musica.artista?.nome || '---' }}</td>
              <td class="text-center">
                <a v-if="musica.link" :href="musica.link" target="_blank" class="btn btn-sm btn-outline-gold rounded-pill px-3">
                  <i class="bi bi-headphones text-gold"></i>
                </a>
                <span v-else class="text-muted small">---</span>
              </td>
              <td v-if="isAdmin" class="text-end pe-4">
                <div class="d-flex justify-content-end gap-3">
                  <button @click="editarMusica(musica.id)" class="btn-icon-only text-primary" title="Editar">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button @click="excluirMusica(musica)" class="btn-icon-only text-danger" title="Excluir">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="musicasFiltradas.length === 0">
              <td :colspan="isAdmin ? 4 : 3" class="text-center py-4 text-muted">
                Nenhuma música encontrada com os filtros aplicados.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer bg-white border-0 py-3" v-if="totalPaginas > 1">
        <div class="d-flex justify-content-between align-items-center">
          <div class="small text-muted d-none d-md-block">
            Mostrando <b>{{ musicasPaginadas.length }}</b> de <b>{{ musicasFiltradas.length }}</b> registros
          </div>
          <nav>
            <ul class="pagination pagination-sm mb-0">
              <li class="page-item" :class="{ disabled: paginaAtual === 1 }">
                <button class="page-link border-gold text-gold shadow-none" @click="paginaAtual--">Anterior</button>
              </li>
              <li v-for="pagina in totalPaginas" :key="pagina" class="page-item" :class="{ active: paginaAtual === pagina }">
                <button class="page-link border-gold shadow-none" :class="paginaAtual === pagina ? 'bg-gold text-white' : 'text-gold'" @click="paginaAtual = pagina">{{ pagina }}</button>
              </li>
              <li class="page-item" :class="{ disabled: paginaAtual === totalPaginas }">
                <button class="page-link border-gold text-gold shadow-none" @click="paginaAtual++">Próxima</button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalDetalhes" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
          <div v-if="getYoutubeThumbnail(musicaSelecionada?.link)" class="ratio ratio-16x9 border-bottom border-gold border-3">
            <img :src="getYoutubeThumbnail(musicaSelecionada.link)" class="img-fluid object-fit-cover" alt="Capa">
          </div>
          <div class="ecoar-header p-4 text-white position-relative">
            <h4 class="mb-0 text-gold">{{ musicaSelecionada?.titulo }}</h4>
            <p class="mb-0 small opacity-75">{{ musicaSelecionada?.artista?.nome || 'Artista não definido' }}</p>
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 shadow-none" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 bg-white">
            <div class="mb-4">
              <label class="fw-bold text-muted small d-block mb-2 text-uppercase">Estilos / Vibe</label>
              <div class="d-flex flex-wrap gap-2">
                <span v-for="estilo in musicaSelecionada?.estilos" :key="estilo.id" class="badge ecoar-badge px-3 py-2">
                  {{ estilo.descricao }}
                </span>
                <span v-if="!musicaSelecionada?.estilos?.length" class="text-muted small">Nenhum estilo definido</span>
              </div>
            </div>
            <div class="mb-4">
              <label class="fw-bold text-muted small d-block mb-2 text-uppercase">Momentos Sugeridos</label>
              <div class="d-flex flex-wrap gap-2">
                <span v-for="momento in musicaSelecionada?.momentos" :key="momento.id" class="badge bg-light text-dark border px-3 py-2">
                  {{ momento.descricao }}
                </span>
                <span v-if="!musicaSelecionada?.momentos?.length" class="text-muted small">Nenhum momento sugerido</span>
              </div>
            </div>
            <div v-if="musicaSelecionada?.link" class="d-grid mt-4">
              <a :href="musicaSelecionada.link" target="_blank" class="btn btn-gold py-3 shadow-sm fw-bold">
                <i class="bi bi-play-circle-fill me-2"></i> Abrir Referência Musical
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

// CONFIGURAÇÃO DE SEGURANÇA
const TOKEN_MESTRE = import.meta.env.VITE_TOKEN_ADMIN;
const isAdmin = computed(() => route.query.token === TOKEN_MESTRE);

const musicas = ref([]);
const artistas = ref([]);
const listaEstilos = ref([]);
const listaMomentos = ref([]);

const filtros = ref({
  texto: '',
  artistas: [],
  estilos: [],
  momentos: []
});

const musicaSelecionada = ref(null);
const paginaAtual = ref(1);
const itensPorPagina = ref(10);
const opcoesPagina = [10, 20, 50, 100];

const getYoutubeThumbnail = (url) => {
  if (!url) return null;
  const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
  const match = url.match(regExp);
  return (match && match[2].length === 11) ? `https://img.youtube.com/vi/${match[2]}/hqdefault.jpg` : null;
};

const carregarDados = async () => {
  try {
    const [resMusicas, resArtistas, resEstilos, resMomentos] = await Promise.all([
      api.get('/musicas'),
      api.get('/artistas'),
      api.get('/estilos'),
      api.get('/momentos')
    ]);
    musicas.value = resMusicas.data;
    artistas.value = resArtistas.data;
    listaEstilos.value = resEstilos.data;
    listaMomentos.value = resMomentos.data;
  } catch (error) {
    console.error("Erro ao carregar dados:", error);
  }
};

const abrirDetalhes = (musica) => {
  musicaSelecionada.value = musica;
  const modalEl = document.getElementById('modalDetalhes');
  let myModal = Modal.getInstance(modalEl);
  if (!myModal) myModal = new Modal(modalEl, { focus: true });
  myModal.show();
};

const excluirMusica = async (musica) => {
  if (confirm(`Deseja realmente excluir a música "${musica.titulo}"?`)) {
    try {
      await api.delete(`/musicas/${musica.id}`);
      carregarDados();
    } catch (error) {
      alert("Erro ao excluir música.");
    }
  }
};

// Navegação para editar mantendo o token na URL
const editarMusica = (id) => {
  router.push({ path: `/editar/${id}`, query: route.query });
};

const musicasFiltradas = computed(() => {
  return musicas.value.filter(m => {
    const bateTexto = m.titulo.toLowerCase().includes(filtros.value.texto.toLowerCase()) ||
                     (m.artista?.nome || '').toLowerCase().includes(filtros.value.texto.toLowerCase());
    const bateArtista = filtros.value.artistas.length === 0 || 
                       filtros.value.artistas.some(fArt => m.artista_id === fArt.id);
    const bateEstilos = filtros.value.estilos.length === 0 || 
      filtros.value.estilos.every(f => m.estilos.some(e => e.id === f.id));
    const bateMomentos = filtros.value.momentos.length === 0 || 
      filtros.value.momentos.every(f => m.momentos.some(mom => mom.id === f.id));
    return bateTexto && bateArtista && bateEstilos && bateMomentos;
  });
});

const temFiltroAtivo = computed(() => {
  return filtros.value.texto || filtros.value.artistas.length > 0 || filtros.value.estilos.length > 0 || filtros.value.momentos.length > 0;
});

const limparFiltros = () => {
  filtros.value = { texto: '', artistas: [], estilos: [], momentos: [] };
};

const totalPaginas = computed(() => Math.ceil(musicasFiltradas.value.length / itensPorPagina.value));
const musicasPaginadas = computed(() => {
  const inicio = (paginaAtual.value - 1) * itensPorPagina.value;
  return musicasFiltradas.value.slice(inicio, inicio + itensPorPagina.value);
});

watch([filtros, itensPorPagina], () => paginaAtual.value = 1, { deep: true });
onMounted(carregarDados);
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
.ecoar-search-bg { background-color: #1a302e; border-radius: 12px; }
.border-gold { border-color: #c58d2b !important; }
.ecoar-header { background-color: #1a302e; }
.bg-gold { background-color: #c58d2b !important; }
.text-gold { color: #c58d2b !important; }
.custom-input { background-color: rgba(255, 255, 255, 0.05); color: white; }
.custom-input::placeholder { color: #999 !important; opacity: 1; }
.custom-input:focus { background-color: white; color: #1a302e; box-shadow: 0 0 0 0.25rem rgba(197, 141, 43, 0.25); }
.custom-select-dark { background-color: rgba(255, 255, 255, 0.05); color: white; border-radius: 6px; }
.custom-select-dark option { background-color: #ffffff !important; color: #1a302e !important; }
.h-38 { height: 38px !important; }
hr.border-gold { border-top: 1px solid #c58d2b; }
.music-link { text-decoration: none; border-bottom: 1px solid transparent; transition: all 0.3s; }
.music-link:hover { color: #c58d2b !important; border-bottom: 1px solid #c58d2b; }
.ecoar-badge { background-color: #1a302e; color: #c58d2b; border: 1px solid #c58d2b; font-weight: 500; }
.btn-icon-only { background: none; border: none; padding: 0; cursor: pointer; transition: transform 0.2s ease, opacity 0.2s; display: flex; align-items: center; justify-content: center; }
.btn-icon-only i { font-size: 1.25rem; }
.btn-icon-only:hover { transform: scale(1.1); opacity: 0.8; }
.text-primary { color: #0d6efd !important; }
.text-danger { color: #dc3545 !important; }

:deep(.multiselect__tags) { background: rgba(255, 255, 255, 0.05) !important; border: 1px solid #c58d2b !important; min-height: 38px !important; padding: 4px 40px 0 8px !important; }
:deep(.multiselect__single), :deep(.multiselect__input) { background: transparent !important; color: white !important; }
:deep(.multiselect__tag) { background: #c58d2b !important; color: white !important; }
:deep(.multiselect__tag-icon:after) { color: white !important; }
:deep(.multiselect__tag-icon:hover) { background: #a87724 !important; }
:deep(.multiselect__placeholder) { color: #999 !important; margin-bottom: 5px !important; padding-top: 2px !important; }
:deep(.multiselect__select) { height: 36px !important; }
:deep(.multiselect__option--highlight) { background: #c58d2b !important; color: white !important; }
:deep(.multiselect__option--selected.multiselect__option--highlight) { background: #a87724 !important; color: white !important; }
:deep(.multiselect__option--highlight::after) { background: #c58d2b !important; }
.pagination .page-link { background-color: transparent; transition: all 0.3s; }
.page-item.disabled .page-link { color: #ccc !important; }
.modal-content { border-radius: 20px; }
.object-fit-cover { object-fit: cover; }
.ratio-16x9 { overflow: hidden; }

.text-gold {
  color: #c58d2b !important;
}

.ecoar-header i.bi-headphones {
  font-size: 1.2rem;
  vertical-align: middle;
}
</style>