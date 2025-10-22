<template>
  <div class="location-menu">
    <div class="menu-header">
      <h3>Selecione uma localização</h3>
      <button @click="$emit('close')" class="close-btn">✕</button>
    </div>

    <div class="menu-content">
      <!-- Continentes -->
      <div v-if="step === 'continente'" class="menu-step">
        <h4>Continentes</h4>
        <div class="menu-list">
          <button
            v-for="continente in continentes"
            :key="continente.id"
            @click="selectContinente(continente)"
            class="menu-item"
          >
            {{ continente.nome }}
          </button>
        </div>
      </div>

      <!-- Países -->
      <div v-if="step === 'pais'" class="menu-step">
        <button @click="goBack" class="back-btn">← Voltar</button>
        <h4>Países de {{ selectedContinente?.nome }}</h4>
        <div class="menu-list">
          <button
            v-for="pais in paises"
            :key="pais.id"
            @click="selectPais(pais)"
            class="menu-item"
          >
            {{ pais.nome }}
          </button>
        </div>
      </div>

      <!-- Regiões -->
      <div v-if="step === 'regiao'" class="menu-step">
        <button @click="goBack" class="back-btn">← Voltar</button>
        <h4>Regiões de {{ selectedPais?.nome }}</h4>
        <div class="menu-list">
          <button
            v-for="regiao in regioes"
            :key="regiao.id"
            @click="selectRegiao(regiao)"
            class="menu-item"
          >
            {{ regiao.nome }}
          </button>
        </div>
      </div>

      <!-- Cidades -->
      <div v-if="step === 'cidade'" class="menu-step">
        <button @click="goBack" class="back-btn">← Voltar</button>
        <h4>Cidades de {{ selectedRegiao?.nome }}</h4>
        <div class="menu-list">
          <button
            v-for="cidade in cidades"
            :key="cidade.id"
            @click="selectCidade(cidade)"
            class="menu-item"
          >
            {{ cidade.nome }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api.js';

export default {
  name: 'LocationMenu',
  data() {
    return {
      step: 'continente',
      continentes: [],
      paises: [],
      regioes: [],
      cidades: [],
      selectedContinente: null,
      selectedPais: null,
      selectedRegiao: null,
      history: []
    };
  },
  methods: {
    async loadContinentes() {
      try {
        const res = await api.get('/continentes');
        this.continentes = res.data.data || res.data;
      } catch (error) {
        console.error('Erro ao carregar continentes:', error);
      }
    },
    async selectContinente(continente) {
      this.selectedContinente = continente;
      this.history.push('continente');
      this.step = 'pais';
      try {
        const res = await api.get('/paises', {
          params: { paises_continente_id: continente.id }
        });
        this.paises = res.data.data || res.data;
      } catch (error) {
        console.error('Erro ao carregar países:', error);
      }
    },
    async selectPais(pais) {
      this.selectedPais = pais;
      this.history.push('pais');
      this.step = 'regiao';
      try {
        const res = await api.get('/regioes', {
          params: { pais_id: pais.id }
        });
        this.regioes = res.data.data || res.data;
      } catch (error) {
        console.error('Erro ao carregar regiões:', error);
      }
    },
    async selectRegiao(regiao) {
      this.selectedRegiao = regiao;
      this.history.push('regiao');
      this.step = 'cidade';
      try {
        const res = await api.get('/cidades', {
          params: { paises_regiao_id: regiao.id }
        });
        this.cidades = res.data.data || res.data;
      } catch (error) {
        console.error('Erro ao carregar cidades:', error);
      }
    },
    selectCidade(cidade) {
      // Salva o ID da cidade selecionada no localStorage
      localStorage.setItem('selected_region_id', cidade.id);

      // Emite evento com todas as informações da seleção
      this.$emit('location-selected', {
        continente: this.selectedContinente,
        pais: this.selectedPais,
        regiao: this.selectedRegiao,
        cidade: cidade
      });
    },
    goBack() {
      const previousStep = this.history.pop();
      if (previousStep === 'continente') {
        this.step = 'continente';
        this.selectedContinente = null;
        this.paises = [];
      } else if (previousStep === 'pais') {
        this.step = 'pais';
        this.selectedPais = null;
        this.regioes = [];
      } else if (previousStep === 'regiao') {
        this.step = 'regiao';
        this.selectedRegiao = null;
        this.cidades = [];
      }
    }
  },
  mounted() {
    this.loadContinentes();
  }
};
</script>

<style scoped>
.location-menu {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  max-width: 500px;
  width: 90%;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.menu-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 2px solid #2196f3;
}

.menu-header h3 {
  margin: 0;
  color: #222;
  font-size: 1.5rem;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #666;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  color: #222;
}

.menu-content {
  padding: 12px 0;
}

.menu-step h4 {
  color: #2196f3;
  font-size: 1.2rem;
  margin-bottom: 16px;
}

.back-btn {
  background: #f5f5f5;
  color: #222;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  margin-bottom: 16px;
  font-size: 1rem;
  font-weight: 600;
}

.back-btn:hover {
  background: #e0e0e0;
}

.menu-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 400px;
  overflow-y: auto;
}

.menu-item {
  background: #2196f3;
  color: #222;
  border: 2px solid #222;
  border-radius: 10px;
  padding: 14px 20px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  text-align: left;
  transition: all 0.2s;
}

.menu-item:hover {
  background: #1976d2;
  color: #fff;
  transform: translateX(5px);
}

@media (max-width: 768px) {
  .location-menu {
    width: 95%;
    padding: 16px;
    max-height: 90vh;
  }

  .menu-header h3 {
    font-size: 1.2rem;
  }

  .menu-item {
    padding: 12px 16px;
    font-size: 1rem;
  }
}
</style>
