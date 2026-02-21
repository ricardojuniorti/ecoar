<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-7">
        
        <div class="d-flex align-items-center mb-4">
          <button @click="$router.back()" class="btn btn-outline-secondary btn-sm me-3 border-0">
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
                <tr v-for="artista in artistas" :key="artista.id">
                  <td class="ps-4 fw-bold text-dark">
                    {{ artista.nome }}
                    <span v-if="artista.musicas_count > 0" class="ms-2 badge bg-light text-muted border fw-normal">
                      {{ artista.musicas_count }} música(s) vinculada(s)
                    </span>
                  </td>
                  <td class="text-end pe-4">
                    <button 
                      @click="excluirArtista(artista)" 
                      class="btn btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                      :class="artista.musicas_count > 0 ? 'btn-secondary opacity-50' : 'btn-danger'"
                      :disabled="artista.musicas_count > 0"
                      :title="artista.musicas_count > 0 ? 'Artista vinculado a músicas' : 'Excluir Artista'"
                      style="width: 32px; height: 32px; border: none;"
                    >
                      <i v-if="artista.musicas_count > 0" class="bi bi-lock-fill text-white"></i>
                      <i v-else class="bi bi-x-lg text-white"></i>
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
import api from '../services/api';

const artistas = ref([]);
const novoArtista = ref('');

const carregarArtistas = async () => {
  try {
    const response = await api.get('/artistas');
    artistas.value = response.data;
  } catch (error) {
    console.error("Erro ao carregar artistas:", error);
  }
};

// No seu <script setup> do ArtistasView.vue

const adicionarArtista = async () => {
  if (!novoArtista.value.trim()) return;

  try {
    const response = await api.post('/artistas', { nome: novoArtista.value });
    const novoId = response.data.id; // Pega o ID que o Laravel acabou de gerar

    // VERIFICAÇÃO INTELIGENTE
    const rascunhoRaw = localStorage.getItem('rascunho_musica');
    if (rascunhoRaw) {
      const rascunho = JSON.parse(rascunhoRaw);

      if (rascunho.aguardando_artista) {
        // Atualiza o rascunho com o ID do novo artista e remove a flag
        rascunho.artista_id = novoId;
        delete rascunho.aguardando_artista;
        
        localStorage.setItem('rascunho_musica', JSON.stringify(rascunho));
        
        // Volta automaticamente para a tela de cadastro de música
        router.back(); 
        return; // Interrompe para não executar o resto
      }
    }

    // Se não tinha rascunho, fluxo normal (apenas limpa o campo e recarrega a lista)
    novoArtista.value = '';
    carregarArtistas();
  } catch (error) {
    alert("Erro ao salvar artista.");
  }
};

const excluirArtista = async (artista) => {
  // Trava no frontend: se tem música, nem abre o confirm
  if (artista.musicas_count > 0) {
    alert(`O artista "${artista.nome}" não pode ser excluído pois está sendo usado.`);
    return;
  }

  if (!confirm(`Deseja realmente excluir o artista "${artista.nome}"?`)) return;

  try {
    await api.delete(`/artistas/${artista.id}`);
    carregarArtistas();
  } catch (error) {
    if (error.response && error.response.status === 500) {
      alert("Erro de integridade: este artista ainda possui vínculos no banco.");
    } else {
      alert("Erro ao tentar excluir o artista.");
    }
  }
};

onMounted(carregarArtistas);
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
  box-shadow: 0 0 0 0.25rem rgba(197, 141, 43, 0.25);
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
  background-color: #dc3545; /* Vermelho sólido para visibilidade */
}
</style>