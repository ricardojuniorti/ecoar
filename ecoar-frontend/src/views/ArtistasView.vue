<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-7">
        
        <div class="d-flex align-items-center mb-4">
          <button @click="$router.push({ path: '/', query: $route.query })" class="btn btn-outline-secondary btn-sm me-3 border-0">
            <i class="bi bi-arrow-left-circle fs-4"></i>
          </button>
          <h2 class="ecoar-title mb-0">👨‍🎤 Gerenciar <span class="text-gold">Artistas</span></h2>
        </div>

        <div class="card shadow-sm border-0 mb-4 ecoar-dark-card">
          <div class="card-body p-4">
            <label class="form-label text-white-50 small fw-bold">NOVO ARTISTA / REFERÊNCIA</label>
            <div class="input-group">
              <input 
                v-model="novoArtista" 
                type="text" 
                class="form-control custom-input-dark" 
                placeholder="Nome do artista..."
                @keyup.enter="adicionarArtista"
              >
              <button @click="adicionarArtista" class="btn btn-gold px-4">
                <i class="bi bi-plus-lg text-white"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="card shadow border-0 overflow-hidden">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="ecoar-header text-white">
                <tr>
                  <th class="ps-4">NOME</th>
                  <th class="text-end pe-4">AÇÕES</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="artista in artistas" :key="artista.id" class="artista-row">
                  <td class="ps-4">
                    <div v-if="idEmEdicao === artista.id" class="d-flex gap-2 align-items-center">
                      <input 
                        v-model="artistaEdicao.nome" 
                        type="text" 
                        class="form-control form-control-sm border-gold shadow-none"
                        @keyup.enter="salvarEdicao(artista.id)"
                        @keyup.esc="cancelarEdicao"
                        autoFocus
                      >
                      <button @click="salvarEdicao(artista.id)" class="btn btn-sm btn-success">
                        <i class="bi bi-check-lg"></i>
                      </button>
                      <button @click="cancelarEdicao" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </div>

                    <div v-else @click="ativarEdicao(artista)" class="cursor-pointer py-1 edit-zone" title="Clique para editar">
                      <span class="fw-bold text-dark">{{ artista.nome }}</span>
                      <span v-if="artista.musicas_count > 0" class="ms-2 badge bg-light text-muted border fw-normal">
                        {{ artista.musicas_count }} música(s) vinculada(s)
                      </span>
                      <i class="bi bi-pencil ms-2 text-muted small edit-hint"></i>
                    </div>
                  </td>
                  
                  <td class="text-end pe-4">
                    <button 
                      v-if="idEmEdicao !== artista.id"
                      @click="excluirArtista(artista)" 
                      class="btn btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                      :class="artista.musicas_count > 0 ? 'btn-secondary opacity-50' : 'btn-danger'"
                      :disabled="artista.musicas_count > 0"
                      style="width: 32px; height: 32px; border: none;"
                    >
                      <i v-if="artista.musicas_count > 0" class="bi bi-lock-fill text-white"></i>
                      <i v-else class="bi bi-trash text-white"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="artistas.length === 0">
                  <td colspan="2" class="text-center py-4 text-muted small">
                    Nenhum artista cadastrado.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../services/api';

const router = useRouter();
const route = useRoute();

const artistas = ref([]);
const novoArtista = ref('');

// ESTADOS PARA EDIÇÃO
const idEmEdicao = ref(null);
const artistaEdicao = ref({ nome: '' });

const carregarArtistas = async () => {
  try {
    const response = await api.get('/artistas');
    artistas.value = response.data;
  } catch (error) {
    console.error("Erro ao carregar artistas:", error);
  }
};

const adicionarArtista = async () => {
  if (!novoArtista.value.trim()) return;

  try {
    const response = await api.post('/artistas', { nome: novoArtista.value });
    const novoId = response.data.id;

    // VERIFICAÇÃO INTELIGENTE (Rascunho)
    const rascunhoRaw = localStorage.getItem('rascunho_musica');
    if (rascunhoRaw) {
      const rascunho = JSON.parse(rascunhoRaw);
      if (rascunho.aguardando_artista) {
        rascunho.artista_id = novoId;
        delete rascunho.aguardando_artista;
        localStorage.setItem('rascunho_musica', JSON.stringify(rascunho));
        router.back(); 
        return;
      }
    }

    novoArtista.value = '';
    carregarArtistas();
  } catch (error) {
    alert("Erro ao salvar artista.");
  }
};

const ativarEdicao = (artista) => {
  idEmEdicao.value = artista.id;
  artistaEdicao.value = { ...artista };
};

const cancelarEdicao = () => {
  idEmEdicao.value = null;
  artistaEdicao.value = { nome: '' };
};

const salvarEdicao = async (id) => {
  if (!artistaEdicao.value.nome.trim()) return;
  try {
    await api.put(`/artistas/${id}`, { nome: artistaEdicao.value.nome });
    idEmEdicao.value = null;
    carregarArtistas();
  } catch (error) {
    alert("Erro ao atualizar artista. Verifique se o nome já existe.");
  }
};

const excluirArtista = async (artista) => {
  if (artista.musicas_count > 0) return;
  if (!confirm(`Deseja realmente excluir o artista "${artista.nome}"?`)) return;

  try {
    await api.delete(`/artistas/${artista.id}`);
    carregarArtistas();
  } catch (error) {
    alert("Erro ao excluir o artista.");
  }
};

onMounted(carregarArtistas);
</script>

<style scoped>
.ecoar-dark-card { background-color: #1a302e; border-radius: 12px; }
.custom-input-dark { background-color: rgba(255, 255, 255, 0.05); border: 1px solid #c58d2b; color: white; }
.custom-input-dark:focus { background-color: white; color: #1a302e; box-shadow: 0 0 0 0.25rem rgba(197, 141, 43, 0.25); }
.ecoar-header { background-color: #1a302e; }
.text-gold { color: #c58d2b !important; }
.border-gold { border-color: #c58d2b !important; }
.btn-gold { background-color: #c58d2b; color: white; border: none; }
.cursor-pointer { cursor: pointer; }
.edit-zone:hover .edit-hint { opacity: 1 !important; }
.edit-hint { opacity: 0; transition: opacity 0.2s; }
.artista-row:hover { background-color: rgba(197, 141, 43, 0.05); }
</style>