<template>
  <div class="d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 350px;">
      <h2 class="text-center mb-3">Login</h2>
      
      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label class="form-label">E-mail</label>
          <input type="email" v-model="email" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">Senha</label>
          <input type="password" v-model="password" class="form-control" required />
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="isLoading">
          <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          <span v-if="!isLoading">Entrar</span>
        </button>
      </form>
      
      <p class="text-center mt-3">
        Não tem uma conta? <a href="/register">Cadastre-se</a>
      </p>

      <p v-if="errorMessage" class="text-danger text-center mt-3">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script>
import api from "@/services/api";

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
      isLoading: false
    };
  },
  methods: {
    async handleLogin() {
  this.isLoading = true;
  this.errorMessage = '';

  try {
    const response = await api.post("/login", {
      email: this.email,
      password: this.password,
    });

    // Log para verificar a resposta
    console.log("Resposta do login:", response);

    const token = response.data.token;

    // Log para verificar o token
    console.log("Token recebido:", token);

    // Armazenando o token no localStorage
    localStorage.setItem("token", token);
    console.log("Token armazenado no localStorage:", localStorage.getItem("token"));

    this.$router.push("/dashboard");

  } catch (error) {
    this.errorMessage = "Credenciais inválidas!";
    console.error("Erro de login:", error);
  } finally {
    this.isLoading = false;
  }
}

  }
};
</script>

<style scoped>
.card {
  border-radius: 10px;
}
.spinner-border {
  width: 1rem;
  height: 1rem;
}
</style>