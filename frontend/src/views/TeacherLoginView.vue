<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { RouterLink } from 'vue-router';

const authStore = useAuthStore();
const credentials = ref({
  email: '',
  password: '',
});

const handleLogin = () => {
  if (credentials.value.email && credentials.value.password) {
    authStore.loginGuru(credentials.value);
  } else {
    alert('Email dan password tidak boleh kosong!');
  }
};
</script>

<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
      <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">Login Guru / Konselor</h1>
        <p class="mt-2 text-sm text-gray-600">
          Masuk ke dasbor manajemen Anda.
        </p>
      </div>
      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label for="email" class="sr-only">Email</label>
          <input
            id="email"
            v-model="credentials.email"
            name="email"
            type="email"
            autocomplete="email"
            required
            class="block w-full px-3 py-2 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            placeholder="Alamat Email"
          />
        </div>

        <div>
          <label for="password" class="sr-only">Password</label>
          <input
            id="password"
            v-model="credentials.password"
            name="password"
            type="password"
            autocomplete="current-password"
            required
            class="block w-full px-3 py-2 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            placeholder="Password"
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
        <RouterLink to="/" class="font-medium text-indigo-600 hover:text-indigo-500">
          Masuk sebagai Siswa
        </RouterLink>
      </div>
    </div>
  </div>
</template>
