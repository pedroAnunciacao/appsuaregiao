
<template>
  <div class="page">
    <div class="main-layout">
      <div class="main-left">
        <div class="page-header">Sua Vida</div>
        <div class="calendar-card">
          <div class="calendar-header">
            <div class="calendar-header-top">Sua Vida</div>
            <div class="calendar-header-nav">
              <button @click="prevMonth">&lt;</button>
              <div class="calendar-header-month">Novembro 2025</div>
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
              <tr>
                <td></td><td></td><td></td><td></td><td></td><td>1</td><td>2</td>
              </tr>
              <tr>
                <td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td>
              </tr>
              <tr>
                <td><span class="calendar-day-active">10</span></td><td>11</td><td>12</td>
                <td>13</td><td>14</td><td>15</td><td>16</td>
              </tr>
              <tr>
                <td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td>
              </tr>
              <tr>
                <td>24</td><td>25</td><td>26</td><td>27</td><td>28</td><td>29</td><td>30</td>
              </tr>
            </tbody>
          </table>

          <button class="btn-primary btn-publish" @click="novaPublicacao">
            Sua publicação
          </button>
        </div>

        <div class="section-title">Terra</div>
        <div class="section-text">
          Publique aqui textos, fotos e vídeos da sua cidade.
        </div>
      </div>

      <div class="main-right">
        <div class="side-list">
          <div class="side-item white-text" @click="$emit('go-pais')">América do Sul</div>
          <div class="side-item" @click="$emit('go-pais')">Brazil</div>
          <div class="side-item">Argentina</div>
          <div class="side-item">Aruba</div>
          <div class="side-item">Bolivia</div>
          <div class="side-item">Chile</div>
          <div class="side-item">Colombia</div>
          <div class="side-item">Ecuador</div>
          <div class="side-item">Falkland Islands (Malvinas)</div>
          <div class="side-item">French Guiana</div>
          <div class="side-item">Guyana</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardView',
  methods: {
    prevMonth() {
      alert('Navegação de meses será integrada pelo backend.')
    },
    nextMonth() {
      alert('Navegação de meses será integrada pelo backend.')
    },
    novaPublicacao() {
      alert('Ação de nova publicação será integrada no sistema.')
    }
  }
}
</script>
