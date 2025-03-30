<template>
  <BaseLayout>
    <div class="container mt-4">
    
      <Notification
        v-if="notification.visible"
        :message="notification.message"
        :type="notification.type"
      />

      <h2 class="mb-4 text-center">📅 Meu Dashboard</h2>

      <!-- Resumo -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card shadow-sm p-3 text-center">
            <h4 class="mb-0">👥 {{ totalClients }}</h4>
            <p>Clientes Cadastrados</p>
            <button class="btn btn-primary w-100 mt-2" @click="goToClients">➕ Cadastrar Cliente</button>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card shadow-sm p-3 text-center">
            <h4 class="mb-0">📅 {{ upcomingMeetings.length }}</h4>
            <p>Reuniões Agendadas</p>
            <button class="btn btn-primary w-100 mt-2" @click="goToSchedule">➕ Agendar Reunião</button>
          </div>
        </div>
      </div>

      <!-- Próximas reuniões -->
      <div class="card shadow-sm p-3 mb-4">
        <h4 class="mb-3">📌 Próximas Reuniões</h4>

        <table class="table table-hover">
          <thead>
            <tr>
              <th>Cliente</th>
              <th>Data</th>
              <th>Horário</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="meeting in upcomingMeetings" :key="meeting.id">
              <td>{{ meeting.client.name }}</td>
              <td>{{ formatDate(meeting.scheduled_at) }}</td>
              <td>{{ formatTime(meeting.scheduled_at) }}</td>
              <td>
                <button class="btn btn-sm btn-outline-primary me-2" @click="openEditModal(meeting)">
                  ✏️ Editar
                </button>
                <button class="btn btn-sm btn-outline-danger" @click="cancelMeeting(meeting.id)">
                  ❌ Cancelar
                </button>
              </td>
            </tr>
            <tr v-if="upcomingMeetings.length === 0">
              <td colspan="4" class="text-center">Nenhuma reunião agendada</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal para edição de reunião -->
      <div v-if="showEditModal" class="modal fade show d-block" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Editar Reunião</h5>
              <button type="button" class="btn-close" @click="closeEditModal"></button>
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
                  <label class="form-label">Data</label>
                  <input type="datetime-local" v-model="form.scheduled_at" class="form-control" required />
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

      <!-- Botão de Logout -->
      <div class="text-center">
        <button class="btn btn-danger" @click="logout">Logout</button>
      </div>
    </div>
  </BaseLayout>
</template>

<script>
import api from '@/services/api'
import BaseLayout from "@/layouts/BaseLayout.vue";
import Notification from "@/components/Notification.vue";
import { initEcho } from "@/echo";

export default {
  components: {
    BaseLayout,
    Notification,
  },
  data() {
    return {
      totalClients: 0,
      upcomingMeetings: [],
      clients: [],
      showEditModal: false,
      editingMeeting: null,
      form: { client_id: "", scheduled_at: "", notes: "" },
      userId: null,
      notification: {
        visible: false,
        message: "",
        type: "info",
      },
      currentChannel: null,
    };
  },
  methods: {
    async fetchDashboardData() {
      try {
        // Buscar o número de clientes cadastrados
        const clientsResponse = await api.get("/clients");
        this.totalClients = clientsResponse.data.length;

        // Buscar as próximas reuniões agendadas
        const meetingsResponse = await api.get("/meetings");
        this.upcomingMeetings = meetingsResponse.data;

        // Buscar os clientes para o modal de edição
        this.clients = clientsResponse.data;

      } catch (error) {
        console.error("Erro ao buscar dados do dashboard:", error);
      }
    },
    async cancelMeeting(meetingId) {
      if (!confirm("Tem certeza que deseja cancelar esta reunião?")) return;

      try {
        await api.delete(`/meetings/${meetingId}`);
        this.upcomingMeetings = this.upcomingMeetings.filter(m => m.id !== meetingId);
      } catch (error) {
        console.error("Erro ao cancelar reunião:", error);
      }
    },
    openEditModal(meeting) {
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
      
      this.showEditModal = true;
    },
    closeEditModal() {
      this.showEditModal = false;
    },
    async saveMeeting() {
      try {
        // Create date in local timezone without adjustments
        const date = new Date(this.form.scheduled_at);
        
        const formData = {
          ...this.form,
          scheduled_at: date.toISOString()
        };

        await api.put(`/meetings/${this.editingMeeting}`, formData);
        this.closeEditModal();
        this.fetchDashboardData();
      } catch (error) {
        console.error("Erro ao salvar reunião:", error);
      }
    },
    goToSchedule() {
      this.$router.push("/schedule");
    },
    goToClients() {
      this.$router.push("/clients");
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('pt-BR');
    },
    formatTime(date) {
      return new Date(date).toLocaleTimeString('pt-BR', {
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    logout() {
      localStorage.removeItem("token");
      this.$router.push("/login");
    },
    showNotification(message, type = "info") {
      this.notification.message = message;
      this.notification.type = type;
      this.notification.visible = true;

      setTimeout(() => {
        this.notification.visible = false;
      }, 5000);
    },
    setupWebSocketListeners() {
      if (!this.userId) {
        console.error('User ID is not set');
        return;
      }

      try {
        console.log('Setting up WebSocket listeners for user:', this.userId);

        if (!window.echo) {
          console.log('Initializing Echo...');
          window.echo = initEcho();
        }

        if (!window.echo) {
          throw new Error('Failed to initialize Echo');
        }

        const channelName = `ricochet360-techassessment.${this.userId}`;
        console.log('Subscribing to channel:', channelName);
        
        // Leave existing channel if any
        if (this.currentChannel) {
          console.log('Leaving existing channel:', this.currentChannel);
          window.echo.leave(this.currentChannel);
        }

        // Subscribe to new channel
        const channel = window.echo.private(channelName);
        this.currentChannel = channelName;

        channel.listen('MeetingReminder', (e) => {
          console.log('Received reminder:', e);
          this.showNotification(
            `Lembrete: Você tem uma reunião com ${e.client_name} às ${this.formatTime(e.scheduled_at)}`,
            'info'
          );
        });

        // Add connection status handlers with more detailed logging
        if (window.echo.connector.pusher) {
          window.echo.connector.pusher.connection.bind('connecting', () => {
            console.log('WebSocket connecting...');
          });

          window.echo.connector.pusher.connection.bind('connected', () => {
            console.log('WebSocket connected successfully');
            this.showNotification('Conexão estabelecida com sucesso', 'success');
          });

          window.echo.connector.pusher.connection.bind('disconnected', () => {
            console.log('WebSocket disconnected');
            this.showNotification('Conexão perdida. Tentando reconectar...', 'error');
          });

          window.echo.connector.pusher.connection.bind('error', (err) => {
            console.error('WebSocket error:', err);
            this.showNotification('Erro na conexão WebSocket', 'error');
          });

          // Log current connection state
          console.log('Current connection state:', window.echo.connector.pusher.connection.state);
        }
      } catch (error) {
        console.error('Error setting up WebSocket:', error);
        this.showNotification('Erro ao configurar as notificações em tempo real.', 'error');
      }
    },

    async getUserId() {
      try {
        const response = await api.get("/user");
        this.userId = response.data.user.id;
        await this.setupWebSocketListeners();
      } catch (error) {
        console.error("Erro ao buscar ID do usuário:", error);
        this.showNotification("Erro ao buscar ID do usuário.", "error");
      }
    }
  },
  
  beforeDestroy() {
    if (window.echo && this.currentChannel) {
      window.echo.leave(this.currentChannel);
    }
  },
  mounted() {
    this.fetchDashboardData();
    this.getUserId();
  }
};
</script>

<style scoped>
.card {
  border-radius: 10px;
}
.modal {
  background: rgba(0, 0, 0, 0.5);
}
</style>