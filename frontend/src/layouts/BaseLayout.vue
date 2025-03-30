<template>
    <div class="layout">
      <!-- Sidebar -->
      <aside :class="{ collapsed: isCollapsed }">
        <button class="toggle-btn" @click="toggleSidebar">
          {{ isCollapsed ? "☰" : "✖" }}
        </button>
        <nav v-if="!isCollapsed">
          <router-link to="/dashboard">📊 Dashboard</router-link>
          <router-link to="/meetings">📅 Meetings</router-link>
          <router-link to="/clients">👥 Clients</router-link>
          <button @click="logout">🚪 Sair</button>
        </nav>
      </aside>
  
      <!-- Main Content -->
      <main>
      <keep-alive>
        <router-view v-slot="{ Component }">
          <component :is="Component" />
        </router-view>
      </keep-alive>
    </main>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/store/auth";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const isCollapsed = ref(false);
const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
};

const logout = () => {
  authStore.logout();
  router.push("/login");
};
</script>

<style scoped>
/* Layout Principal */
.layout {
  display: flex;
  height: 100vh;
}

/* Sidebar */
aside {
  width: 250px;
  background: #2c3e50;
  color: white;
  padding: 20px;
  transition: width 0.3s;
  display: flex;
  flex-direction: column;
  align-items: center;
}
aside.collapsed {
  width: 60px;
}
aside .toggle-btn {
  background: none;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
}
aside nav {
  display: flex;
  flex-direction: column;
  margin-top: 20px;
}
aside nav a {
  color: white;
  text-decoration: none;
  padding: 10px;
  display: block;
  width: 100%;
  text-align: center;
}
aside nav a:hover {
  background: #34495e;
}
aside button {
  margin-top: auto;
  background: #e74c3c;
  border: none;
  color: white;
  padding: 10px;
  cursor: pointer;
  width: 100%;
}
aside button:hover {
  background: #c0392b;
}

/* Conteúdo Principal */
main {
  flex-grow: 1;
  padding: 20px;
  background: #ecf0f1;
}
</style>
