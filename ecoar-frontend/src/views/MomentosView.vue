<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="d-flex align-items-center mb-4">
          <button @click="$router.push({ path: '/', query: $route.query })" class="btn btn-outline-secondary btn-sm me-3 border-0">
            <i class="bi bi-arrow-left-circle fs-4"></i>
          </button>
          <h2 class="ecoar-title mb-0">⏳ Gerenciar <span class="text-gold">Momentos</span></h2>
        </div>

        <div class="card shadow-sm border-0 mb-4 ecoar-dark-card">
          <div class="card-body p-4">
            <label class="form-label text-white-50 small fw-bold">NOVO MOMENTO DA CERIMÔNIA</label>
            <div class="input-group">
              <input 
                v-model="novoMomento" 
                type="text" 
                class="form-control custom-input-dark" 
                placeholder="Ex: Entrada dos Pais, Assinaturas..."
                @keyup.enter="adicionarMomento"
              >
              <button @click="adicionarMomento" class="btn btn-gold px-4">
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
                  <th class="ps-4">DESCRIÇÃO</th>
                  <th class="text-end pe-4">AÇÕES</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="momento in momentos" :key="momento.id" class="momento-row">
                  <td class="ps-4">
                    <div v-if="idEmEdicao === momento.id" class="d-flex gap-2 align-items-center">
                      <input 
                        v-model="momentoEdicao.descricao" 
                        type="text" 
                        class="form-control form-control-sm border-gold shadow-none"
                        @keyup.enter="salvarEdicao(momento.id)"
                        @keyup.esc="cancelarEdicao"
                        autoFocus
                      >
                      <button @click="salvarEdicao(momento.id)" class="btn btn-sm btn-success">
                        <i class="bi bi-check-lg"></i>
                      </button>
                      <button @click="cancelarEdicao" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </div>

                    <div v-else @click="ativarEdicao(momento)" class="cursor-pointer py-1 edit-zone" title="Clique para editar">
                      <span class="fw-bold text-dark">{{ momento.descricao }}</span>
                      <span v-if="momento.musicas_count > 0" class="ms-2 badge bg-light text-muted border fw-normal">
                        {{ momento.musicas_count }} música(s)
                      </span>
                      <i class="bi bi-pencil ms-2 text-muted small edit-hint"></i>
                    </div>
                  </td>
                  
                  <td class="text-end pe-4">
                    <button 
                      v-if="idEmEdicao !== momento.id"
                      @click="excluirMomento(momento)" 
                      class="btn btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                      :class="momento.musicas_count > 0 ? 'btn-secondary opacity-50' : 'btn-danger'"
                      :disabled="momento.musicas_count > 0"
                      style="width: 32px; height: 32px; border: none;"
                    >
                      <i v-if="momento.musicas_count > 0" class="bi bi-lock-fill text-white"></i>
                      <i v-else class="bi bi-trash text-white"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="momentos.length === 0">
                  <td colspan="2" class="text-center py-4 text-muted small">
                    Nenhum momento cadastrado.
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
import api from '../services/api';

const momentos = ref([]);
const novoMomento = ref('');

// ESTADOS PARA EDIÇÃO
const idEmEdicao = ref(null);
const momentoEdicao = ref({ descricao: '' });

const carregarMomentos = async () => {
  try {
    const response = await api.get('/momentos');
    momentos.value = response.data;
  } catch (error) {
    console.error("Erro ao carregar momentos:", error);
  }
};

const adicionarMomento = async () => {
  if (!novoMomento.value.trim()) return;
  try {
    await api.post('/momentos', { descricao: novoMomento.value });
    novoMomento.value = '';
    carregarMomentos();
  } catch (error) {
    alert("Erro ao salvar momento.");
  }
};

const ativarEdicao = (momento) => {
  idEmEdicao.value = momento.id;
  momentoEdicao.value = { ...momento };
};

const cancelarEdicao = () => {
  idEmEdicao.value = null;
  momentoEdicao.value = { descricao: '' };
};

const salvarEdicao = async (id) => {
  if (!momentoEdicao.value.descricao.trim()) return;
  try {
    await api.put(`/momentos/${id}`, { descricao: momentoEdicao.value.descricao });
    idEmEdicao.value = null;
    carregarMomentos();
  } catch (error) {
    alert("Erro ao atualizar momento.");
  }
};

const excluirMomento = async (momento) => {
  if (momento.musicas_count > 0) return;
  if (!confirm(`Excluir momento "${momento.descricao}"?`)) return;
  try {
    await api.delete(`/momentos/${momento.id}`);
    carregarMomentos();
  } catch (error) {
    alert("Erro ao excluir.");
  }
};

onMounted(carregarMomentos);
</script>

<style scoped>
.ecoar-dark-card { background-color: #1a302e; border-radius: 12px; }
.custom-input-dark { background-color: rgba(255, 255, 255, 0.05); border: 1px solid #c58d2b; color: white; }
.custom-input-dark:focus { background-color: white; color: #1a302e; }
.ecoar-header { background-color: #1a302e; }
.text-gold { color: #c58d2b !important; }
.border-gold { border-color: #c58d2b !important; }
.btn-gold { background-color: #c58d2b; color: white; border: none; }
.cursor-pointer { cursor: pointer; }
.edit-zone:hover .edit-hint { opacity: 1 !important; }
.edit-hint { opacity: 0; transition: opacity 0.2s; }
.momento-row:hover { background-color: rgba(197, 141, 43, 0.05); }
</style>