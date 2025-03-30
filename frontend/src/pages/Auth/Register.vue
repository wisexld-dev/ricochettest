<template>
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="card p-4 shadow-lg" style="width: 350px;">
            <h2 class="text-center mb-3">Cadastro</h2>

            <form @submit.prevent="handleRegister">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" v-model="name" class="form-control" required />
                </div>

                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" v-model="email" class="form-control" required />
                </div>

                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" v-model="password" class="form-control" required />
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirme a Senha</label>
                    <input type="password" v-model="passwordConfirmation" class="form-control" required />
                </div>

                <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
            </form>

            <p class="text-center mt-3">
                Já tem uma conta? <a href="/login">Faça login</a>
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
            name: '',
            email: '',
            password: '',
            passwordConfirmation: '',
            errorMessage: ''
        };
    },
    methods: {
        async handleRegister() {
            if (this.password !== this.passwordConfirmation) {
                this.errorMessage = "As senhas não coincidem!";
                return;
            }

            try {
                const response = await api.post("/register", {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.passwordConfirmation,
                });

                alert("Conta criada com sucesso!");
                this.$router.push("/login");

            } catch (error) {
                this.errorMessage = "Erro ao registrar. Tente novamente." + error;
            }
        }
    }
};
</script>

<style scoped>
.card {
    border-radius: 10px;
}
</style>
