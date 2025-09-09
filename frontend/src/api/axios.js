import axios from 'axios';
import { useAuthStore } from '@/stores/authStore';

const apiClient = axios.create({
    baseURL: 'http://localhost:8000/api', // Sesuaikan dengan URL backend Anda
    withCredentials: true,
});

// Interceptor untuk menambahkan token ke setiap request
apiClient.interceptors.request.use(config => {
    const authStore = useAuthStore();
    const token = authStore.token;
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => {
    return Promise.reject(error);
});

export default apiClient;
