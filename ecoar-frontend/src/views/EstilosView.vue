<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="d-flex align-items-center mb-4">
          <button @click="$router.push({ path: '/', query: $route.query })" class="btn btn-outline-secondary btn-sm me-3 border-0">
            <i class="bi bi-arrow-left-circle fs-4"></i>
          </button>
          <h2 class="ecoar-title mb-0">🎸 Gerenciar <span class="text-gold">Estilos</span></h2>
        </div>

        <div class="card shadow-sm border-0 mb-4 ecoar-dark-card">
          <div class="card-body p-4">
            <label class="form-label text-white-50 small fw-bold">NOVO ESTILO / VIBE</label>
            <div class="input-group">
              <input 
                v-model="novoEstilo" 
                type="text" 
                class="form-control custom-input-dark" 
                placeholder="Ex: Pop Rock, Jazz..."
                @keyup.enter="adicionarEstilo"
              >
              <button @click="adicionarEstilo" class="btn btn-gold px-4">
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
                <tr v-for="estilo in estilos" :key="estilo.id" class="estilo-row">
                  <td class="ps-4">
                    <div v-if="idEmEdicao === estilo.id" class="d-flex gap-2 align-items-center">
                      <input 
                        v-model="estiloEdicao.descricao" 
                        type="text" 
                        class="form-control form-control-sm border-gold shadow-none"
                        @keyup.enter="salvarEdicao(estilo.id)"
                        @keyup.esc="cancelarEdicao"
                        autoFocus
                      >
                      <button @click="salvarEdicao(estilo.id)" class="btn btn-sm btn-success" title="Salvar Alterações">
                        <i class="bi bi-check-lg"></i>
                      </button>
                      <button @click="cancelarEdicao" class="btn btn-sm btn-outline-danger" title="Cancelar">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </div>

                    <div v-else @click="ativarEdicao(estilo)" class="cursor-pointer py-1 edit-zone" title="Clique para editar">
                      <span class="fw-bold text-dark">{{ estilo.descricao }}</span>
                      <span v-if="estilo.musicas_count > 0" class="ms-2 badge bg-light text-muted border fw-normal">
                        {{ estilo.musicas_count }} música(s)
                      </span>
                      <i class="bi bi-pencil ms-2 text-muted small edit-icon-hint"></i>
                    </div>
                  </td>
                  
                  <td class="text-end pe-4">
                    <button 
                      v-if="idEmEdicao !== estilo.id"
                      @click="excluirEstilo(estilo)" 
                      class="btn btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                      :class="estilo.musicas_count > 0 ? 'btn-secondary opacity-50' : 'btn-danger'"
                      :disabled="estilo.musicas_count > 0"
                      style="width: 32px; height: 32px; border: none;"
                    >
                      <i v-if="estilo.musicas_count > 0" class="bi bi-lock-fill text-white"></i>
                      <i v-else class="bi bi-trash text-white"></i>
                    </button>
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

const estilos = ref([]);
const novoEstilo = ref('');

// ESTADOS PARA EDIÇÃO INLINE
const idEmEdicao = ref(null); // Armazena o ID do estilo sendo editado
const estiloEdicao = ref({ descricao: '' }); // Cópia para não alterar o original antes de salvar

const carregarEstilos = async () => {
  try {
    const response = await api.get('/estilos');
    estilos.value = response.data;
  } catch (error) {
    console.error("Erro ao carregar estilos:", error);
  }
};

const adicionarEstilo = async () => {
  if (!novoEstilo.value.trim()) return;
  try {
    await api.post('/estilos', { descricao: novoEstilo.value });
    novoEstilo.value = '';
    carregarEstilos();
  } catch (error) {
    alert("Erro ao salvar estilo.");
  }
};

// ATIVA O MODO DE EDIÇÃO
const ativarEdicao = (estilo) => {
  idEmEdicao.value = estilo.id;
  // Criamos uma cópia para o input não alterar a tabela em tempo real (evita confusão se cancelar)
  estiloEdicao.value = { ...estilo };
};

const cancelarEdicao = () => {
  idEmEdicao.value = null;
  estiloEdicao.value = { descricao: '' };
};

const salvarEdicao = async (id) => {
  if (!estiloEdicao.value.descricao.trim()) return;
  
  try {
    await api.put(`/estilos/${id}`, { descricao: estiloEdicao.value.descricao });
    idEmEdicao.value = null; // Fecha o modo edição
    carregarEstilos(); // Atualiza a lista
  } catch (error) {
    alert("Erro ao atualizar. Verifique se o nome já existe.");
  }
};

const excluirEstilo = async (estilo) => {
  if (estilo.musicas_count > 0) return;
  if (!confirm(`Excluir estilo "${estilo.descricao}"?`)) return;
  try {
    await api.delete(`/estilos/${estilo.id}`);
    carregarEstilos();
  } catch (error) {
    alert("Erro ao excluir.");
  }
};

onMounted(carregarEstilos);
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
.edit-zone:hover .edit-icon-hint { opacity: 1 !important; }
.edit-icon-hint { opacity: 0; transition: opacity 0.2s; }
.estilo-row:hover { background-color: rgba(197, 141, 43, 0.05); }
</style>