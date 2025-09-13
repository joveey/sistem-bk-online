<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { RouterLink } from 'vue-router';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const authStore = useAuthStore();
const credentials = ref({ email: '', password: '' });
const error = ref('');
const isLoading = ref(false);

const handleLogin = async () => {
  error.value = '';
  isLoading.value = true;
  try {
    await authStore.loginGuru(credentials.value);
  } catch (err) {
    error.value = err.response?.data?.message || 'Terjadi kesalahan saat mencoba login.';
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-50 via-indigo-50 to-blue-50 dark:from-gray-900 dark:via-purple-900/20 dark:to-blue-900/20 flex items-center justify-center p-4">
    <div class="absolute inset-0 overflow-hidden">
      <svg class="absolute left-[max(50%,25rem)] top-0 h-[64rem] w-[128rem] -translate-x-1/2 stroke-gray-200 [mask-image:radial-gradient(64rem_64rem_at_top,white,transparent)]" aria-hidden="true">
        <defs>
          <pattern id="e813992c-7d03-4cc4-a2bd-151760b470a0" width="200" height="200" x="50%" y="-1" patternUnits="userSpaceOnUse">
            <path d="M100 200V.5M.5 .5H200" fill="none" />
          </pattern>
        </defs>
        <rect width="100%" height="100%" stroke-width="0" fill="url(#e813992c-7d03-4cc4-a2bd-151760b470a0)" />
      </svg>
    </div>
    <div class="relative w-full max-w-md">
       <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
          <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"/>
          </svg>
        </div>
        <h1 class="mt-4 text-3xl font-bold bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-800 bg-clip-text text-transparent">
          Portal Konselor
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Sistem BK Online</p>
      </div>

      <Card class="border-0 shadow-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl">
        <CardHeader class="text-center">
          <CardTitle class="text-2xl font-bold text-gray-900 dark:text-gray-100">Login Guru / Konselor</CardTitle>
          <CardDescription class="text-gray-600 dark:text-gray-400">
            Masukkan kredensial Anda untuk mengakses dasbor.
          </CardDescription>
        </CardHeader>
        <form @submit.prevent="handleLogin">
          <CardContent class="grid gap-4 pt-6">
            <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-600 dark:text-red-400">
              {{ error }}
            </div>
            <div class="grid gap-2">
              <Label for="email">Email</Label>
              <Input id="email" v-model="credentials.email" type="email" placeholder="nama@email.com" required />
            </div>
            <div class="grid gap-2">
              <Label for="password">Password</Label>
              <Input id="password" v-model="credentials.password" type="password" required />
            </div>
          </CardContent>
          <CardFooter class="flex flex-col gap-4 pt-6">
            <Button class="w-full h-11 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white font-medium" type="submit" :disabled="isLoading">
              {{ isLoading ? 'Memproses...' : 'Masuk' }}
            </Button>
            <RouterLink to="/" class="text-sm text-center text-gray-500 hover:underline">
              Masuk sebagai Siswa
            </RouterLink>
          </CardFooter>
        </form>
      </Card>
    </div>
  </div>
</template>