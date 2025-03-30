<template>
  <BaseLayout>
    <div class="container mt-5">
      <h2 class="text-center mb-4">Gerenciar Reuniões</h2>
      
      <!-- Botão para adicionar nova reunião -->
      <div class="text-end mb-3">
        <button class="btn btn-success" @click="openModal(null)">Adicionar Reunião</button>
      </div>
      
      <!-- Tabela de reuniões -->
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="meeting in meetings" :key="meeting.id">
            <td>{{ meeting.id }}</td>
            <td>{{ meeting.client.name }}</td>
            <td>{{ formatDate(meeting.scheduled_at) }}</td>
            <td>
              <button class="btn btn-primary btn-sm me-2" @click="openModal(meeting)">Editar</button>
              <button class="btn btn-danger btn-sm" @click="deleteMeeting(meeting.id)">Excluir</button>
            </td>
          </tr>
          <tr v-if="meetings.length === 0">
            <td colspan="4" class="text-center">Nenhuma reunião encontrada</td>
          </tr>
        </tbody>
      </table>
  
      <!-- Modal para criação/edição de reunião -->
      <div v-if="showModal" class="modal fade show d-block" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ editingMeeting ? 'Editar Reunião' : 'Nova Reunião' }}</h5>
              <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="saveMeeting">
                <div class="mb-3">
                  <label class="form-label">Cliente</label>
                  <select v-model="form.client_id" class="form-control" required>
                    <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Data e Hora</label>
                  <input 
                    type="datetime-local" 
                    v-model="form.scheduled_at" 
                    class="form-control" 
                    required 
                  />
                </div>
                <div class="mb-3">
                  <label class="form-label">Notas</label>
                  <textarea v-model="form.notes" class="form-control"></textarea>
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
import BaseLayout from "../layouts/BaseLayout.vue";

export default {
  components: {
    BaseLayout,
  },
  data() {
    return {
      meetings: [],
      clients: [],
      showModal: false,
      editingMeeting: null,
      form: { client_id: "", scheduled_at: "", notes: "" },
    };
  },
  methods: {
    async fetchMeetings() {
      try {
        const response = await api.get("/meetings");
        this.meetings = response.data;
      } catch (error) {
        console.error("Erro ao buscar reuniões:", error);
      }
    },
    async fetchClients() {
      try {
        const response = await api.get("/clients");
        this.clients = response.data;
      } catch (error) {
        console.error("Erro ao buscar clientes:", error);
      }
    },
    openModal(meeting) {
      if (meeting) {
        this.editingMeeting = meeting.id;
        
        // Format the date directly for datetime-local input
        const date = new Date(meeting.scheduled_at);
        const formattedDate = date.getFullYear() + '-' +
          String(date.getMonth() + 1).padStart(2, '0') + '-' +
          String(date.getDate()).padStart(2, '0') + 'T' +
          String(date.getHours()).padStart(2, '0') + ':' +
          String(date.getMinutes()).padStart(2, '0');

        this.form = {
          ...meeting,
          scheduled_at: formattedDate
        };
      } else {
        this.editingMeeting = null;
        this.form = { client_id: "", scheduled_at: "", notes: "" };
      }
      this.showModal = true;
    },

    async saveMeeting() {
      try {
        // Create date without timezone adjustments
        const date = new Date(this.form.scheduled_at);
        
        const formData = {
          ...this.form,
          scheduled_at: date.toISOString()
        };

        if (this.editingMeeting) {
          await api.put(`/meetings/${this.editingMeeting}`, formData);
        } else {
          await api.post("/meetings", formData);
        }
        this.closeModal();
        this.fetchMeetings();
      } catch (error) {
        console.error("Erro ao salvar reunião:", error);
      }
    },
    async deleteMeeting(id) {
      if (confirm("Tem certeza que deseja excluir esta reunião?")) {
        try {
          await api.delete(`/meetings/${id}`);
          this.fetchMeetings();
        } catch (error) {
          console.error("Erro ao excluir reunião:", error);
        }
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleString("pt-BR", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        timeZone: "America/Sao_Paulo",
      });
    },
    closeModal() {
      this.showModal = false;
      this.form = { client_id: "", scheduled_at: "", notes: "" };
    },
  },
  mounted() {
    this.fetchMeetings();
    this.fetchClients();
  },
};
</script>

<style scoped>
.modal {
  background: rgba(0, 0, 0, 0.5);
}
</style>
