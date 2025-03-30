import { defineStore } from "pinia";
import { ref } from "vue";

export const useAuthStore = defineStore("auth", () => {
    const token = ref(localStorage.getItem("token") || null);
    const user = ref(null);

    const setToken = (newToken) => {
        token.value = newToken;
        localStorage.setItem("token", newToken);
    };

    const removeToken = () => {
        token.value = null;
        user.value = null;
        localStorage.removeItem("token");
    };

    const logout = () => {
        removeToken();
    };

    return { token, user, setToken, removeToken, logout };
});
