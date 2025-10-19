<template>
  <div class="admin-wrap">
    <h2>Painel Admin</h2>

    <section class="card">
      <h3>TV ao Vivo por Local</h3>
      <div class="form-row">
        <input v-model="newCountry" placeholder="Novo país..." />
        <button @click="addCountry">Adicionar País</button>
        <button @click="removeCountry(modalCountry)" style="color:#c00; margin-left:8px;">Remover País Selecionado</button>
        <select v-model="modalCountry" @change="onSelectCountry">
          <option value="">Selecione o país</option>
          <option v-for="country in countries" :key="country" :value="country">{{ country }}</option>
        </select>
        <div v-if="modalCountry === 'Brasil'">
          <select v-if="states.length" v-model="modalState" @change="onSelectState">
            <option value="">Selecione o estado</option>
            <option v-for="state in states" :key="state" :value="state">{{ normalizeName(state) }}</option>
          </select>
          <select v-if="cities.length" v-model="modalCity">
            <option value="">Selecione a cidade</option>
            <option v-for="city in cities" :key="city" :value="city">{{ normalizeName(city) }}</option>
          </select>
        </div>
        <div v-else>
          <input v-model="modalCity" type="text" placeholder="Digite a cidade" />
        </div>
        <input v-model="tvLink" placeholder="URL do vídeo (YouTube/Twitch)" />
        <button @click="saveTvLink">Salvar</button>
      </div>
    </section>

    <section class="card">
      <h3>Usuários</h3>
      <div v-for="user in users" :key="user.id" class="user-row">
        <span>{{ user.name }} ({{ user.email }})
          <span v-if="user.is_banned" style="color:red">[Banido]</span>
        </span>
        <button @click="banUser(user.id)" v-if="!user.is_banned">Banir</button>
        <button @click="unbanUser(user.id)" v-if="user.is_banned">Desbanir</button>
        <button @click="deleteUser(user.id)" style="color:#c00">Remover</button>
      </div>
    </section>

    <section class="card">
      <h3>Palavras Banidas</h3>
      <form @submit.prevent="addBadWord" style="margin-bottom:8px;">
        <input v-model="newBadWord" placeholder="Nova palavra..." />
        <button type="submit">Adicionar</button>
      </form>
      <ul>
        <li v-for="word in badWords" :key="word">
          {{ word.word }}
          <button @click="removeBadWord(word.id)" style="margin-left:8px;color:#c00">Remover</button>
        </li>
      </ul>
    </section>

    <section class="card">
      <h3>Posts</h3>
      <div v-for="post in posts" :key="post.id" class="post-row">
        <span>{{ post.text }}</span>
        <button @click="deletePost(post.id)" style="color:#c00">Excluir</button>
      </div>
    </section>
    <section class="card">
      <h3>Comentários</h3>
      <div v-for="comment in comments" :key="comment.id" class="post-row">
        <span>{{ comment.body }}</span>
        <button @click="deleteComment(comment.id)" style="color:#c00">Excluir</button>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  name: 'AdminDashboard',
  data() {
    return {
      tvLink: '',
      locations: [],
      newLocation: { name: '', level: 'country', parent_id: null, youtube_url: '' },
      editing: null,
      modalCountry: '',
      modalState: '',
      modalCity: '',
  countries: [],
  newCountry: '',
  states: [],
  cities: [],
      users: [],
      badWords: [],
      newBadWord: '',
      posts: [],
      comments: []
    };
  },
  methods: {
    removeCountry(country) {
      this.countries = this.countries.filter(c => c !== country);
      localStorage.setItem('countries', JSON.stringify(this.countries));
    },
    async addCountry() {
      if (!this.newCountry.trim()) return;
      // Adiciona país apenas na barra lateral (array local)
      if (!this.countries.includes(this.newCountry)) {
        this.countries.push(this.newCountry);
        // Salva no localStorage
        localStorage.setItem('countries', JSON.stringify(this.countries));
      }
      this.newCountry = '';
    },
    normalizeName(str) {
      if (!str) return '';
      return str.trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    },
    onSelectCountry() {
      this.modalState = '';
      this.modalCity = '';
      this.states = [];
      this.cities = [];
      if (this.modalCountry === 'Brasil') {
        this.loadStates();
      }
    },
    async loadStates() {
      // Carrega estados do backend
      try {
        const res = await axios.get(`/locations/states?country=${this.modalCountry}`, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
        this.states = Array.isArray(res.data) ? res.data : [];
      } catch (e) { this.states = []; }
    },
    async onSelectState() {
      this.modalCity = '';
      this.cities = [];
      this.loadCities();
    },
    async loadCities() {
      // Carrega cidades do backend
      try {
        let url = '';
        if (this.modalCountry === 'Brasil') {
          url = `/locations/cities?country=${this.modalCountry}&state=${this.modalState}`;
        } else {
          url = `/locations/cities?country=${this.modalCountry}`;
        }
        const res = await axios.get(url, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
        this.cities = Array.isArray(res.data) ? res.data : [];
      } catch (e) { this.cities = []; }
    },
    async loadSettings() {
      try {
        const res = await axios.get('/admin/settings/tv_link', { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
        this.tvLink = res.data.url || '';
      } catch (e) { console.warn(e); }
    },
    async saveTvLink() {
      // Salva link associado ao país/estado/cidade
      try {
        const payload = {
          country: this.modalCountry,
          state: this.modalState,
          city: this.modalCity,
          url: this.tvLink
        };
        await axios.post('/admin/locations/tv_link', payload, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
        alert('Link salvo');
      } catch (e) { alert('Erro ao salvar'); console.error(e); }
    },
    async loadLocations() {
      try {
        const res = await axios.get('/admin/locations', { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
        this.locations = res.data || [];
      } catch (e) { console.error(e); }
    },
    async createLocation() {
      try {
        const payload = { ...this.newLocation };
        const res = await axios.post('/admin/locations', payload, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
        this.locations.push(res.data);
        this.newLocation = { name: '', level: 'country', parent_id: null, youtube_url: '' };
      } catch (e) { console.error(e); alert('Erro'); }
    },
    editLocation(loc) { this.editing = { ...loc }; },
    async updateLocation() {
      try {
        const res = await axios.put(`/admin/locations/${this.editing.id}`, this.editing, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
        const idx = this.locations.findIndex(l => l.id === this.editing.id);
        if (idx >= 0) this.locations.splice(idx, 1, res.data);
        this.editing = null;
      } catch (e) { console.error(e); alert('Erro ao atualizar'); }
    },
    async deleteLocation(loc) {
      if (!confirm('Excluir local?')) return;
      try {
        await axios.delete(`/admin/locations/${loc.id}`, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
        this.locations = this.locations.filter(l => l.id !== loc.id);
      } catch (e) { console.error(e); alert('Erro ao deletar'); }
    },
    async loadUsers() {
      const res = await axios.get('/admin/users', { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.users = Array.isArray(res.data) ? res.data : [];
    },
    async banUser(id) {
      const reason = prompt('Motivo do banimento:');
      const expires = prompt('Data/hora de expiração (YYYY-MM-DD HH:MM) ou deixe vazio para permanente:');
      await axios.post(`/admin/users/${id}/ban`, { reason, expires_at: expires }, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.loadUsers();
    },
    async unbanUser(id) {
      await axios.post(`/admin/users/${id}/unban`, {}, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.loadUsers();
    },
    async deleteUser(id) {
      if (!confirm('Remover usuário?')) return;
      await axios.delete(`/admin/users/${id}`, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.loadUsers();
    },
    async loadBadWords() {
      const res = await axios.get('/banned_words', { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.badWords = Array.isArray(res.data) ? res.data : [];
    },
    async addBadWord() {
      if (!this.newBadWord.trim()) return;
      await axios.post('/banned_words', { word: this.newBadWord }, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.newBadWord = '';
      this.loadBadWords();
    },
    async removeBadWord(id) {
      await axios.delete(`/banned_words/${id}`, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.loadBadWords();
    },
    async loadPosts() {
      const res = await axios.get('/posts', { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.posts = Array.isArray(res.data) ? res.data : [];
    },
    async deletePost(id) {
      if (!confirm('Excluir post?')) return;
      await axios.delete(`/posts/${id}`, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.loadPosts();
    },
    async loadComments() {
      const res = await axios.get('/comments', { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.comments = Array.isArray(res.data) ? res.data : [];
    },
    async deleteComment(id) {
      if (!confirm('Excluir comentário?')) return;
      await axios.delete(`/comments/${id}`, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }});
      this.loadComments();
    },
  },
  watch: {
    modalCountry(val) {
      if (val === 'Brasil') {
        this.loadStates();
      } else {
        this.states = [];
        this.cities = [];
      }
    },
    modalState(val) {
      if (val) {
        this.loadCities();
      } else {
        this.cities = [];
      }
    }
  },
  mounted() {
    // Carrega países do localStorage ou inicializa padrão
    const savedCountries = localStorage.getItem('countries');
    if (savedCountries) {
      this.countries = JSON.parse(savedCountries);
    } else {
      this.countries = ['Brasil', 'Estados Unidos', 'Argentina', 'Canadá', 'França', 'Alemanha', 'Japão', 'Portugal', 'Espanha'];
      localStorage.setItem('countries', JSON.stringify(this.countries));
    }
    this.loadSettings();
    this.loadLocations();
    this.loadUsers();
    this.loadBadWords();
    this.loadPosts();
    this.loadComments();
    // Se Brasil já estiver selecionado ao abrir, carrega estados
    if (this.modalCountry === 'Brasil') {
      this.loadStates();
    }
  }
};
</script>

<style scoped>
.admin-wrap { padding: 18px; max-width: 1100px; margin: 0 auto; }
.card { background: #fff; padding: 16px; border-radius: 8px; margin-top: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
.form-row { display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-top:8px; }
.form-row input, .form-row select { padding:8px; border-radius:6px; border:1px solid #ddd; }
.locations-list { margin-top:12px; display:flex; flex-direction:column; gap:8px; max-height:420px; overflow-y:auto; }
.location-item { display:flex; justify-content:space-between; align-items:center; background:#f6f6f6; padding:8px; border-radius:6px; }
.ls-actions button { margin-left:8px; }
.actions { margin-top:10px; }
.modal { width:100%; max-width:640px; background:#fff; padding:16px; border-radius:8px; }
.ghost { background:transparent; border:1px solid #ccc; padding:8px 10px; cursor:pointer; }
.hint { font-size:0.85rem; color:#666; margin-top:8px; }
.user-row, .post-row { display:flex; justify-content:space-between; align-items:center; padding:8px; border-bottom: 1px solid #ddd; }
</style>
