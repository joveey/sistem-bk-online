<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { RouterLink } from 'vue-router';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
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
  <div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
    <Card class="w-full max-w-sm">
      <CardHeader>
        <CardTitle class="text-2xl">
          Login Konselor
        </CardTitle>
        <CardDescription>
          Masukkan kredensial Anda untuk masuk ke dasbor.
        </CardDescription>
      </CardHeader>
      <form @submit.prevent="handleLogin">
        <CardContent class="grid gap-4">
          <div v-if="error" class="p-3 text-center bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-md text-sm">
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
        <CardFooter class="flex flex-col gap-4">
          <Button class="w-full" type="submit" :disabled="isLoading">
            {{ isLoading ? 'Memproses...' : 'Masuk' }}
          </Button>
          <RouterLink to="/" class="text-sm text-center text-gray-500 hover:underline">
            Masuk sebagai Siswa
          </RouterLink>
        </CardFooter>
      </form>
    </Card>
  </div>
</template>