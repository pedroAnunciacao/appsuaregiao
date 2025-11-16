<template>
  <div class="page">
    <div class="main-layout">
      <!-- LEFT SIDE: Calendar + Posts -->
      <div class="main-left">
        <!-- Wrapping calendar and description in a fixed container -->
        <div class="fixed-top-section">
          <div class="page-header">Sua Vida</div>
          <div class="calendar-card">
            <div class="calendar-header">
              <div class="calendar-header-top">Sua Vida</div>
              <div class="calendar-header-nav">
                <button @click="prevMonth">&lt;</button>
                <div class="calendar-header-month">{{ currentMonth }}</div>
                <button @click="nextMonth">&gt;</button>
              </div>
            </div>

            <table class="calendar-table">
              <thead>
                <tr>
                  <th>Dom</th>
                  <th>Seg</th>
                  <th>Ter</th>
                  <th>Qua</th>
                  <th>Qui</th>
                  <th>Sex</th>
                  <th>Sáb</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(week, idx) in calendarDays" :key="idx">
                  <td v-for="(day, dayIdx) in week" :key="dayIdx">
                    <span v-if="day" :class="{ 'calendar-day-active': isToday(day) }">{{ day }}</span>
                  </td>
                </tr>
              </tbody>
            </table>

            <button class="btn-primary btn-publish" @click="openCreatePostModal">
              Sua publicação
            </button>
          </div>

          <div class="section-title">Terra</div>
          <div class="section-text">
            Publique aqui textos, fotos e vídeos da sua cidade.
          </div>
        </div>

        <!-- Posts section with own scrollbar -->
        <div class="posts-section">
          <div v-if="posts.length === 0" class="posts-empty">
            Nenhum post encontrado.
          </div>
          <div v-for="post in posts" :key="post.id" class="post-card">
            <div class="post-header">
              <div class="post-author">
                <div class="post-author-name">{{ post.user.name }}</div>
                <div class="post-author-date">{{ formatDate(post.created_at) }}</div>
              </div>
            </div>
            <img v-if="post.thumbnail_path" :src="post.thumbnail_path" :alt="post.user.name" class="post-image">
            <div class="post-content">
              <p>{{ truncateText(post.text, 150) }}</p>
              <button v-if="post.text.length > 150" class="btn-read-more">Ver mais...</button>
            </div>
            <div class="post-footer">
              <div class="post-likes">❤️ {{ post.likes_count }} curtidas</div>
              <div class="post-comments-count">💬 {{ post.comments.length }} comentários</div>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT SIDE: Menu -->
      <div class="main-right">
        <div class="side-list">
          <!-- Page 1: Continentes e Países -->
          <template v-if="menuPage === 'continents'">
            <div v-for="continente in continentes" :key="continente.id">
              <div class="side-item white-text">
                {{ continente.nome }}
              </div>
              <div
                v-for="pais in getPaisesByContinente(continente.id)"
                :key="pais.id"
                class="side-item"
                @click="selectCountry(pais)"
              >
                {{ pais.nome }}
              </div>
            </div>
          </template>

          <!-- Page 2: Regiões e Cidades -->
          <template v-if="menuPage === 'cities'">
            <button class="btn-back" @click="menuPage = 'continents'">← Voltar</button>
            <div v-for="regiao in regioes" :key="regiao.id">
              <div class="side-item white-text">{{ regiao.nome }}</div>
              <div
                v-for="cidade in getCidadesByRegiao(regiao.id)"
                :key="cidade.id"
                class="side-item"
                @click="selectCity(cidade)"
              >
                {{ cidade.nome }}
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- MODALS -->
    <!-- Modal para criar/editar publicações -->
    <div v-if="showPostModal" class="modal-overlay" @click="closePostModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>{{ editingPost ? 'Editar Publicação' : 'Nova Publicação' }}</h2>
          <button class="modal-close" @click="closePostModal">&times;</button>
        </div>
        <div class="modal-body">
          <input v-model="postForm.title" type="text" placeholder="Título" class="input-rounded">
          <textarea v-model="postForm.content" placeholder="Conteúdo" class="input-rounded" rows="4"></textarea>
          <!-- <input v-model="postForm.image" type="file" @change="handleImageUpload" class="input-file"> -->
          <img v-if="postForm.imagePreview" :src="postForm.imagePreview" class="image-preview" alt="Preview">
        </div>
        <div class="modal-footer">
          <button class="btn-outline" @click="closePostModal">Cancelar</button>
          <button class="btn-primary" @click="savePost">{{ editingPost ? 'Atualizar' : 'Publicar' }}</button>
        </div>
      </div>
    </div>
  </div>

  <!-- BOTTOM NAV -->
  <nav class="bottom-nav">
    <!-- Home button now clears x-region_selected -->
    <button class="active" @click="goHome">🏠</button>
    <button>📅</button>
    <button>⭕</button>
  </nav>
</template>

<script>
import api from './services/api.js'

export default {
  name: 'DashboardView',
  data() {
    return {
      menuPage: 'continents',
      continentes: [],
      paises: [],
      regioes: [],
      cidades: [],
      selectedContinent: null,
      selectedCountry: null,
      selectedCity: null,
      currentMonth: 'Novembro 2025',
      calendarDays: [],
      showPostModal: false,
      editingPost: null,
      postForm: {
        title: '',
        content: '',
        image: null,
        imagePreview: null
      },
      posts: [],
      selectedRegionId: null
    }
  },
  mounted() {
    this.loadContinents()
    this.generateCalendar()
    this.loadPosts()
    this.selectedRegionId = localStorage.getItem('x-region_selected')
  },
  methods: {
    async loadContinents() {
      try {
        const response = await api.get('/continentes')
        this.continentes = response.data.data || response.data
        
        // Carregar países para cada continente
        for (let continente of this.continentes) {
          const paisResponse = await api.get(`/paises?continente_id=${continente.id}`)
          continente.paises = paisResponse.data.data || paisResponse.data
        }
      } catch (error) {
        console.error('Erro ao carregar continentes:', error)
      }
    },
    getPaisesByContinente(continenteId) {
      const continente = this.continentes.find(c => c.id === continenteId)
      return continente?.paises || []
    },
    async selectCountry(pais) {
      this.selectedCountry = pais
      try {
        const response = await api.get(`/regioes?pais_id=${pais.id}`)
        this.regioes = response.data.data || response.data
        this.menuPage = 'cities'
        console.log('[v0] Regiões carregadas com cidades aninhadas:', this.regioes)
      } catch (error) {
        console.error('Erro ao carregar regiões:', error)
      }
    },
    getCidadesByRegiao(regiaoId) {
      const regiao = this.regioes.find(r => r.id === regiaoId)
      return regiao?.cidades || []
    },
    selectCity(cidade) {
      this.selectedCity = cidade
      localStorage.setItem('x-region_selected', cidade.id)
      window.location.reload()
    },
    prevMonth() {
      alert('Navegação de meses será integrada pelo backend.')
    },
    nextMonth() {
      alert('Navegação de meses será integrada pelo backend.')
    },
    generateCalendar() {
      const now = new Date(2025, 10, 1)
      const year = now.getFullYear()
      const month = now.getMonth()
      const firstDay = new Date(year, month, 1)
      const lastDay = new Date(year, month + 1, 0)
      const daysInMonth = lastDay.getDate()
      const startingDayOfWeek = firstDay.getDay()

      const days = []
      let week = new Array(startingDayOfWeek).fill(null)

      for (let i = 1; i <= daysInMonth; i++) {
        week.push(i)
        if (week.length === 7) {
          days.push(week)
          week = []
        }
      }

      if (week.length > 0) {
        days.push(week)
      }

      this.calendarDays = days
    },
    isToday(day) {
      const today = new Date().getDate()
      return day === today
    },
    openCreatePostModal() {
      this.editingPost = null
      this.postForm = { title: '', content: '', image: null, imagePreview: null }
      this.showPostModal = true
    },
    closePostModal() {
      this.showPostModal = false
    },
    handleImageUpload(event) {
      const file = event.target.files[0]
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
          this.postForm.imagePreview = e.target.result
        }
        reader.readAsDataURL(file)
      }
    },
    async savePost() {
      const userId = localStorage.getItem('userId')
      const cityId = this.selectedCity?.id

      if (!userId || !cityId) {
        alert('Selecione uma cidade e faça login primeiro')
        return
      }

      const formData = new FormData()
      formData.append('titulo', this.postForm.title)
      formData.append('conteudo', this.postForm.content)
      formData.append('user_id', userId)
      formData.append('cidade_id', cityId)
      if (this.postForm.image) {
        formData.append('imagem', this.postForm.image)
      }

      try {
        const method = this.editingPost ? 'PUT' : 'POST'
        const url = this.editingPost
          ? `/posts/${this.editingPost.id}`
          : '/posts'

        const response = await api({
          method,
          url,
          data: formData,
          headers: { 'Content-Type': 'multipart/form-data' }
        })

        if (response.status === 200 || response.status === 201) {
          alert('Publicação salva com sucesso!')
          this.closePostModal()
        }
      } catch (error) {
        console.error('Erro ao salvar publicação:', error)
        alert('Erro ao salvar publicação')
      }
    },
    async loadPosts() {
      try {
        const headers = {}
        if (this.selectedRegionId) {
          headers['x-region_selected'] = this.selectedRegionId
        }
        const response = await api.get('/posts', { headers })
        this.posts = response.data.data || []
      } catch (error) {
        console.error('Erro ao carregar posts:', error)
      }
    },

    formatDate(dateString) {
      const date = new Date(dateString)
      return date.toLocaleDateString('pt-BR', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric' 
      })
    },

    truncateText(text, length) {
      if (text.length > length) {
        return text.substring(0, length) + '...'
      }
      return text
    },

    goHome() {
      localStorage.removeItem('x-region_selected')
      this.selectedRegionId = null
      this.loadPosts()
    }
  }
}
</script>