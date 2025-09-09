import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios'; // 1. Impor axios utama
import apiClient from '@/api/axios'; // Klien API kita tetap digunakan untuk yang lain

export const useAuthStore = defineStore('auth', () => {
    // === STATE ===
    const user = ref(JSON.parse(localStorage.getItem('user')) || null);
    const token = ref(localStorage.getItem('token') || null);
    const role = ref(localStorage.getItem('role') || null);
    const router = useRouter();

    // === GETTERS ===
    const isAuthenticated = computed(() => !!token.value);
    const isGuru = computed(() => role.value === 'counselor');
    const isSiswa = computed(() => role.value === 'student');

    // === ACTIONS ===
    function setAuthData(userData, userToken, userRole) {
        user.value = userData;
        token.value = userToken;
        role.value = userRole;
        localStorage.setItem('user', JSON.stringify(userData));
        localStorage.setItem('token', userToken);
        localStorage.setItem('role', userRole);
        apiClient.defaults.headers.common['Authorization'] = `Bearer ${userToken}`;
    }

    function clearAuthData() {
        user.value = null;
        token.value = null;
        role.value = null;
        localStorage.removeItem('user');
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        delete apiClient.defaults.headers.common['Authorization'];
    }

    /**
     * Menangani proses login untuk guru/konselor.
     * @param {object} credentials - { email, password }
     */
    async function loginGuru(credentials) {
        try {
            // --- PERBAIKAN UTAMA ADA DI SINI ---
            // 2. Gunakan 'axios' biasa dengan URL lengkap, BUKAN 'apiClient'
            await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
                withCredentials: true,
            });

            // 3. Gunakan 'apiClient' untuk permintaan login yang sebenarnya
            const response = await apiClient.post('/guru/login', credentials);
            
            const { user, token, user_type } = response.data;
            setAuthData(user, token, user_type);
            await router.push('/guru/dashboard');
        } catch (error) {
            console.error('Gagal login guru:', error);
            if (error.response) {
                alert(error.response.data.message || 'Kredensial tidak valid.');
            } else {
                alert('Tidak bisa terhubung ke server. Pastikan server backend berjalan.');
            }
        }
    }

    async function loginSiswa(uniqueCode) {
        const response = await apiClient.post('/student-login', { unique_code: uniqueCode });
        const { user, token, user_type } = response.data;
        setAuthData(user, token, user_type);
        await router.push('/dashboard');
    }

    async function fetchUser() {
        if (!token.value) return;
        try {
            const { data } = await apiClient.get('/user');
            user.value = data;
            localStorage.setItem('user', JSON.stringify(data));
        } catch (error) {
            clearAuthData();
            router.push('/');
        }
    }

    async function logout() {
        const lastRole = role.value;
        try {
            if (token.value) {
                await apiClient.post('/logout');
            }
        } catch (error) {
            console.error("Gagal logout di server, tetap logout di client.", error);
        } finally {
            clearAuthData();
            if (lastRole === 'counselor') {
                await router.push('/guru/login');
            } else {
                await router.push('/');
            }
        }
    }

    return {
        user, token, role, isAuthenticated, isGuru, isSiswa,
        loginGuru, loginSiswa, fetchUser, logout,
    };
});