<template>
  <div class="auth-wrapper">
    <div class="auth-logo">
      <img :src="logo" alt="Logo" />
    </div>
    <h2 class="auth-title">Faça login para continuar</h2>
    <form class="auth-form" @submit.prevent="login">
      <input v-model="email" type="email" placeholder="Email" class="auth-input" required />
      <input v-model="password" type="password" placeholder="Senha" class="auth-input" required />
      <button type="submit" class="auth-btn">Entrar</button>
    </form>
    <div class="auth-links">
      <span>ou</span>
      <button type="button" class="auth-btn-outline" @click="goRegister">Crie sua conta</button>
      <button type="button" class="auth-link" @click="forgotPassword">Esqueci minha senha</button>
    </div>
    <div class="auth-footer">
      <a href="#" class="auth-footer-link">Termos de Uso</a>
      <a href="#" class="auth-footer-link">Política de Privacidade</a>
      <div class="auth-copyright">© 2025 Sua Região. Todos os direitos reservados.</div>
    </div>
    <p v-if="error" class="auth-error">{{ error }}</p>
  </div>
</template>

<script>
import api from './services/api.js';
import logo from './assets/logo.png';

export default {
  data() {
    return {
      email: '',
      password: '',
      error: '',
      logo
    };
  },
  methods: {
    async login() {
      try {
        const res = await api.post('/auth/login', {
          email: this.email,
          password: this.password
        });
        localStorage.setItem('token', res.data.token);
        this.$emit('login-success');
      } catch (err) {
        this.error = err.response?.data?.message || 'Credenciais inválidas';
      }
    },
    forgotPassword() {
      this.$emit('go-forgot-password');
    },
    goRegister() {
      this.$emit('go-register');
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
.auth-logo img {
  display: block;
  margin: 0 auto 18px auto;
  max-width: 220px;
  height: auto;
}
.auth-title {
  font-size: 1.35rem;
  font-weight: 700;
  margin-bottom: 24px;
  color: #111;
  text-align: center;
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
.auth-btn-outline {
  width: 100%;
  padding: 14px 0;
  background: #fff;
  color: #2196f3;
  font-size: 1.15rem;
  font-weight: 700;
  border: 2px solid #2196f3;
  border-radius: 28px;
  margin-top: 8px;
  margin-bottom: 0;
  cursor: pointer;
  box-shadow: none;
}
.auth-links {
  width: 100%;
  text-align: center;
  margin: 18px 0 0 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
  align-items: center;
}
.auth-link {
  background: none;
  border: none;
  color: #111;
  font-size: 1.05rem;
  font-weight: 500;
  text-decoration: underline;
  cursor: pointer;
  margin-top: 8px;
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
.auth-footer-link {
  color: #2196f3;
  font-size: 1rem;
  text-decoration: underline;
  font-weight: 500;
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

/* Logo */
.logo {
  display: block;
  margin: 0 auto 20px auto; /* centraliza e adiciona espaço abaixo */
  max-width: 200px;         /* aumenta ou diminui a logo */
  height: auto;
}

/* Título */
/* h2 removido para evitar texto branco sobre fundo branco */

/* Campos de input */
input {
  display: block;
  width: 90%;
  max-width: 300px;
  margin: 10px auto; /* centraliza horizontalmente */
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #000;
  font-size: 1em;
}

/* Botões */
button {
  display: block;
  width: 90%;
  max-width: 300px;
  margin: 10px auto; /* centraliza horizontalmente */
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

/* Links adicionais */
.login-links {
  display: flex;
  justify-content: space-between;
  margin-top: 1rem;
}

.login-links button {
  background: none;
  border: none;
  color: #fff;
  cursor: pointer;
  text-decoration: underline;
  font-size: 0.9em;
}

/* Mensagens */
.error {
  color: #ff6666;
  margin-top: 1rem;
}

.success {
  color: #66ff66;
  margin-top: 1rem;
}

@media (max-width: 480px) {
  .auth-wrapper {
    padding: 24px 12px;
  }
  .auth-input, .auth-btn, .auth-btn-outline {
    width: 100%;
    max-width: 100%;
    font-size: 1rem;
    padding: 12px;
  }
  .auth-title {
    font-size: 1.2rem;
  }
  .auth-footer {
    gap: 12px;
  }
}
</style>
