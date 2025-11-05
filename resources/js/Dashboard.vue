<template>
<div>
  <div class="layout">
    <!-- Calendário fixo no topo (substituindo "Sua Vida" e "Sua TV") -->
    <div class="calendar-fixed">
      <div class="calendar-container">
        <div class="calendar-month">
          <button class="calendar-nav-btn" @click="previousMonth">‹</button>
          <span class="calendar-month-text">{{ currentMonthName }} {{ currentYear }}</span>
          <button class="calendar-nav-btn" @click="nextMonth">›</button>
        </div>
        <div class="calendar-weekdays">
          <div class="calendar-weekday">D</div>
          <div class="calendar-weekday">S</div>
          <div class="calendar-weekday">T</div>
          <div class="calendar-weekday">Q</div>
          <div class="calendar-weekday">Q</div>
          <div class="calendar-weekday">S</div>
          <div class="calendar-weekday">S</div>
        </div>
        <div class="calendar-days">
          <div
            v-for="day in calendarDays"
            :key="day.key"
            class="calendar-day"
            :class="{
              'calendar-day-empty': !day.day,
              'calendar-day-today': day.isToday,
              'calendar-day-selected': day.isSelected
            }"
            @click="selectDay(day)"
          >
            {{ day.day }}
          </div>
        </div>
      </div>
    </div>

    <!-- Área de conteúdo agora começa após o calendário -->
    <div class="content-area">
      <!-- Seção "Sua Postagem" e "Terra" -->
      <section class="hero">
        <div class="tv-post">
          <div class="tv-post-title">Sua Postagem</div>
          <div class="tv-post-desc">Publique aqui textos, fotos e vídeos da sua cidade.</div>
        </div>
        <div class="city-country">
          <span class="city-country-text">{{ locationText }}</span>
        </div>
        <div class="logo-central">
          <img :src="logoSrc" alt="Logo" class="logo-img" />
        </div>
      </section>

      <!-- Banner informativo da cidade selecionada -->
      <div v-if="selectedCityId && selectedCityId !== userDefaultCityId" class="city-info-banner">
        <div class="city-info-text">
          Você está vendo posts de <strong>{{ getCityName() }}</strong>
        </div>
        <button class="city-info-btn" @click="resetToUserCity">
          Voltar para sua cidade
        </button>
      </div>

      <!-- Display posts with thumbnails, text preview, comments and likes -->
      <div class="posts-list">
        <div v-if="isLoading" class="loading">Carregando posts...</div>
        <div v-else-if="posts.length === 0" class="no-posts">Nenhum post encontrado</div>
        <div v-else v-for="post in posts" :key="post.id" class="post-item">
          <div class="post-header">
            <div class="post-user">{{ post.user?.name || 'Usuário' }}</div>
            <div class="post-date">{{ formatDate(post.created_at) }}</div>
          </div>
          <!-- Imagem ANTES do texto (como no Instagram) -->
          <div v-if="post.thumbnail_path" class="post-media">
            <img :src="post.thumbnail_path" alt="Post media" />
          </div>
          <!-- Preview do texto com botão "Ver mais" -->
          <div class="post-content">
            <template v-if="isPostExpanded(post.id)">
              {{ post.text || post.content }}
            </template>
            <template v-else>
              {{ getPostPreview(post.text || post.content) }}
            </template>
            <button
              v-if="shouldShowReadMore(post.text || post.content)"
              class="read-more-btn"
              @click="togglePostExpansion(post.id)"
            >
              {{ isPostExpanded(post.id) ? 'Ver menos' : 'Ver mais...' }}
            </button>
          </div>

          <!-- Adicionada seção de likes e comentários -->
          <div class="post-actions">
            <div class="post-likes">
              <span class="like-icon">❤️</span>
              <span class="like-count">{{ post.likes_count || 0 }} curtidas</span>
            </div>
          </div>

          <!-- Seção de comentários -->
          <div v-if="post.comments && post.comments.length > 0" class="post-comments">
            <div class="comments-title">Comentários ({{ post.comments.length }})</div>
            <div v-for="(comment, index) in post.comments" :key="index" class="comment-item">
              <div class="comment-user">{{ comment.user?.name || 'Usuário' }}</div>
              <div class="comment-body">{{ comment.body }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Menu lateral agora tem botão para abrir modal em mobile -->
    <aside class="right-sidebar">
      <div class="weather">
        <div class="weather-icon">☁️</div>
        <div class="weather-temp">{{ weather.temp }}°C</div>
        <div class="weather-desc">{{ weather.desc }}</div>
      </div>

      <div class="location-menu">
        <!-- Nível 1: Continentes -->
        <div v-if="sidebarStep === 'continent'" class="menu-section">
          <h3 class="menu-title">Continentes</h3>
          <!-- Alterado para coluna única (1fr) -->
          <div class="checkbox-grid-sidebar">
            <label
              v-for="continent in continents"
              :key="continent.id"
              class="checkbox-item-sidebar"
            >
              <input
                type="checkbox"
                :value="continent.id"
                :checked="selectedContinentId === continent.id"
                @change="selectContinentSidebar(continent.id)"
              />
              <span class="checkbox-label-sidebar">{{ continent.nome }}</span>
            </label>
          </div>
        </div>

        <!-- Nível 2: Países -->
        <div v-if="sidebarStep === 'country'" class="menu-section">
          <h3 class="menu-title">Países</h3>
          <button class="back-btn-sidebar" @click="sidebarGoBackToContinent">← Voltar</button>
          <div class="checkbox-grid-sidebar">
            <label
              v-for="country in countries"
              :key="country.id"
              class="checkbox-item-sidebar"
            >
              <input
                type="checkbox"
                :value="country.id"
                :checked="selectedCountryId === country.id"
                @change="selectCountrySidebar(country.id)"
              />
              <span class="checkbox-label-sidebar">{{ country.nome }}</span>
            </label>
          </div>
        </div>

        <!-- Nível 3: Regiões -->
        <div v-if="sidebarStep === 'region'" class="menu-section">
          <h3 class="menu-title">Regiões</h3>
          <button class="back-btn-sidebar" @click="sidebarGoBackToCountry">← Voltar</button>
          <div class="checkbox-grid-sidebar">
            <label
              v-for="region in regions"
              :key="region.id"
              class="checkbox-item-sidebar"
            >
              <input
                type="checkbox"
                :value="region.id"
                :checked="selectedRegionId === region.id"
                @change="selectRegionSidebar(region.id)"
              />
              <span class="checkbox-label-sidebar">{{ region.nome }}</span>
            </label>
          </div>
        </div>

        <!-- Nível 4: Cidades -->
        <div v-if="sidebarStep === 'city'" class="menu-section">
          <h3 class="menu-title">Cidades</h3>
          <button class="back-btn-sidebar" @click="sidebarGoBackToRegion">← Voltar</button>
          <div class="checkbox-grid-sidebar">
            <label
              v-for="city in cities"
              :key="city.id"
              class="checkbox-item-sidebar"
            >
              <input
                type="checkbox"
                :value="city.id"
                :checked="selectedCityId === city.id"
                @change="selectCitySidebar(city.id)"
              />
              <span class="checkbox-label-sidebar">{{ city.nome }}</span>
            </label>
          </div>
        </div>
      </div>
    </aside>

    <!-- Botão flutuante para abrir menu em mobile (oculto em desktop) -->
    <button class="mobile-menu-btn" @click="toggleMobileMenu">
      ☰
    </button>
  </div>

  <!-- Modal de menu para mobile -->
  <div v-if="showMobileMenu" class="mobile-menu-overlay" @click="closeMobileMenu">
    <div class="mobile-menu-modal" @click.stop>
      <div class="mobile-menu-header">
        <h3>Selecione sua localização</h3>
        <button class="mobile-menu-close" @click="closeMobileMenu">✕</button>
      </div>
      <div class="mobile-menu-body">
        <div class="location-menu">
          <!-- Nível 1: Continentes -->
          <div v-if="sidebarStep === 'continent'" class="menu-section">
            <h3 class="menu-title">Continentes</h3>
            <div class="checkbox-grid-sidebar">
              <label
                v-for="continent in continents"
                :key="continent.id"
                class="checkbox-item-sidebar"
              >
                <input
                  type="checkbox"
                  :value="continent.id"
                  :checked="selectedContinentId === continent.id"
                  @change="selectContinentSidebar(continent.id)"
                />
                <span class="checkbox-label-sidebar">{{ continent.nome }}</span>
              </label>
            </div>
          </div>

          <!-- Nível 2: Países -->
          <div v-if="sidebarStep === 'country'" class="menu-section">
            <h3 class="menu-title">Países</h3>
            <button class="back-btn-sidebar" @click="sidebarGoBackToContinent">← Voltar</button>
            <div class="checkbox-grid-sidebar">
              <label
                v-for="country in countries"
                :key="country.id"
                class="checkbox-item-sidebar"
              >
                <input
                  type="checkbox"
                  :value="country.id"
                  :checked="selectedCountryId === country.id"
                  @change="selectCountrySidebar(country.id)"
                />
                <span class="checkbox-label-sidebar">{{ country.nome }}</span>
              </label>
            </div>
          </div>

          <!-- Nível 3: Regiões -->
          <div v-if="sidebarStep === 'region'" class="menu-section">
            <h3 class="menu-title">Regiões</h3>
            <button class="back-btn-sidebar" @click="sidebarGoBackToCountry">← Voltar</button>
            <div class="checkbox-grid-sidebar">
              <label
                v-for="region in regions"
                :key="region.id"
                class="checkbox-item-sidebar"
              >
                <input
                  type="checkbox"
                  :value="region.id"
                  :checked="selectedRegionId === region.id"
                  @change="selectRegionSidebar(region.id)"
                />
                <span class="checkbox-label-sidebar">{{ region.nome }}</span>
              </label>
            </div>
          </div>

          <!-- Nível 4: Cidades -->
          <div v-if="sidebarStep === 'city'" class="menu-section">
            <h3 class="menu-title">Cidades</h3>
            <button class="back-btn-sidebar" @click="sidebarGoBackToRegion">← Voltar</button>
            <div class="checkbox-grid-sidebar">
              <label
                v-for="city in cities"
                :key="city.id"
                class="checkbox-item-sidebar"
              >
                <input
                  type="checkbox"
                  :value="city.id"
                  :checked="selectedCityId === city.id"
                  @change="selectCitySidebar(city.id)"
                />
                <span class="checkbox-label-sidebar">{{ city.nome }}</span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de calendário "Sua Vida" -->
  <div v-if="showCalendarModal" class="calendar-modal-overlay" @click="closeCalendarModal">
    <div class="calendar-modal" @click.stop>
      <div class="calendar-header">Sua Vida</div>
      <div class="calendar-body">
        <div class="calendar-month">
          <button class="calendar-nav-btn" @click="previousMonth">‹</button>
          <span class="calendar-month-text">{{ currentMonthName }} {{ currentYear }}</span>
          <button class="calendar-nav-btn" @click="nextMonth">›</button>
        </div>
        <div class="calendar-weekdays">
          <div class="calendar-weekday">D</div>
          <div class="calendar-weekday">S</div>
          <div class="calendar-weekday">T</div>
          <div class="calendar-weekday">Q</div>
          <div class="calendar-weekday">Q</div>
          <div class="calendar-weekday">S</div>
          <div class="calendar-weekday">S</div>
        </div>
        <div class="calendar-days">
          <div
            v-for="day in calendarDays"
            :key="day.key"
            class="calendar-day"
            :class="{
              'calendar-day-empty': !day.day,
              'calendar-day-today': day.isToday,
              'calendar-day-selected': day.isSelected
            }"
            @click="selectDay(day)"
          >
            {{ day.day }}
          </div>
        </div>
      </div>
      <button class="calendar-btn-new-post" @click="openCreate">Nova publicação</button>
    </div>
  </div>

  <!-- Barra de navegação inferior com botão "+" entre Home e Região -->
  <nav class="bottom-nav">
    <button @click="nav('home')">Home</button>
    <button class="add-post-btn" @click="openCreate">+</button>
    <button @click="nav('region')">Região</button>
    <button v-if="isAdmin" @click="$emit('go-admin-dashboard')">Admin</button>
    <button @click="nav('history')">Histórico</button>
    <button @click="nav('account')">Conta</button>
  </nav>
</div>
</template>

<script>
import api from "./services/api.js";
import logo2 from './assets/logo-2.png';

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
  " Liechtenstein": { name: "Liechtenstein", code: "LI" },
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
  data() {
    return {
      youtubeUrl: "https://www.youtube.com/embed/4Q46xYqUwZQ",
      logoSrc: logo2,
      userCity: "",
      userCountry: "",
      isAdmin: false,
      posts: [],
      weather: { temp: "--", desc: "Carregando..." },
      isLoading: true,
      locationError: '', // This is also related to the removed modal.

      continents: [],
      countries: [],
      regions: [],
      cities: [],

      selectedContinentId: '',
      selectedCountryId: '',
      selectedRegionId: '',
      selectedCityId: '',

      sidebarStep: 'continent',

      userEmail: '',
      canPost: false,
      userDefaultCityId: null, // Armazena a cidade padrão do usuário
      expandedPosts: [], // Array para controlar quais posts estão expandidos
      showCalendarModal: false,
      currentMonth: new Date().getMonth(),
      currentYear: new Date().getFullYear(),
      selectedDate: null,
      showMobileMenu: false, // Adicionado estado para controlar modal mobile
    };
  },
  computed: {
    locationText() {
      if (this.selectedCityId) {
        const city = this.cities.find(c => c.id === this.selectedCityId);
        return city ? city.nome : 'Terra';
      }
      if (this.selectedRegionId) {
        const region = this.regions.find(r => r.id === this.selectedRegionId);
        return region ? region.nome : 'Terra';
      }
      if (this.selectedCountryId) {
        const country = this.countries.find(c => c.id === this.selectedCountryId);
        return country ? country.nome : 'Terra';
      }
      if (this.selectedContinentId) {
        const continent = this.continents.find(c => c.id === this.selectedContinentId);
        return continent ? continent.nome : 'Terra';
      }
      return 'Terra';
    },

    currentMonthName() {
      const months = [
        'JANEIRO', 'FEVEREIRO', 'MARÇO', 'ABRIL', 'MAIO', 'JUNHO',
        'JULHO', 'AGOSTO', 'SETEMBRO', 'OUTUBRO', 'NOVEMBRO', 'DEZEMBRO'
      ];
      return months[this.currentMonth];
    },

    calendarDays() {
      const firstDay = new Date(this.currentYear, this.currentMonth, 1);
      const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
      const daysInMonth = lastDay.getDate();
      const startingDayOfWeek = firstDay.getDay();

      const days = [];

      // Adiciona dias vazios antes do primeiro dia do mês
      for (let i = 0; i < startingDayOfWeek; i++) {
        days.push({ key: `empty-${i}`, day: null, isToday: false, isSelected: false });
      }

      // Adiciona os dias do mês
      const today = new Date();
      for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(this.currentYear, this.currentMonth, day);
        const isToday = date.toDateString() === today.toDateString();
        const isSelected = this.selectedDate && date.toDateString() === this.selectedDate.toDateString();

        days.push({
          key: `day-${day}`,
          day,
          date,
          isToday,
          isSelected
        });
      }

      return days;
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
      if (this.selectedCityId) {
        url = `/api/locations/tv_link?city_id=${this.selectedCityId}`;
      } else if (this.selectedCountryId) {
        url = `/api/locations/tv_link?country_id=${this.selectedCountryId}`;
      } else if (this.selectedContinentId) {
        url = `/api/locations/tv_link?continent_id=${this.selectedContinentId}`;
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

    async fetchContinents() {
      try {
        const res = await api.get('/continentes');
        console.log('[v0] Continents response:', res.data);
        this.continents = res.data.data || res.data || [];
      } catch (e) {
        console.error('Error fetching continents:', e);
        this.continents = [];
      }
    },

    async fetchCountries(continentId) {
      try {
        const res = await api.get(`/paises?continente_id=${continentId}`);
        console.log('[v0] Countries response:', res.data);
        this.countries = res.data.data || res.data || [];
      } catch (e) {
        console.error('Error fetching countries:', e);
        this.countries = [];
      }
    },

    async fetchRegions(countryId) {
      try {
        const res = await api.get(`/regioes?pais_id=${countryId}`);
        console.log('[v0] Regions response:', res.data);
        this.regions = res.data.data || res.data || [];
      } catch (e) {
        console.error('Error fetching regions:', e);
        this.regions = [];
      }
    },

    async fetchCities(regionId) {
      try {
        const res = await api.get(`/cidades?regiao_id=${regionId}`);
        console.log('[v0] Cities response:', res.data);
        this.cities = res.data.data || res.data || [];
      } catch (e) {
        console.error('Error fetching cities:', e);
        this.cities = [];
      }
    },

    async selectContinentSidebar(continentId) {
      this.selectedContinentId = continentId;
      this.selectedCountryId = '';
      this.selectedRegionId = '';
      this.selectedCityId = '';
      this.countries = [];
      this.regions = [];
      this.cities = [];

      await this.fetchCountries(continentId);
      this.sidebarStep = 'country';
      this.fetchPosts();
      this.fetchTvLink();
    },

    async selectCountrySidebar(countryId) {
      this.selectedCountryId = countryId;
      this.selectedRegionId = '';
      this.selectedCityId = '';
      this.regions = [];
      this.cities = [];

      await this.fetchRegions(countryId);
      this.sidebarStep = 'region';
      this.fetchPosts();
      this.fetchTvLink();
    },

    async selectRegionSidebar(regionId) {
      this.selectedRegionId = regionId;
      this.selectedCityId = '';
      this.cities = [];

      await this.fetchCities(regionId);
      this.sidebarStep = 'city';
      this.fetchPosts();
      this.fetchWeather();
      this.fetchTvLink();
    },

    selectCitySidebar(cityId) {
      this.selectedCityId = cityId;
      localStorage.setItem('selectedRegionId', cityId);
      this.fetchPosts();
      this.fetchWeather();
      this.fetchTvLink();
    },

    sidebarGoBackToContinent() {
      this.sidebarStep = 'continent';
      this.selectedCountryId = '';
      this.selectedRegionId = '';
      this.selectedCityId = '';
      this.countries = [];
      this.regions = [];
      this.cities = [];
      this.fetchPosts();
    },

    sidebarGoBackToCountry() {
      this.sidebarStep = 'country';
      this.selectedRegionId = '';
      this.selectedCityId = '';
      this.regions = [];
      this.cities = [];
      this.fetchPosts();
    },

    sidebarGoBackToRegion() {
      this.sidebarStep = 'region';
      this.selectedCityId = '';
      this.cities = [];
      this.fetchPosts();
    },

    async fetchUser() {
      try {
        const token = localStorage.getItem("token");
        console.log('[v0] Token encontrado:', token ? 'Sim' : 'Não');

        if (!token) {
          this.isLoading = false;
          this.$router.push('/login');
          return;
        }

        const res = await api.get("/user");
        console.log('[v0] Dados do usuário:', res.data);

        if (res.data) {
          this.userEmail = res.data.email || '';
          this.isAdmin = this.userEmail === 'admin@gmail.com';
          this.canPost = true;

          if (res.data.pais_regiao_cidade_id) {
            this.userDefaultCityId = res.data.pais_regiao_cidade_id;

            // Verifica se há uma cidade selecionada no localStorage
            const storedCityId = localStorage.getItem('selectedRegionId');
            if (storedCityId) {
              this.selectedCityId = parseInt(storedCityId);
            } else {
              this.selectedCityId = this.userDefaultCityId;
              localStorage.setItem('selectedRegionId', this.userDefaultCityId);
            }

            // Fetch related location data to populate sidebar correctly
            try {
              const cityResponse = await api.get(`/cidades/${this.selectedCityId}`);
              const city = cityResponse.data;
              this.cities = [city];

              const regionResponse = await api.get(`/regioes/${city.regiao_id}`);
              const region = regionResponse.data;
              this.regions = [region];
              this.selectedRegionId = region.id;

              const countryResponse = await api.get(`/paises/${region.pais_id}`);
              const country = countryResponse.data;
              this.countries = [country];
              this.selectedCountryId = country.id;

              const continentResponse = await api.get(`/continentes/${country.continente_id}`);
              const continent = continentResponse.data;
              this.continents = [continent];
              this.selectedContinentId = continent.id;

              this.sidebarStep = 'city';
            } catch (e) {
              console.error('[v0] Erro ao buscar dados de localização:', e);
            }
          } else if (this.isAdmin) {
            this.selectedContinentId = '';
            this.selectedCountryId = '';
            this.selectedRegionId = '';
            this.selectedCityId = '';
            this.sidebarStep = 'continent';
          }
        }
        this.isLoading = false;
      } catch (e) {
        console.error('[v0] Erro ao buscar usuário:', e);
        this.isLoading = false;

        if (e.response && (e.response.status === 401 || e.response.status === 403)) {
          localStorage.removeItem('token');
          localStorage.removeItem('userId');
          this.$router.push('/login');
        }
      }
    },

    async fetchPosts() {
      try {
        this.isLoading = true;
        let apiUrl = '/posts';
        if (this.selectedCityId) {
          apiUrl = `/posts?cidade_id=${this.selectedCityId}`;
        } else if (this.selectedRegionId) {
          apiUrl = `/posts?regiao_id=${this.selectedRegionId}`;
        } else if (this.selectedCountryId) {
          apiUrl = `/posts?pais_id=${this.selectedCountryId}`;
        } else if (this.selectedContinentId) {
          apiUrl = `/posts?continente_id=${this.selectedContinentId}`;
        }

        const res = await api.get(apiUrl);
        console.log('[v0] Posts response:', res.data);

        if (Array.isArray(res.data)) {
          this.posts = res.data;
        } else if (res.data && Array.isArray(res.data.data)) {
          this.posts = res.data.data;
        } else {
          this.posts = [];
        }
      } catch (e) {
        console.error('Error fetching posts:', e);
        this.posts = [];
      } finally {
        this.isLoading = false;
      }
    },

    async fetchWeather() {
      // Weather API implementation
      try {
        const apiKey = "0c80d5976ca76722ed51673286e90cbe";
        let cityForWeather = 'São Paulo'; // Default city if no location is selected
        let countryForWeather = 'Brazil';

        if (this.selectedCityId) {
          const city = this.cities.find(c => c.id === this.selectedCityId);
          const country = this.countries.find(c => c.id === this.selectedCountryId);

          if (city) cityForWeather = city.nome;
          if (country) countryForWeather = country.nome;
        } else if (this.userCity) {
          // Fallback to user's city if no region is selected
          cityForWeather = this.userCity;
          countryForWeather = this.userCountry;
        }

        const res = await api.get(`https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(cityForWeather)},${encodeURIComponent(countryForWeather)}&appid=${apiKey}&units=metric&lang=pt`);

        this.weather.temp = res.data.main && res.data.main.temp ? Math.round(res.data.main.temp) : '--';
        this.weather.desc = res.data.weather && res.data.weather[0] && res.data.weather[0].description ? res.data.weather[0].description : 'Indisponível';
      } catch (e) {
        console.error('Error fetching weather:', e);
        this.weather = { temp: "--", desc: "Indisponível" };
      }
    },

    openCreate() {
      alert("Abrir modal de criação de post.");
    },

    async nav(page) {
      if (page === 'home') {
        this.sidebarStep = 'continent';
        this.selectedContinentId = '';
        this.selectedCountryId = '';
        this.selectedRegionId = '';
        this.selectedCityId = '';
        localStorage.removeItem('selectedRegionId');
        this.fetchPosts();
        this.fetchTvLink();
      } else if (page === 'region') {
        // If no city is selected, try to use user's default location
        if (this.userEmail && !this.isAdmin && !this.selectedCityId) {
          await this.fetchUser(); // Re-fetch user to ensure location data is loaded
          if (this.selectedCityId) {
            this.fetchPosts();
            this.fetchTvLink();
          } else {
            alert("Por favor, selecione sua localização.");
          }
        } else {
          this.fetchPosts();
          this.fetchTvLink();
        }
      } else {
        alert("Navegar para " + page);
      }
    },

    getCityName() {
      if (this.selectedCityId) {
        const city = this.cities.find(c => c.id === this.selectedCityId);
        return city ? city.nome : 'Cidade desconhecida';
      }
      return '';
    },

    resetToUserCity() {
      localStorage.removeItem('selectedRegionId');
      this.selectedCityId = this.userDefaultCityId || '';

      if (this.userDefaultCityId) {
        localStorage.setItem('selectedRegionId', this.userDefaultCityId);
      }

      this.fetchPosts();
      this.fetchWeather();
      this.fetchTvLink();
    },

    getPostPreview(text) {
      if (!text) return '';
      const maxLength = 150;
      if (text.length <= maxLength) return text;
      return text.substring(0, maxLength) + '...';
    },

    shouldShowReadMore(text) {
      if (!text) return false;
      return text.length > 150;
    },

    isPostExpanded(postId) {
      return this.expandedPosts.includes(postId);
    },

    togglePostExpansion(postId) {
      const index = this.expandedPosts.indexOf(postId);
      if (index > -1) {
        this.expandedPosts.splice(index, 1);
      } else {
        this.expandedPosts.push(postId);
      }
    },

    openCalendarModal() {
      this.showCalendarModal = true;
    },

    closeCalendarModal() {
      this.showCalendarModal = false;
    },

    previousMonth() {
      if (this.currentMonth === 0) {
        this.currentMonth = 11;
        this.currentYear--;
      } else {
        this.currentMonth--;
      }
    },

    nextMonth() {
      if (this.currentMonth === 11) {
        this.currentMonth = 0;
        this.currentYear++;
      } else {
        this.currentMonth++;
      }
    },

    selectDay(day) {
      if (day.day) {
        this.selectedDate = day.date;
      }
    },

    toggleMobileMenu() {
      this.showMobileMenu = !this.showMobileMenu;
    },

    closeMobileMenu() {
      this.showMobileMenu = false;
    },
  },

  async mounted() {
    console.log('[v0] Dashboard montado');
    await this.fetchContinents();
    await this.fetchUser();

    this.fetchPosts();
    this.fetchWeather();
    this.fetchTvLink();
  }
};
</script>

<style scoped>
/* Reduzindo altura do calendário e ajustando espaçamento dos posts */
/* Calendário fixo no topo com estilo azul - altura reduzida */
.calendar-fixed {
  position: fixed;
  top: 0;
  left: 0;
  right: 320px; /* Espaço para sidebar */
  background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
  z-index: 100;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  padding: 16px 20px; /* Reduzido de 20px para 16px */
}

.calendar-container {
  max-width: 600px;
  margin: 0 auto;
}

.calendar-month {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.calendar-nav-btn {
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid #fff;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  font-size: 1.8rem;
  color: #fff;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.calendar-nav-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.05);
}

.calendar-month-text {
  font-size: 1.4rem;
  font-weight: bold;
  color: #fff;
  letter-spacing: 1px;
}

.calendar-weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px;
  margin-bottom: 8px;
}

.calendar-weekday {
  text-align: center;
  font-weight: bold;
  color: #fff;
  font-size: 0.85rem; /* Reduzido de 0.9rem */
  padding: 6px 0; /* Reduzido de 8px */
}

.calendar-days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px; /* Reduzido de 6px */
}

.calendar-day {
  height: 40px; /* Altura fixa reduzida de ~60px para 40px */
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  font-size: 0.95rem; /* Reduzido de 1rem */
  color: #fff;
  cursor: pointer;
  transition: all 0.2s;
  background: rgba(255, 255, 255, 0.1);
  font-weight: 500;
}

.calendar-day:hover:not(.calendar-day-empty) {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.05);
}

.calendar-day-empty {
  cursor: default;
  background: transparent;
}

.calendar-day-today {
  background: #f44336;
  color: #fff;
  font-weight: bold;
  box-shadow: 0 2px 8px rgba(244, 67, 54, 0.4);
}

.calendar-day-selected {
  background: #fff;
  color: #2196f3;
  font-weight: bold;
  box-shadow: 0 2px 8px rgba(255, 255, 255, 0.4);
}

/* Removendo scroll separado e tornando layout fluido como Instagram */
.layout {
  display: flex;
  flex-direction: row;
  min-height: 100vh;
  background: #f5f5f5;
  position: relative;
}

/* Área de conteúdo agora começa após o calendário com altura reduzida (~220px) */
.content-area {
  flex: 1;
  background: #ffffff;
  padding: 0;
  margin-top: 220px; /* Reduzido de 280px para 220px */
  margin-right: 320px; /* Espaço para sidebar */
}

.hero {
  background: #2196f3;
  padding: 0;
  position: relative;
}

.tv-post {
  background: #000;
  color: #fff;
  padding: 16px 0;
  text-align: center;
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
  bottom: -60px;
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

/* Posts agora têm margem superior adequada e não passam por trás do calendário */
.posts-list {
  background: #fff;
  padding: 80px 16px 80px 16px; /* Aumentado padding-top de 20px para 80px */
  position: relative;
  min-height: calc(100vh - 220px); /* Ajustado de 280px para 220px */
}

.loading, .no-posts {
  text-align: center;
  padding: 60px 20px;
  color: #666;
  font-size: 1.1rem;
}

/* Melhorando card do post com espaçamento e borda visível */
.post-item {
  background: #fff;
  border-radius: 12px;
  margin: 20px auto;
  padding: 0;
  max-width: 600px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.12); /* Sombra mais visível */
  border: 1px solid #e0e0e0;
  overflow: hidden;
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid #f0f0f0;
}

.post-user {
  font-weight: 600;
  color: #222;
  font-size: 0.95rem;
}

.post-date {
  color: #999;
  font-size: 0.85rem;
}

/* Imagem do post sem padding para layout mais limpo */
.post-media {
  width: 100%;
  background: #000;
  line-height: 0;
}

/* Imagem do post com altura máxima para evitar quebra de layout */
.post-media img {
  width: 100%;
  max-height: 500px; /* Reduzido de 600px para 500px */
  object-fit: cover;
  display: block;
}

/* Conteúdo do post com padding adequado */
.post-content {
  padding: 12px 16px;
  color: #333;
  font-size: 0.95rem;
  line-height: 1.5;
  white-space: pre-wrap; /* Preserva quebras de linha */
  word-wrap: break-word; /* Quebra palavras longas */
}

/* Botão "Ver mais" */
.read-more-btn {
  background: none;
  border: none;
  color: #2196f3;
  cursor: pointer;
  font-size: 0.9rem;
  padding: 4px 0;
  margin-top: 4px;
  font-weight: 500;
}

.read-more-btn:hover {
  text-decoration: underline;
}

/* Ações do post com melhor espaçamento */
.post-actions {
  padding: 8px 16px;
  border-top: 1px solid #f0f0f0;
  border-bottom: 1px solid #f0f0f0;
}

.post-likes {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.9rem;
}

.like-icon {
  font-size: 1.2rem;
}

.like-count {
  color: #666;
  font-weight: 500;
}

/* Comentários com melhor layout */
.post-comments {
  padding: 12px 16px;
  background: #fafafa;
}

.comments-title {
  font-weight: 600;
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 12px;
}

.comment-item {
  margin-bottom: 12px;
  padding: 8px 0;
}

.comment-user {
  font-weight: 600;
  color: #222;
  font-size: 0.9rem;
  margin-bottom: 4px;
}

.comment-body {
  color: #444;
  font-size: 0.9rem;
  line-height: 1.4;
}


/* Menu lateral fixo à direita */
.right-sidebar {
  width: 320px;
  background: #2196f3;
  color: #fff;
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
  border-left: 2px solid #1976d2;
  position: fixed;
  top: 0;
  right: 0;
  z-index: 50;
}

.weather {
  background: #1976d2;
  color: #fff;
  text-align: center;
  padding: 20px;
  border-bottom: 2px solid #1565c0;
}

.weather-icon {
  font-size: 2.5rem;
  margin-bottom: 8px;
}

.weather-temp {
  font-size: 1.8rem;
  font-weight: bold;
  margin-bottom: 4px;
}

.weather-desc {
  font-size: 1rem;
  text-transform: capitalize;
}

/* Área de menu com scroll interno */
.location-menu {
  flex: 1;
  overflow-y: auto; /* Permite scroll vertical */
  padding: 16px 12px;
  max-height: calc(100vh - 150px); /* Altura máxima menos o espaço do weather */
}

.menu-section {
  margin-bottom: 20px;
}

.menu-title {
  font-size: 1.2rem;
  font-weight: bold;
  color: #fff;
  margin-bottom: 12px;
  padding: 10px 12px;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 8px;
  text-align: center;
}

/* Alterado grid para coluna única e estilo azul com fundo preto */
.checkbox-grid-sidebar {
  display: grid;
  grid-template-columns: 1fr; /* Coluna única */
  gap: 8px;
}

.checkbox-item-sidebar {
  display: flex;
  align-items: center;
  /* Invertendo cores: fundo azul e texto preto */
  background: #2196f3; /* Fundo azul */
  color: #000; /* Texto preto */
  border: 2px solid #1976d2; /* Borda azul escuro */
  border-radius: 6px;
  padding: 12px 10px;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.95rem;
}

.checkbox-item-sidebar:hover {
  background: #42a5f5; /* Azul mais claro no hover */
  border-color: #1565c0;
  transform: scale(1.02);
}

.checkbox-item-sidebar input[type="checkbox"] {
  margin-right: 8px;
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #000; /* Checkbox preto */
}

.checkbox-label-sidebar {
  flex: 1;
  font-weight: 600;
  cursor: pointer;
  color: #000; /* Texto preto */
}

.back-btn-sidebar {
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
  border: 2px solid #fff;
  border-radius: 6px;
  padding: 8px 16px;
  font-size: 0.95rem;
  font-weight: bold;
  cursor: pointer;
  margin-bottom: 12px;
  width: 100%;
  transition: all 0.2s;
}

.back-btn-sidebar:hover {
  background: rgba(255, 255, 255, 0.3);
}

/* Botão flutuante para abrir menu em mobile (oculto em desktop) */
.mobile-menu-btn {
  display: none;
  position: fixed;
  top: 16px;
  right: 16px;
  background: #2196f3;
  color: #fff;
  border: none;
  border-radius: 8px;
  width: 48px;
  height: 48px;
  font-size: 1.8rem;
  cursor: pointer;
  z-index: 101;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  transition: all 0.2s;
}

.mobile-menu-btn:hover {
  background: #1976d2;
  transform: scale(1.05);
}

/* Modal de menu para mobile */
.mobile-menu-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.6);
  z-index: 1001;
}

.mobile-menu-modal {
  position: fixed;
  top: 0;
  right: 0;
  width: 80%;
  max-width: 320px;
  height: 100vh;
  background: #2196f3;
  box-shadow: -4px 0 16px rgba(0,0,0,0.3);
  overflow-y: auto;
}

.mobile-menu-header {
  background: #1976d2;
  color: #fff;
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #1565c0;
}

.mobile-menu-header h3 {
  font-size: 1.2rem;
  margin: 0;
}

.mobile-menu-close {
  background: none;
  border: none;
  color: #fff;
  font-size: 1.8rem;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mobile-menu-body {
  padding: 16px;
}

/* Barra de navegação inferior com z-index alto e botão "+" estilizado */
.bottom-nav {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100vw;
  background: #fff;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 12px 0;
  z-index: 200; /* Z-index alto para ficar acima do menu lateral */
  box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
  border-top: 2px solid #e0e0e0;
}

.bottom-nav button {
  background: #2196f3;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  margin: 0 4px;
  transition: background 0.2s;
}

.bottom-nav button:hover {
  background: #1976d2;
}

/* Botão "+" estilizado entre Home e Região */
.add-post-btn {
  background: #f44336 !important;
  border-radius: 50% !important;
  width: 56px !important;
  height: 56px !important;
  font-size: 2rem !important;
  padding: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-shadow: 0 4px 12px rgba(244, 67, 54, 0.4) !important;
}

.add-post-btn:hover {
  background: #d32f2f !important;
  transform: scale(1.05);
}

/* Banner informativo mais fluido */
.city-info-banner {
  background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
  color: white;
  padding: 16px 20px;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  margin-bottom: 20px;
  border-radius: 8px;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.city-info-text {
  flex: 1;
  font-size: 0.95rem;
  word-wrap: break-word;
  min-width: 200px;
}

.city-info-btn {
  background: white;
  color: #2196f3;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.2s;
  white-space: nowrap;
}

.city-info-btn:hover {
  background: #f5f5f5;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Responsivo com layout fluido */
@media (max-width: 768px) {
  .layout {
    flex-direction: column;
  }

  .calendar-fixed {
    right: 0; /* Ocupa largura total em mobile */
    padding: 12px 16px; /* Padding reduzido em mobile */
  }

  .content-area {
    margin-right: 0;
    margin-top: 200px; /* Ajustado para mobile */
  }

  .right-sidebar {
    display: none;
  }

  .mobile-menu-btn {
    display: flex;
  }

  .mobile-menu-overlay {
    display: block;
  }

  .post-item {
    margin: 16px 12px;
    border-radius: 8px;
  }

  .city-info-banner {
    margin: 0 12px 16px 12px;
    border-radius: 8px;
  }
}
</style>
