<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="d-flex align-items-center mb-4">
          <button @click="$router.back()" class="btn btn-outline-secondary btn-sm me-3 border-0">
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
                placeholder="Ex: Pop Rock, Jazz, Clássico..."
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
                <tr v-for="estilo in estilos" :key="estilo.id">
                  <td class="ps-4 fw-bold text-dark">
                    {{ estilo.descricao }}
                    <span v-if="estilo.musicas_count > 0" class="ms-2 badge bg-light text-muted border fw-normal">
                      {{ estilo.musicas_count }} música(s)
                    </span>
                  </td>
                  <td class="text-end pe-4">
                    <button 
                      @click="excluirEstilo(estilo)" 
                      class="btn btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                      :class="estilo.musicas_count > 0 ? 'btn-secondary opacity-50' : 'btn-danger'"
                      :disabled="estilo.musicas_count > 0"
                      :title="estilo.musicas_count > 0 ? 'Estilo em uso' : 'Excluir Estilo'"
                      style="width: 32px; height: 32px; border: none;"
                    >
                      <i v-if="estilo.musicas_count > 0" class="bi bi-lock-fill text-white"></i>
                      <i v-else class="bi bi-x-lg text-white"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="estilos.length === 0">
                  <td colspan="2" class="text-center py-4 text-muted small">
                    Nenhum estilo cadastrado.
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

const carregarEstilos = async () => {
  try {
    // Lembre-se de ajustar o Controller para usar withCount('musicas')
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
    alert("Erro ao salvar estilo. Verifique se ele já existe.");
  }
};

const excluirEstilo = async (estilo) => {
  if (estilo.musicas_count > 0) {
    alert("Este estilo não pode ser excluído pois existem músicas vinculadas a ele.");
    return;
  }

  if (!confirm(`Deseja realmente excluir o estilo "${estilo.descricao}"?`)) return;

  try {
    await api.delete(`/estilos/${estilo.id}`);
    carregarEstilos();
  } catch (error) {
    alert("Erro ao excluir no servidor.");
  }
};

onMounted(carregarEstilos);
</script>

<style scoped>
.ecoar-dark-card {
  background-color: var(--ecoar-verde);
  border-radius: 12px;
}

.custom-input-dark {
  background-color: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--ecoar-dourado);
  color: white;
}

.custom-input-dark:focus {
  background-color: white;
  color: var(--ecoar-verde);
}

.ecoar-header {
  background-color: var(--ecoar-verde);
}

.text-gold {
  color: var(--ecoar-dourado) !important;
}

.btn-gold {
  background-color: var(--ecoar-dourado);
  color: white;
  border: none;
}

.btn-danger {
  background-color: #dc3545;
}
</style>