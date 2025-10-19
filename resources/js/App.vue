<template>
  <div id="app">
    <auth-login 
      v-if="currentPage === 'login'" 
      @go-register="currentPage = 'register'" 
      @go-forgot-password="currentPage = 'forgot-password'"
      @login-success="goToDashboard"
    />
    <auth-register 
      v-if="currentPage === 'register'" 
      @go-login="currentPage = 'login'" 
    />
    <forgot-password 
      v-if="currentPage === 'forgot-password'" 
      @go-login="currentPage = 'login'" 
    />
    <reset-password 
      v-if="currentPage === 'reset-password'" 
      :resetToken="resetToken"
      :resetEmail="resetEmail"
      @go-login="currentPage = 'login'" 
    />
    <dashboard 
      v-if="currentPage === 'dashboard'" 
      @logout="currentPage = 'login'" 
      @go-admin-dashboard="currentPage = 'admindashboard'"
    />
    <admin-dashboard 
      v-if="currentPage === 'admindashboard'" 
      @go-dashboard="currentPage = 'dashboard'"
    />
  </div>
</template>

<script>
import AuthLogin from './AuthLogin.vue';
import AuthRegister from './AuthRegister.vue';
import ForgotPassword from './ForgotPassword.vue';
import ResetPassword from './ResetPassword.vue';
import Dashboard from './Dashboard.vue';
import AdminDashboard from './AdminDashboard.vue';

export default {
  components: { 
    AuthLogin, 
    AuthRegister, 
    ForgotPassword, 
    ResetPassword, 
    Dashboard,
    AdminDashboard
  },
  data() {
    return {
      currentPage: 'login',
      resetToken: '',
      resetEmail: ''
    };
  },
  mounted() {
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    const email = urlParams.get('email');

    if (window.location.pathname === '/reset-password' && token && email) {
      this.currentPage = 'reset-password';
      this.resetToken = token;
      this.resetEmail = email;
    }
  },
  methods: {
    goToDashboard() {
      this.currentPage = 'dashboard';
    }
  }
};
</script>

