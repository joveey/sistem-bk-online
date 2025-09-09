<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { RouterLink } from 'vue-router';

const authStore = useAuthStore();
const uniqueCode = ref('');

const handleLogin = () => {
  if (uniqueCode.value.trim()) {
    authStore.loginSiswa(uniqueCode.value);
  } else {
    alert('Kode unik tidak boleh kosong!');
  }
};
</script>

<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
      <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">Login Siswa</h1>
        <p class="mt-2 text-sm text-gray-600">
          Masukkan kode unik Anda untuk memulai sesi konseling.
        </p>
      </div>
      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label for="unique_code" class="text-sm font-medium text-gray-700 sr-only">Kode Unik</label>
          <input
            id="unique_code"
            v-model="uniqueCode"
            name="unique_code"
            type="text"
            required
            class="block w-full px-3 py-2 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            placeholder="Masukkan Kode Unik Anda"
          />
        </div>

        <div>
          <button
            type="submit"
            class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Masuk
          </button>
        </div>
      </form>
      <div class="text-sm text-center">
        <RouterLink to="/guru/login" class="font-medium text-indigo-600 hover:text-indigo-500">
          Masuk sebagai Guru / Konselor
        </RouterLink>
      </div>
    </div>
  </div>
</template>
