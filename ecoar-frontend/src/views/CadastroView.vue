<template>
  <div class="container mt-4 mb-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
          <div class="ecoar-header p-4 text-center">
            <h3 class="text-gold mb-0">
              {{ editando ? '📝 Editar Música' : '➕ Cadastrar Nova Música' }}
            </h3>
            <p class="text-white-50 small">
              {{ editando ? 'Atualize as informações do seu repertório' : 'Alimente o seu repertório oficial' }}
            </p>
          </div>

          <div class="card-body p-4 bg-white">
            <form @submit.prevent="salvarMusica">
              
              <div class="mb-3">
                <label class="form-label fw-bold text-dark">Nome da Música</label>
                <input v-model="form.titulo" type="text" class="form-control custom-input" required placeholder="Ex: A Thousand Years">
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold text-dark">Artista / Referência</label>
                <select v-model="form.artista_id" class="form-select custom-input" required>
                  <option value="" disabled>Selecione o artista...</option>
                  <option v-for="artista in artistas" :key="artista.id" :value="artista.id">
                    {{ artista.nome }}
                  </option>
                </select>
                <div class="form-text small">
                  Não achou? <a href="#" class="text-gold fw-bold" data-bs-toggle="modal" data-bs-target="#modalNovoItem" @click="configurarModal('artista')">Cadastre artista aqui</a>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold text-dark">Estilos</label>
                  <VueMultiselect 
                    v-model="form.estilos" 
                    :options="listaEstilos" 
                    :multiple="true" 
                    label="descricao" 
                    track-by="id" 
                    placeholder="Selecione os estilos"
                    selectLabel="Adicionar"
                    deselectLabel="Remover"
                    selectedLabel="Selecionado"
                  />
                  <div class="form-text small">
                    <a href="#" class="text-gold fw-bold" data-bs-toggle="modal" data-bs-target="#modalNovoItem" @click="configurarModal('estilo')">+ Novo Estilo</a>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold text-dark">Momentos Sugeridos</label>
                  <VueMultiselect 
                    v-model="form.momentos" 
                    :options="listaMomentos" 
                    :multiple="true" 
                    label="descricao" 
                    track-by="id" 
                    placeholder="Ex: Entrada, Alianças..."
                    selectLabel="Adicionar"
                    deselectLabel="Remover"
                    selectedLabel="Selecionado"
                  />
                  <div class="form-text small">
                    <a href="#" class="text-gold fw-bold" data-bs-toggle="modal" data-bs-target="#modalNovoItem" @click="configurarModal('momento')">+ Novo Momento</a>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-bold text-dark">Link (Youtube / Spotify)</label>
                <input v-model="form.link" type="url" class="form-control custom-input" placeholder="https://youtube.com/...">
              </div>

              <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-gold flex-grow-1 py-3 shadow">
                  <i class="bi bi-check-lg me-2"></i> {{ editando ? 'Salvar Alterações' : 'Gravar Música' }}
                </button>
                <button 
                type="button" 
                @click="router.push({ path: '/', query: route.query })" 
                class="btn btn-outline-secondary">
                Cancelar
              </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalNovoItem" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
          <div class="ecoar-header p-3 text-center text-white rounded-top">
            <h5 class="modal-title mb-0">➕ Novo {{ modalConfig.titulo }}</h5>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold small text-muted">DESCRIÇÃO</label>
              <input 
                v-model="novoItemNome" 
                type="text" 
                class="form-control border-gold" 
                :placeholder="'Ex: ' + modalConfig.placeholder"
                @keyup.enter="salvarItemRapido"
              >
            </div>
            <div class="d-grid gap-2">
              <button @click="salvarItemRapido" class="btn btn-gold py-2">Gravar e Adicionar</button>
              <button type="button" class="btn btn-sm btn-link text-muted" data-bs-dismiss="modal" id="btnFecharModal">Fechar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../services/api';
import VueMultiselect from 'vue-multiselect';

const router = useRouter();
const route = useRoute();

const editando = computed(() => !!route.params.id);
const artistas = ref([]);
const listaEstilos = ref([]);
const listaMomentos = ref([]);

const form = ref({
  titulo: '',
  artista_id: '',
  link: '',
  estilos: [],
  momentos: []
});

const novoItemNome = ref('');
const modalConfig = ref({ tipo: '', titulo: '', placeholder: '', endpoint: '' });

const carregarDadosIniciais = async () => {
  try {
    const [resArt, resEst, resMom] = await Promise.all([
      api.get('/artistas'),
      api.get('/estilos'),
      api.get('/momentos')
    ]);
    artistas.value = resArt.data;
    listaEstilos.value = resEst.data;
    listaMomentos.value = resMom.data;

    if (editando.value) {
      const resMusica = await api.get(`/musicas/${route.params.id}`);
      form.value = {
        ...resMusica.data,
        artista_id: resMusica.data.artista_id || '',
        estilos: resMusica.data.estilos || [],
        momentos: resMusica.data.momentos || []
      };
    }
  } catch (error) {
    console.error("Erro ao carregar dados:", error);
  }
};

const salvarMusica = async () => {
  try {
    const dadosParaEnviar = {
      titulo: form.value.titulo,
      artista_id: form.value.artista_id,
      link: form.value.link,
      estilos: form.value.estilos.map(e => e.id),
      momentos: form.value.momentos.map(m => m.id)
    };

    if (editando.value) {
      await api.put(`/musicas/${route.params.id}`, dadosParaEnviar);
      alert('Música atualizada com sucesso!');
    } else {
      await api.post('/musicas', dadosParaEnviar);
      alert('Música cadastrada com sucesso!');
    }
    router.push({ path: '/', query: route.query });

  } catch (error) {
    console.error(error); // Boa prática para você debugar se algo falhar
    alert('Erro ao salvar música. Verifique os campos obrigatórios.');
  }
};

const configurarModal = (tipo) => {
  const configs = {
    artista: { titulo: 'Artista', placeholder: 'Coldplay', endpoint: '/artistas' },
    estilo: { titulo: 'Estilo', placeholder: 'Jazz', endpoint: '/estilos' },
    momento: { titulo: 'Momento', placeholder: 'Alianças', endpoint: '/momentos' }
  };
  modalConfig.value = { ...configs[tipo], tipo };
  novoItemNome.value = '';
};

const salvarItemRapido = async () => {
  if (!novoItemNome.value.trim()) return;
  try {
    const payload = modalConfig.value.tipo === 'artista' 
      ? { nome: novoItemNome.value } 
      : { descricao: novoItemNome.value };
      
    const res = await api.post(modalConfig.value.endpoint, payload);
    const novoItem = res.data;

    if (modalConfig.value.tipo === 'artista') {
      const r = await api.get('/artistas');
      artistas.value = r.data;
      form.value.artista_id = novoItem.id;
    } else if (modalConfig.value.tipo === 'estilo') {
      const r = await api.get('/estilos');
      listaEstilos.value = r.data;
      form.value.estilos.push(novoItem);
    } else {
      const r = await api.get('/momentos');
      listaMomentos.value = r.data;
      form.value.momentos.push(novoItem);
    }

    document.getElementById('btnFecharModal').click();
  } catch (e) {
    alert("Erro ao cadastrar item rápido. Verifique se já não existe.");
  }
};

onMounted(carregarDadosIniciais);
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
.ecoar-header { background-color: var(--ecoar-verde); }
.text-gold { color: var(--ecoar-dourado) !important; }
.btn-gold { background-color: var(--ecoar-dourado); color: white; border: none; font-weight: bold; transition: all 0.3s; }
.btn-gold:hover { background-color: #a87724; transform: translateY(-1px); }

/* Inputs e Select Nativo */
.custom-input:focus { border-color: var(--ecoar-dourado); box-shadow: 0 0 0 0.25rem rgba(197, 141, 43, 0.1); }
.border-gold { border-color: var(--ecoar-dourado) !important; }

/* --- PADRONIZAÇÃO MULTISELECT --- */

/* Cor das Tags selecionadas */
:deep(.multiselect__tag) { 
  background: var(--ecoar-dourado) !important; 
  color: white !important;
}

/* Ícone de fechar da tag */
:deep(.multiselect__tag-icon:after) {
  color: white !important;
}
:deep(.multiselect__tag-icon:hover) {
  background: #a87724 !important;
}

/* Hover dourado nas opções da lista */
:deep(.multiselect__option--highlight) { 
  background: var(--ecoar-dourado) !important; 
  color: white !important; 
}

/* Texto de ajuda (Adicionar/Remover) no hover */
:deep(.multiselect__option--highlight::after) {
  background: var(--ecoar-dourado) !important;
}

/* Cor quando o item já está selecionado na lista */
:deep(.multiselect__option--selected.multiselect__option--highlight) {
  background: #a87724 !important;
  color: white !important;
}

/* Borda dourada quando o Multiselect está focado */
:deep(.multiselect--active) {
  border-color: var(--ecoar-dourado) !important;
}
</style>