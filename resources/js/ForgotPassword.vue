<template>
  <div class="auth-wrapper">
    <div class="auth-header-row">
      <button class="auth-back" type="button" @click="$emit('go-login')">←</button>
      <span class="auth-header-title">Recuperar senha</span>
    </div>
    <div class="auth-subtitle">Digite seu e-mail para receber o link de redefinição.</div>
    <form class="auth-form" @submit.prevent="sendResetLink">
      <input type="email" v-model="email" placeholder="E-mail" class="auth-input" required />
      <button type="submit" class="auth-btn">Enviar link de redefinição</button>
    </form>
    <div class="auth-footer">
      <div class="auth-copyright">© 2025 Sua Região. Todos os direitos reservados.</div>
    </div>
    <p v-if="message" class="auth-success">{{ message }}</p>
    <p v-if="error" class="auth-error">{{ error }}</p>
  </div>
</template>

<script>
import api from './services/api.js';

export default {
  data() {
    return {
      email: '',
      message: '',
      error: ''
    };
  },
  methods: {
    async sendResetLink() {
      try {
        const res = await api.post('/password/email', { email: this.email });
        this.message = res.data.message || 'Link enviado para seu email!';
        this.error = '';
      } catch (err) {
        this.error = err.response?.data?.message || 'Erro ao enviar link.';
      }
    }
  }
};
</script>

<style scoped>
html, body {
  background: #fff !important;
  font-family: Arial, Helvetica, sans-serif;
}
.auth-bg {
  background: #fff;
  min-height: 100vh;
  width: 100vw;
  display: flex;
  align-items: center;
  justify-content: center;
}
.auth-wrapper {
  max-width: 420px;
  margin: 0 auto;
  padding: 32px 18px 0 18px;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  background: #fff !important;
}
.auth-header-row {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}
.auth-back {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #111;
  cursor: pointer;
  margin-right: 8px;
}
.auth-header-title {
  font-size: 1.35rem;
  font-weight: 700;
  color: #111;
}
.auth-subtitle {
  width: 100%;
  text-align: left;
  color: #789;
  font-size: 1.05rem;
  margin-bottom: 18px;
}
.auth-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 18px;
  margin-bottom: 18px;
}
.auth-input {
  width: 100%;
  padding: 14px 18px;
  font-size: 1.1rem;
  border: 2px solid #111;
  border-radius: 28px;
  outline: none;
  margin-bottom: 0;
  background: #fff;
  color: #222;
}
.auth-btn {
  width: 100%;
  padding: 14px 0;
  background: #2196f3;
  color: #fff;
  font-size: 1.15rem;
  font-weight: 700;
  border: none;
  border-radius: 28px;
  margin-top: 8px;
  margin-bottom: 0;
  cursor: pointer;
  box-shadow: none;
}
.auth-footer {
  width: 100%;
  margin-top: 32px;
  display: flex;
  flex-direction: row;
  justify-content: center;
  gap: 18px;
  align-items: center;
  flex-wrap: wrap;
}
.auth-copyright {
  width: 100%;
  text-align: center;
  color: #888;
  font-size: 0.95rem;
  margin-top: 8px;
}
.auth-error {
  color: #d32f2f;
  font-size: 1rem;
  margin-top: 12px;
  text-align: center;
}
.auth-success {
  color: #2196f3;
  font-size: 1rem;
  margin-top: 12px;
  text-align: center;
}

.logo {
  display: block;
  margin: 0 auto 20px auto;
  max-width: 200px;
  height: auto;
}

/* h2 removido para evitar texto branco sobre fundo branco */

input {
  display: block;
  width: 90%;
  max-width: 300px;
  margin: 10px auto;
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #000;
  font-size: 1em;
}

button {
  display: block;
  width: 90%;
  max-width: 300px;
  margin: 10px auto;
  padding: 10px;
  border: none;
  border-radius: 5px;
  /* background-color removido para fundo branco */
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

/* bloco button:hover removido pois estava vazio */

.error {
  color: #ff6666;
  margin-top: 1rem;
}

.success {
  color: #66ff66;
  margin-top: 1rem;
}
</style>


