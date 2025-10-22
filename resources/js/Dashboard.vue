<template>
<div>
  <!-- Modal de localização hierárquica -->
  <div v-if="showLocationModal" class="location-modal">
    <LocationMenu
      @close="showLocationModal = false"
      @location-selected="handleLocationSelected"
    />
  </div>
  <div class="layout">
    <div class="content-area">
      <section class="hero">
        <div class="tv-header">
          <div class="tv-title">Sua Tv (Ao Vivo)</div>
          <div class="tv-video">
            <template v-if="youtubeUrl && youtubeUrl !== 'https://www.youtube.com/embed/4Q46xYqUwZQ'">
              <iframe
                :src="youtubeUrl"
                width="100%"
                height="180"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                class="tv-iframe"
              ></iframe>
            </template>
            <template v-else>
              <img src="https://i.imgur.com/1Q46xYq.jpg" alt="tv ao vivo" class="tv-img" />
              <div class="tv-play">▶</div>
            </template>
          </div>
          <div class="tv-post">
            <div class="tv-post-title">Sua Postagem</div>
            <div class="tv-post-desc">Publique aqui textos, fotos e vídeos da sua cidade.</div>
          </div>
        </div>
        <div class="city-country">
          <span class="city-country-text">{{ countryText }}</span>
        </div>
        <div class="logo-central">
          <img :src="logoSrc" alt="Logo" class="logo-img" />
        </div>
      </section>
      <div class="posts-list">
        <div v-for="post in filteredPosts" :key="post.id" class="post-item">
          <div class="post-user">{{ post.user?.name }}</div>
          <div class="post-date">{{ formatDate(post.created_at) }}</div>
          <div class="post-content">{{ post.content }}</div>
          <div v-if="post.media_url" class="post-media">
            <img :src="post.media_url" alt="media" />
          </div>
        </div>
        <button v-if="isInOwnCityView" class="central-floating-btn" @click="openCreate">
          <span class="plus-sign">+</span>
        </button>
      </div>
    </div>
    <aside class="right-sidebar">
      <div class="weather">
        <div class="weather-icon">☁️</div>
        <div class="weather-temp">{{ weather.temp }}°C</div>
        <div class="weather-desc">{{ weather.desc }}</div>
      </div>
      <div class="region-selector">
        <button @click="openLocationMenu" class="open-menu-btn">
          📍 Selecionar Região
        </button>
        <div v-if="currentLocation" class="current-location">
          <strong>{{ currentLocation.cidade?.nome }}</strong>
          <small>{{ currentLocation.regiao?.nome }}, {{ currentLocation.pais?.nome }}</small>
        </div>
      </div>
    </aside>
  </div>
  <nav class="bottom-nav">
    <button @click="nav('home')">Home</button>
    <button @click="nav('region')">Região</button>
  <button v-if="userEmail === 'admin@gmail.com'" @click="$emit('go-admin-dashboard')" style="margin: 0 24px;">Painel Admin</button>
    <button @click="nav('history')">Histórico</button>
    <button @click="nav('account')">Minha Conta</button>
  </nav>
</div>
</template>

<script>
import api from "./services/api.js";
import logo2 from './assets/logo-2.png';
import LocationMenu from './components/LocationMenu.vue';

// countryMap expandido para tradução completa
const countryMap = {
  "Brazil": { name: "Brasil", code: "BR" },
  "United States": { name: "Estados Unidossss", code: "US" },
  "Canada": { name: "Canadá", code: "CA" },
  "United Kingdom": { name: "Reino Unido", code: "GB" },
  "Germany": { name: "Alemanha", code: "DE" },
  "France": { name: "França", code: "FR" },
  "Argentina": { name: "Argentina", code: "AR" },
  "Spain": { name: "Espanha", code: "ES" },
  "Italy": { name: "Itália", code: "IT" },
  "Portugal": { name: "Portugal", code: "PT" },
  "Mexico": { name: "México", code: "MX" },
  "Japan": { name: "Japão", code: "JP" },
  "China": { name: "China", code: "CN" },
  "Russia": { name: "Rússia", code: "RU" },
  "India": { name: "Índia", code: "IN" },
  "Australia": { name: "Austrália", code: "AU" },
  "South Africa": { name: "África do Sul", code: "ZA" },
  "Egypt": { name: "Egito", code: "EG" },
  "Turkey": { name: "Turquia", code: "TR" },
  "South Korea": { name: "Coreia do Sul", code: "KR" },
  "Netherlands": { name: "Holanda", code: "NL" },
  "Sweden": { name: "Suécia", code: "SE" },
  "Norway": { name: "Noruega", code: "NO" },
  "Finland": { name: "Finlândia", code: "FI" },
  "Denmark": { name: "Dinamarca", code: "DK" },
  "Switzerland": { name: "Suíça", code: "CH" },
  "Belgium": { name: "Bélgica", code: "BE" },
  "Austria": { name: "Áustria", code: "AT" },
  "Poland": { name: "Polônia", code: "PL" },
  "Ukraine": { name: "Ucrânia", code: "UA" },
  "Greece": { name: "Grécia", code: "GR" },
  "Israel": { name: "Israel", code: "IL" },
  "Saudi Arabia": { name: "Arábia Saudita", code: "SA" },
  "United Arab Emirates": { name: "Emirados Árabes", code: "AE" },
  "Morocco": { name: "Marrocos", code: "MA" },
  "Chile": { name: "Chile", code: "CL" },
  "Colombia": { name: "Colômbia", code: "CO" },
  "Peru": { name: "Peru", code: "PE" },
  "Venezuela": { name: "Venezuela", code: "VE" },
  "Uruguay": { name: "Uruguai", code: "UY" },
  "Cuba": { name: "Cuba", code: "CU" },
  "Paraguay": { name: "Paraguai", code: "PY" },
  "Bolivia": { name: "Bolívia", code: "BO" },
  "Ecuador": { name: "Equador", code: "EC" },
  "Guatemala": { name: "Guatemala", code: "GT" },
  "Honduras": { name: "Honduras", code: "HN" },
  "El Salvador": { name: "El Salvador", code: "SV" },
  "Costa Rica": { name: "Costa Rica", code: "CR" },
  "Panama": { name: "Panamá", code: "PA" },
  "Dominican Republic": { name: "República Dominicana", code: "DO" },
  "Puerto Rico": { name: "Porto Rico", code: "PR" },
  "Bahamas": { name: "Bahamas", code: "BS" },
  "Jamaica": { name: "Jamaica", code: "JM" },
  "Trinidad and Tobago": { name: "Trinidad e Tobago", code: "TT" },
  "Guyana": { name: "Guiana", code: "GY" },
  "Suriname": { name: "Suriname", code: "SR" },
  "Belize": { name: "Belize", code: "BZ" },
  "Barbados": { name: "Barbados", code: "BB" },
  "Saint Lucia": { name: "Santa Lúcia", code: "LC" },
  "Grenada": { name: "Granada", code: "GD" },
  "Saint Vincent and the Grenadines": { name: "São Vicente e Granadinas", code: "VC" },
  "Antigua and Barbuda": { name: "Antígua e Barbuda", code: "AG" },
  "Saint Kitts and Nevis": { name: "São Cristóvão e Nevis", code: "KN" },
  "Dominica": { name: "Dominica", code: "DM" },
  "Haiti": { name: "Haiti", code: "HT" },
  "Greenland": { name: "Groenlândia", code: "GL" },
  "Iceland": { name: "Islândia", code: "IS" },
  "Luxembourg": { name: "Luxemburgo", code: "LU" },
  "Liechtenstein": { name: "Liechtenstein", code: "LI" },
  "Monaco": { name: "Mônaco", code: "MC" },
  "San Marino": { name: "San Marino", code: "SM" },
  "Vatican City": { name: "Vaticano", code: "VA" },
  "Andorra": { name: "Andorra", code: "AD" },
  "Estonia": { name: "Estônia", code: "EE" },
  "Latvia": { name: "Letônia", code: "LV" },
  "Lithuania": { name: "Lituânia", code: "LT" },
  "Czech Republic": { name: "República Tcheca", code: "CZ" },
  "Slovakia": { name: "Eslováquia", code: "SK" },
  "Hungary": { name: "Hungria", code: "HU" },
  "Romania": { name: "Romênia", code: "RO" },
  "Bulgaria": { name: "Bulgária", code: "BG" },
  "Croatia": { name: "Croácia", code: "HR" },
  "Slovenia": { name: "Eslovênia", code: "SI" },
  "Serbia": { name: "Sérvia", code: "RS" },
  "Montenegro": { name: "Montenegro", code: "ME" },
  "Bosnia and Herzegovina": { name: "Bósnia e Herzegovina", code: "BA" },
  "Macedonia": { name: "Macedônia", code: "MK" },
  "Albania": { name: "Albânia", code: "AL" },
  "Moldova": { name: "Moldávia", code: "MD" },
  "Armenia": { name: "Armênia", code: "AM" },
  "Georgia": { name: "Geórgia", code: "GE" },
  "Azerbaijan": { name: "Azerbaijão", code: "AZ" },
  "Kazakhstan": { name: "Cazaquistão", code: "KZ" },
  "Uzbekistan": { name: "Uzbequistão", code: "UZ" },
  "Turkmenistan": { name: "Turcomenistão", code: "TM" },
  "Kyrgyzstan": { name: "Quirguistão", code: "KG" },
  "Tajikistan": { name: "Tadjiquistão", code: "TJ" },
  "Afghanistan": { name: "Afeganistão", code: "AF" },
  "Pakistan": { name: "Paquistão", code: "PK" },
  "Bangladesh": { name: "Bangladesh", code: "BD" },
  "Sri Lanka": { name: "Sri Lanka", code: "LK" },
  "Nepal": { name: "Nepal", code: "NP" },
  "Bhutan": { name: "Butão", code: "BT" },
  "Maldives": { name: "Maldivas", code: "MV" },
  "Thailand": { name: "Tailândia", code: "TH" },
  "Vietnam": { name: "Vietnã", code: "VN" },
  "Cambodia": { name: "Camboja", code: "KH" },
  "Laos": { name: "Laos", code: "LA" },
  "Myanmar": { name: "Mianmar", code: "MM" },
  "Malaysia": { name: "Malásia", code: "MY" },
  "Singapore": { name: "Singapura", code: "SG" },
  "Indonesia": { name: "Indonésia", code: "ID" },
  "Philippines": { name: "Filipinas", code: "PH" },
  "Mongolia": { name: "Mongólia", code: "MN" },
  "North Korea": { name: "Coreia do Norte", code: "KP" },
  "Taiwan": { name: "Taiwan", code: "TW" },
  "Hong Kong": { name: "Hong Kong", code: "HK" },
  "Macau": { name: "Macau", code: "MO" },
  "Brunei": { name: "Brunei", code: "BN" },
  "Timor-Leste": { name: "Timor-Leste", code: "TL" },
  "Papua New Guinea": { name: "Papua-Nova Guiné", code: "PG" },
  "Fiji": { name: "Fiji", code: "FJ" },
  "Samoa": { name: "Samoa", code: "WS" },
  "Tonga": { name: "Tonga", code: "TO" },
  "Vanuatu": { name: "Vanuatu", code: "VU" },
  "Solomon Islands": { name: "Ilhas Salomão", code: "SB" },
  "New Zealand": { name: "Nova Zelândia", code: "NZ" },
  "Micronesia": { name: "Micronésia", code: "FM" },
  "Palau": { name: "Palau", code: "PW" },
  "Marshall Islands": { name: "Ilhas Marshall", code: "MH" },
  "Nauru": { name: "Nauru", code: "NR" },
  "Kiribati": { name: "Kiribati", code: "KI" },
  "Tuvalu": { name: "Tuvalu", code: "TV" },
  "Seychelles": { name: "Seicheles", code: "SC" },
  "Madagascar": { name: "Madagáscar", code: "MG" },
  "Mauritius": { name: "Maurício", code: "MU" },
  "Comoros": { name: "Comores", code: "KM" },
  "Malawi": { name: "Malawi", code: "MW" },
  "Mozambique": { name: "Moçambique", code: "MZ" },
  "Zambia": { name: "Zâmbia", code: "ZM" },
  "Zimbabwe": { name: "Zimbábue", code: "ZW" },
  "Botswana": { name: "Botsuana", code: "BW" },
  "Namibia": { name: "Namíbia", code: "NA" },
  "Angola": { name: "Angola", code: "AO" },
  "Congo": { name: "Congo", code: "CG" },
  "Democratic Republic of the Congo": { name: "República Democrática do Congo", code: "CD" },
  "Gabon": { name: "Gabão", code: "GA" },
  "Equatorial Guinea": { name: "Guiné Equatorial", code: "GQ" },
  "Cameroon": { name: "Camarões", code: "CM" },
  "Nigeria": { name: "Nigéria", code: "NG" },
  "Ghana": { name: "Gana", code: "GH" },
  "Ivory Coast": { name: "Costa do Marfim", code: "CI" },
  "Senegal": { name: "Senegal", code: "SN" },
  "Guinea": { name: "Guiné", code: "GN" },
  "Sierra Leone": { name: "Serra Leoa", code: "SL" },
  "Liberia": { name: "Libéria", code: "LR" },
  "Burkina Faso": { name: "Burquina Faso", code: "BF" },
  "Mali": { name: "Mali", code: "ML" },
  "Niger": { name: "Níger", code: "NE" },
  "Chad": { name: "Chade", code: "TD" },
  "Sudan": { name: "Sudão", code: "SD" },
  "South Sudan": { name: "Sudão do Sul", code: "SS" },
  "Ethiopia": { name: "Etiópia", code: "ET" },
  "Somalia": { name: "Somália", code: "SO" },
  "Kenya": { name: "Quênia", code: "KE" },
  "Uganda": { name: "Uganda", code: "UG" },
  "Tanzania": { name: "Tanzânia", code: "TZ" },
  "Rwanda": { name: "Ruanda", code: "RW" },
  "Burundi": { name: "Burundi", code: "BI" },
  "Djibouti": { name: "Djibuti", code: "DJ" },
  "Eritrea": { name: "Eritreia", code: "ER" },
  "Central African Republic": { name: "República Centro-Africana", code: "CF" },
  "Benin": { name: "Benin", code: "BJ" },
  "Togo": { name: "Togo", code: "TG" },
  "Gambia": { name: "Gâmbia", code: "GM" },
  "Cape Verde": { name: "Cabo Verde", code: "CV" },
  "Mauritania": { name: "Mauritânia", code: "MR" },
  "Morocco": { name: "Marrocos", code: "MA" },
  "Algeria": { name: "Argélia", code: "DZ" },
  "Tunisia": { name: "Tunísia", code: "TN" },
  "Libya": { name: "Líbia", code: "LY" },
  "Egypt": { name: "Egito", code: "EG" },
  "Palestine": { name: "Palestina", code: "PS" },
  "Lebanon": { name: "Líbano", code: "LB" },
  "Syria": { name: "Síria", code: "SY" },
  "Jordan": { name: "Jordânia", code: "JO" },
  "Iraq": { name: "Iraque", code: "IQ" },
  "Iran": { name: "Irã", code: "IR" },
  "Kuwait": { name: "Kuwait", code: "KW" },
  "Bahrain": { name: "Bahrein", code: "BH" },
  "Qatar": { name: "Catar", code: "QA" },
  "Oman": { name: "Omã", code: "OM" },
  "Yemen": { name: "Iêmen", code: "YE" }
};

export default {
  components: {
    LocationMenu
  },
  data() {
    return {
      youtubeUrl: "https://www.youtube.com/embed/4Q46xYqUwZQ",
      logoSrc: logo2,
      defaultAvatar: "https://via.placeholder.com/40",
      userCity: "",
      userCountry: "",
      selectedCity: "",
      selectedCountry: "",
      isAdmin: false,
      isInOwnCity: true,
      posts: [],
      weather: { temp: "--", desc: "Carregando..." },
      isLoading: true,
      showLocationModal: false,
      currentLocation: null,
      filteredCountry: '',
      filteredCity: '',
      countryMap,
      menuView: 'region',
      userEmail: '',
    };
  },
  computed: {
    filteredPosts() {
      if (this.menuView === 'home') {
        // Home: mostra todos os posts
        return this.posts.filter((p) => p.content || p.media_url);
      }
      if (this.filteredCountry) {
        return this.posts.filter(
          (p) => (p.content || p.media_url) &&
            this.normalize(p.user?.country) === this.normalize(this.filteredCountry) &&
            (!this.filteredCity || this.normalize(p.user?.city) === this.normalize(this.filteredCity))
        );
      }
      // Região: posts da cidade do usuário
      return this.posts.filter(
        (p) => (p.content || p.media_url) &&
          this.normalize(p.user?.city) === this.normalize(this.userCity) &&
          this.normalize(p.user?.country) === this.normalize(this.userCountry)
      );
    },
    countryText() {
      if (this.menuView === 'home') {
        return 'Terra';
      }
      if (this.filteredCountry) {
        return `${this.filteredCity || ''}${this.filteredCity ? ', ' : ''}${this.filteredCountry}`;
      }
      return `${this.userCity}${this.userCity ? ', ' : ''}${this.userCountry}`;
    },
    isInOwnCityView() {
  // Admin vê sempre, inclusive na home (Terra)
  if (this.userEmail === 'admin@gmail.com') return true;
  // Usuário comum: botão + só aparece se estiver na própria cidade/estado/país, e nunca na home (Terra) ou só país
  // Se está na home (Terra), não mostra
  if (this.menuView === 'home') return false;
  // Se está filtrando só país (sem cidade), não mostra
  if (this.filteredCountry && !this.filteredCity) return false;
  // Usuário comum: só se está na própria cidade e país
  const cityOk = this.normalize(this.userCity) === this.normalize(this.filteredCity || this.userCity);
  const countryOk = this.normalize(this.userCountry) === this.normalize(this.filteredCountry || this.userCountry);
  return cityOk && countryOk;
    },
  },
  watch: {
    modalRegion(val) {
      this.loadStates(val);
      this.modalState = "";
      this.modalCity = "";
      this.cities = [];
    },
    modalState(val) {
      this.loadCities(val);
      this.modalCity = "";
    }
  },
  methods: {
    convertToEmbed(url) {
      // Converte links normais do YouTube para embed
      if (!url) return '';
      // YouTube normal: https://www.youtube.com/watch?v=ID
      const ytMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/);
      if (ytMatch) {
        return `https://www.youtube.com/embed/${ytMatch[1]}`;
      }
      // Twitch: https://player.twitch.tv/?channel=CHANNEL
      // (aqui pode adicionar lógica para outros serviços)
      return url;
    },
    async fetchTvLink() {
      // Busca link de vídeo para país/cidade selecionados
      let url = '';
      if (this.filteredCountry && this.filteredCity) {
        url = `/api/locations/tv_link?country=${this.filteredCountry}&city=${this.filteredCity}`;
      } else if (this.filteredCountry) {
        url = `/api/locations/tv_link?country=${this.filteredCountry}`;
      } else {
        url = `/api/locations/tv_link?country=Brasil`;
      }
      try {
    const res = await api.get(url);
    this.youtubeUrl = this.convertToEmbed(res.data.url) || this.youtubeUrl;
    console.log('TV ao vivo URL:', this.youtubeUrl);
      } catch (e) {}
    },
    formatDate(d) {
      const date = new Date(d);
      return isNaN(date) ? "-" : date.toLocaleDateString("pt-BR");
    },
    normalize(str) {
      return str?.trim().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    },
    async fetchUser() {
      try {
        this.fetchCountries();
        const token = localStorage.getItem("token");
        if (!token) {
          this.isLoading = false;
          return;
        }
        const res = await api.get("/api/user", {
          headers: { Authorization: `Bearer ${token}` }
        });
        if (res.data) {
          this.userCity = res.data.city || "";
          this.userCountry = res.data.country || "";
          this.filteredCity = this.userCity;
          this.filteredCountry = this.userCountry;
          this.fetchTvLink();
          this.selectedCity = this.userCity;
          this.selectedCountry = this.userCountry;
          // Corrige: admin se isAdmin ou is_admin for 1 ou true
          this.userEmail = res.data.email || '';
          this.isAdmin = this.userEmail === 'admin@gmail.com';
          const normalizedUserCity = this.normalize(this.userCity);
          const normalizedSelectedCity = this.normalize(this.selectedCity);
          this.isInOwnCity = normalizedUserCity === normalizedSelectedCity;
          // Se admin, vai direto para Home e não mostra modal
          if (this.isAdmin) {
            this.menuView = 'home';
            this.showLocationModal = false;
            this.filteredCountry = '';
            this.filteredCity = '';
          }
        }
        this.isLoading = false;
        if (this.isAdmin) {
          this.fetchPosts();
        } else if (this.userCity) {
          this.fetchUserCityPosts();
          this.fetchWeather();
        } else {
          this.fetchPosts();
        }
      } catch (e) {
        this.isLoading = false;
        this.fetchPosts();
      }
    },
    fetchCountries() {
      // Países fixos para o modal do login
      this.countries = [
        'Brasil', 'Estados Unidos', 'Argentina', 'Canadá', 'França', 'Alemanha', 'Japão', 'Portugal', 'Espanha'
      ];
    },
    async loadRegions() {
      const res = await api.get('/api/admin/locations?level=region&parent=Brasil');
      this.regions = res.data.map(r => r.name);
    },
    async loadStates() {
  const res = await api.get('/api/estados');
  // Mantém apenas as siglas dos estados
  this.states = res.data;
    },
    async loadCities(state) {
  if (!state) { this.cities = []; return; }
  // Corrige: usa query string para compatibilidade com backend
  const res = await api.get(`/api/cidades/${state}`);
  this.cities = res.data.map(c => this.normalizeName(c));
    },
    normalizeName(str) {
      if (!str) return '';
      // Remove caracteres estranhos e normaliza encoding
      return str
        .replace(/[\uFFFD\?]+/g, '') // Remove pontos de interrogação e caracteres inválidos
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, ' ')
        .trim();
    },
    async fetchPosts() {
      try {
        const res = await api.get("/posts");
        if (Array.isArray(res.data)) {
          this.posts = res.data;
        } else if (res.data && Array.isArray(res.data.data)) {
          this.posts = res.data.data;
        } else {
          this.posts = [];
        }
      } catch (e) {
        console.error('Erro ao carregar posts:', e);
        this.posts = [];
      }
    },
    handleLocationSelected(location) {
      this.currentLocation = location;
      this.showLocationModal = false;

      // Atualiza o texto exibido
      if (location.cidade) {
        this.filteredCity = location.cidade.nome;
      }
      if (location.pais) {
        this.filteredCountry = location.pais.nome;
      }

      // Recarrega os posts com a nova localização
      this.fetchPosts();
    },
    openLocationMenu() {
      this.showLocationModal = true;
    },
      async fetchWeather(validateOnly = false) {
  let city = '';
  let country = '';
  if (validateOnly) {
    city = this.modalCity;
    country = this.modalCountry;
  } else if (this.filteredCity) {
    city = this.filteredCity;
    country = this.filteredCountry;
  } else if (this.userCity) {
    city = this.userCity;
    country = this.userCountry;
  }
  if (!city || !country) return;
  try {
    const apiKey = "0c80d5976ca76722ed51673286e90cbe";
    // Usa nome do país, não código
    const res = await api.get(`https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)},${encodeURIComponent(country)}&appid=${apiKey}&units=metric&lang=pt`);
    if (validateOnly) return true;
    this.weather.temp = res.data.main && res.data.main.temp ? Math.round(res.data.main.temp) : '--';
    this.weather.desc = res.data.weather && res.data.weather[0] && res.data.weather[0].description ? res.data.weather[0].description : 'Indisponível';
    if (this.weather.temp === '--' || this.weather.desc === 'Indisponível') {
      this.cityError = "Cidade não encontrada ou não existe. Tente novamente.";
      this.showLocationModal = true;
    }
  } catch (e) {
    if (validateOnly) {
      this.locationError = "Cidade não encontrada ou não existe. Tente novamente.";
      this.showLocationModal = true;
      throw new Error('Cidade não encontrada ou não existe. Tente novamente.');
    }
    this.weather = { temp: "--", desc: "Indisponível" };
    this.cityError = "Cidade não encontrada ou não existe. Tente novamente.";
    this.showLocationModal = true;
    alert("Cidade não encontrada ou clima indisponível. Tente novamente.");
  }
},
    openCreate() {
      if (!this.isAdmin && !this.isInOwnCity) {
        alert(`Você só pode postar na sua cidade (${this.userCity})`);
        return;
      }
      alert("Abrir modal de criação de post.");
    },
    nav(page) {
      if (page === 'home') {
        this.menuView = 'home';
        this.filteredCountry = '';
        this.filteredCity = '';
        // Se admin, mostra todos os posts
        if (this.isAdmin) {
          this.fetchPosts();
        }
      } else if (page === 'region') {
        this.menuView = 'region';
        this.filteredCountry = '';
        this.filteredCity = '';
        // Se admin, mostra todos os posts
        if (this.isAdmin) {
          this.fetchPosts();
        }
      } else if (page === 'admin') {
        this.goToAdminDashboard();
      } else {
        alert("Navegar para " + page);
      }
    },

    // ...existing code...
  },
  mounted() {
    this.fetchUser();
    this.fetchPosts();
  },
  created() {
  // Exibe modal só se usuário não tem localização e não é admin
  const token = localStorage.getItem('token');
  if (token) {
  api.get('/api/user', { headers: { Authorization: `Bearer ${token}` } })
    .then(res => {
      const email = res.data.email || '';
      if (email !== 'admin@gmail.com' && !res.data.city && !res.data.country) {
        this.showLocationModal = true;
      }
    })
    .catch(() => {});
} else {
  this.showLocationModal = true;
  }
 }
}
</script>

<style scoped>
.layout {
  display: flex;
  flex-direction: row;
  height: 100vh;
  background: #2196f3;
}
.content-area {
  flex: 1;
  background: #ffffff;
  padding: 0;
}
.hero {
  background: #2196f3;
  padding: 0;
  position: relative;
}
.tv-header {
  background: #000;
  color: #fff;
  padding: 0;
  text-align: center;
}
.tv-title {
  font-size: 2.2rem;
  font-weight: bold;
  padding: 16px 0 0 0;
}
.tv-video {
  position: relative;
  width: 100%;
  height: 180px;
  background: #222;
  display: flex;
  align-items: center;
  justify-content: center;
}
.tv-img {
  width: 100%;
  height: 180px;
  object-fit: cover;
}
.tv-play {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  font-size: 3rem;
  color: #fff;
  opacity: 0.8;
}
.tv-post {
  background: #000;
  color: #fff;
  padding: 8px 0 16px 0;
}
.tv-post-title {
  font-size: 1.3rem;
  font-weight: bold;
}
.tv-post-desc {
  font-size: 1rem;
}
.city-country {
  background: #2196f3;
  color: #fff;
  text-align: center;
  font-size: 2rem;
  font-weight: bold;
  padding: 90px 0 0 0;
}
.city-country-text {
  font-size: 1.5rem;
  color: #222;
  font-weight: bold;
  letter-spacing: 0.5px;
  margin: 0;
  position: relative;
  top: -50px;
}
.logo-central {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  bottom: -60px; /* ou outro valor para descer só a logo */
  z-index: 2;
}
.logo-img {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  background: #2196f3;
  border: 4px solid #222;
  object-fit: cover;
}
.posts-list {
  background: #fff;
  padding: 0 12px;
  position: relative;
}
.post-item {
  background: #fff;
  border-radius: 8px;
  margin: 12px 0;
  padding: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
  color: #000000;
}
.post-user, .post-date, .post-content {
  color: #222;
}
.right-sidebar {
  width: 180px;
  background: #2196f3;
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  padding: 0;
 /* height: 1000px;*/
 margin-left: 10px;
}
.weather {
  background: #2196f3;
  color: #fff;
  text-align: center;
  padding: 16px 0 8px 0;
}
.weather-icon {
  font-size: 2.2rem;
}
.weather-temp {
  font-size: 1.5rem;
  font-weight: bold;
}
.weather-desc {
  font-size: 1rem;
}
.region-selector {
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.open-menu-btn {
  background: #fff;
  color: #2196f3;
  border: 2px solid #222;
  border-radius: 10px;
  padding: 14px 12px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
}

.open-menu-btn:hover {
  background: #222;
  color: #fff;
}

.current-location {
  background: #fff;
  color: #222;
  border-radius: 8px;
  padding: 12px;
  text-align: center;
}

.current-location strong {
  display: block;
  font-size: 1.1rem;
  margin-bottom: 4px;
}

.current-location small {
  font-size: 0.9rem;
  color: #666;
}
.central-floating-btn {
  position: absolute;
  right: 24px;
  bottom: -200px;
  background: #2196f3;
  color: #fff;
  border: none;
  border-radius: 12px;
  width: 10px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  cursor: pointer;
  z-index: 10;
}
.plus-sign {
  font-size: 2.4rem;
  color: #fff;
  position: absolute;
  left: 50%;
  top: 43%;
  transform: translate(-50%, -50%);
}
.bottom-nav {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100vw;
  background: #2196f3;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 8px 0;
  z-index: 9;
}
.bottom-nav button {
  background: #2196f3;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px 18px;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  margin: 0 4px;
}
.bottom-nav button:hover {
  background: #2196f3;
}
.location-modal {
  position: fixed;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}


/* === Responsividade para celular (até 768px) === */
/* === Layout móvel organizado === */
@media (max-width: 768px) {
  .layout {
              display: flex !important;
              flex-direction: row !important;
              width: 100vw !important;
              min-width: 0 !important;
              padding: 0 !important;
              position: relative !important;
            }
            .content-area {
              width: 70vw !important;
              min-width: 0 !important;
              padding: 0 !important;
              align-items: flex-start !important;
              display: flex !important;
              flex-direction: column !important;
              position: relative !important;
            }
            .hero {
              display: flex !important;
              flex-direction: column !important;
              align-items: stretch !important;
              padding: 0 !important;
              gap: 0 !important;
            }
            .weather-box {
              width: 100vw !important;
              background: #2196f3 !important;
              color: #fff !important;
              text-align: center !important;
              padding: 12px 0 8px 0 !important;
              font-size: 1.1rem !important;
              border-radius: 0 !important;
              margin-bottom: 0 !important;
            }
            .tv-header {
              display: flex !important;
              flex-direction: column !important;
              align-items: center !important;
              gap: 8px !important;
              width: 100vw !important;
              margin: 0 !important;
            }
            .tv-title {
              font-size: 1.3rem !important;
              text-align: center !important;
              margin-bottom: 4px !important;
            }
            .tv-video {
              width: 100vw !important;
              height: auto !important;
              min-width: 0 !important;
              margin-bottom: 0 !important;
              border-radius: 0 !important;
              overflow: hidden !important;
            }
            .tv-iframe {
              width: 100vw !important;
              height: 180px !important;
              border-radius: 0 !important;
            }
            .tv-img {
              width: 100vw !important;
              height: auto !important;
              border-radius: 0 !important;
            }
            .tv-play {
              font-size: 2.2rem !important;
              left: 50% !important;
              top: 50% !important;
              transform: translate(-50%, -50%) !important;
            }
            .city-country {
              margin: 8px 0 0 0 !important;
              text-align: center !important;
              font-size: 1.2rem !important;
              font-weight: bold !important;
            }
            .logo-central {
              margin: 12px auto 0 auto !important;
              text-align: center !important;
              width: 100vw !important;
              display: flex !important;
              justify-content: center !important;
            }
            .logo-img {
              width: 80px !important;
              height: 80px !important;
              margin: 0 auto !important;
              display: block !important;
              border-radius: 50% !important;
              box-shadow: 0 2px 8px rgba(0,0,0,0.10) !important;
            }
            .side-bar {
              position: fixed !important;
              right: 0 !important;
              top: 0 !important;
              height: 100vh !important;
              width: 30vw !important;
              min-width: 120px !important;
              max-width: 180px !important;
              display: flex !important;
              flex-direction: column !important;
              overflow-y: auto !important;
              gap: 4px !important;
              background: #2196f3 !important;
              padding: 4px 0 !important;
              z-index: 100 !important;
            }
            .side-bar .side-btn {
              width: 90% !important;
              font-size: 1rem !important;
              margin: 4px auto !important;
              padding: 10px 0 !important;
              border-radius: 8px !important;
              background: #fff !important;
              color: #2196f3 !important;
              border: 1px solid #2196f3 !important;
              text-align: center !important;
            }
            .central-floating-btn {
              position: static !important;
              display: flex !important;
              margin: 16px auto 0 auto !important;
              left: 0 !important;
              right: 0 !important;
              bottom: auto !important;
              background: #2196f3 !important;
              color: #fff !important;
              border: none !important;
              border-radius: 50% !important;
              width: 64px !important;
              height: 64px !important;
              align-items: center !important;
              justify-content: center !important;
              font-size: 2.2rem !important;
              box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
              cursor: pointer !important;
              z-index: 10 !important;
            }
            .plus-sign {
              font-size: 2.4rem !important;
              color: #fff !important;
              position: static !important;
              left: auto !important;
              top: auto !important;
              transform: none !important;
              margin: 0 auto !important;
            }
            .bottom-nav {
              position: fixed !important;
              bottom: 0 !important;
              left: 0 !important;
              width: 100vw !important;
              display: flex !important;
              justify-content: space-around !important;
              background: #fff !important;
              box-shadow: 0 -2px 8px rgba(0,0,0,0.08) !important;
              z-index: 100 !important;
              padding: 8px 0 !important;
            }
            .bottom-nav button {
              flex: 1 1 0 !important;
              margin: 0 2px !important;
              font-size: 1rem !important;
              padding: 10px 0 !important;
            }
            .location-modal form {
              min-width: 90vw !important;
              padding: 18px 8px !important;
            }
            .location-selects {
              gap: 8px !important;
            }
            .location-confirm-btn {
              width: 100% !important;
              font-size: 1rem !important;
              padding: 10px 0 !important;
            }
          }
</style>