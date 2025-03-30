<template>
  <BaseLayout>
    <div class="container mt-5">
      <h2 class="text-center mb-4">Gerenciar Clientes</h2>
      
      <!-- Botão para adicionar novo cliente -->
      <div class="text-end mb-3">
        <button class="btn btn-success" @click="openModal(null)">Adicionar Cliente</button>
      </div>
      
      <!-- Tabela de clientes -->
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="client in clients" :key="client.id">
            <td>{{ client.id }}</td>
            <td>{{ client.name }}</td>
            <td>{{ client.email }}</td>
            <td>
              <button class="btn btn-primary btn-sm me-2" @click="openModal(client)">Editar</button>
              <button class="btn btn-danger btn-sm" @click="deleteClient(client.id)">Excluir</button>
            </td>
          </tr>
          <tr v-if="clients.length === 0">
            <td colspan="4" class="text-center">Nenhum cliente encontrado</td>
          </tr>
        </tbody>
      </table>
  
      <!-- Modal para criação/edição de cliente -->
      <div v-if="showModal" class="modal fade show d-block" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ editingClient ? 'Editar Cliente' : 'Novo Cliente' }}</h5>
              <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="saveClient">
                <div class="mb-3">
                  <label class="form-label">Nome</label>
                  <input type="text" v-model="form.name" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">E-mail</label>
                  <input type="email" v-model="form.email" class="form-control" required />
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BaseLayout>
</template>

<script>
import api from '@/services/api'
import BaseLayout from "../layouts/BaseLayout.vue"

export default {
  components: {
    BaseLayout
  },
  data() {
    return {
      clients: [],
      showModal: false,
      editingClient: null,
      form: { name: "", email: "" }
    };
  },
  methods: {
    async fetchClients() {
      try {
        const response = await api.get("/clients");
        this.clients = response.data;
      } catch (error) {
        console.error("Erro ao buscar clientes:", error);
      }
    },
    openModal(client) {
      if (client) {
        this.editingClient = client.id;
        this.form = { ...client };
      } else {
        this.editingClient = null;
        this.form = { name: "", email: "" };
      }
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    async saveClient() {
      try {
        if (this.editingClient) {
          await api.put(`/clients/${this.editingClient}`, this.form);
        } else {
          await api.post("/clients", this.form);
        }
        this.closeModal();
        this.fetchClients();
      } catch (error) {
        console.error("Erro ao salvar cliente:", error);
      }
    },
    async deleteClient(id) {
      if (confirm("Tem certeza que deseja excluir este cliente?")) {
        try {
          await api.delete(`/clients/${id}`);
          this.fetchClients();
        } catch (error) {
          console.error("Erro ao excluir cliente:", error);
        }
      }
    }
  },
  mounted() {
    this.fetchClients();
  }
};
</script>

<style scoped>
.modal {
  background: rgba(0, 0, 0, 0.5);
}
</style>
