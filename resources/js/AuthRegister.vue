<template>
  <div class="auth-bg">
    <div class="auth-wrapper">
      <div class="auth-header-row">
        <button class="auth-back" type="button" @click="$emit('go-login')">←</button>
        <span class="auth-header-title">Crie a sua conta</span>
      </div>
      <div class="auth-subtitle">Forneça todos os dados abaixo.</div>

      <form class="auth-form" @submit.prevent="register">
        <input v-model="name" type="text" placeholder="Nome" class="auth-input" required />
        <input v-model="email" type="email" placeholder="E-mail" class="auth-input" required />
        <input v-model="password" type="password" placeholder="Senha" class="auth-input" required />
        <input v-model="password_confirmation" type="password" placeholder="Confirme a senha" class="auth-input" required />

        <div class="auth-terms-row">
          <input type="checkbox" v-model="acceptTerms" id="acceptTerms" style="width:22px;height:22px;" />
          <label for="acceptTerms" class="auth-terms-label">
            Eu li e concordo com os <a href="#" class="auth-footer-link">Termos de Uso</a> e a 
            <a href="#" class="auth-footer-link">Política de Privacidade</a>
          </label>
        </div>

        <button type="submit" class="auth-btn">Criar Conta</button>
      </form>

      <div class="auth-footer">
        <div class="auth-copyright">© 2025 Sua Região. Todos os direitos reservados.</div>
      </div>

      <p v-if="error" class="auth-error">{{ error }}</p>
      <p v-if="success" class="auth-success">{{ success }}</p>
    </div>
  </div>
</template>

<script>
import api from './services/api.js';

export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      acceptTerms: false,
      error: '',
      success: ''
    };
  }, // 👈 vírgula aqui é essencial

  methods: {
    async register() {
      this.error = '';
      this.success = '';

      if (!this.acceptTerms) {
        this.error = 'Você deve aceitar os termos de uso para continuar.';
        return;
      }

      try {
        const res = await api.post('/auth/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
        });

        localStorage.setItem('token', res.data.token);
        this.success = 'Registro feito com sucesso! Você será redirecionado para login.';

        setTimeout(() => {
          this.$emit('go-login');
        }, 1500);

      } catch (err) {
        if (err.response && err.response.status === 422) {
          const errors = err.response.data.errors;
          if (Array.isArray(errors)) {
            this.error = errors.join(' ');
          } else {
            this.error = 'Erro no cadastro.';
          }
        } else {
          console.error('Erro completo:', err);
          this.error = err.response?.data?.message || err.response?.data?.error || 'Erro no cadastro.';
        }
      }
    }
  }
};
</script>

<style scoped>
body {
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
  width: 100%;
  max-width: 420px;
  margin: 0 auto;
  padding: 32px 16px 0 16px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  align-items: center;
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
.auth-terms-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}
.auth-terms-label {
  font-size: 1rem;
  color: #111;
}
.auth-footer-link {
  color: #2196f3;
  font-size: 1rem;
  text-decoration: underline;
  font-weight: 500;
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
</style>