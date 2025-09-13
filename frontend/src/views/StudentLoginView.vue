<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { RouterLink } from 'vue-router';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const authStore = useAuthStore();
const uniqueCode = ref('');
const isLoading = ref(false);
const error = ref('');

const handleLogin = async () => {
  if (!uniqueCode.value.trim()) {
    error.value = 'Kode unik tidak boleh kosong!';
    return;
  }
  
  isLoading.value = true;
  error.value = '';
  
  try {
    await authStore.loginSiswa(uniqueCode.value);
  } catch (err) {
    error.value = err.response?.data?.message || 'Kode unik tidak valid atau terjadi kesalahan.';
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 dark:from-gray-900 dark:via-blue-900/20 dark:to-purple-900/20 flex items-center justify-center p-4">
    <!-- Background Pattern -->
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
      <!-- Logo Section -->
      <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
          <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
        </div>
        <h1 class="mt-4 text-3xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-800 bg-clip-text text-transparent">
          BK Online
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Portal Konseling Digital</p>
      </div>

      <!-- Login Card -->
      <Card class="border-0 shadow-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl">
        <CardHeader class="text-center pb-2">
          <CardTitle class="text-2xl font-bold text-gray-900 dark:text-gray-100">Login Siswa</CardTitle>
          <CardDescription class="text-gray-600 dark:text-gray-400">
            Masukkan kode unik yang diberikan konselor untuk memulai sesi konseling
          </CardDescription>
        </CardHeader>
        
        <CardContent class="pt-6">
          <form @submit.prevent="handleLogin" class="space-y-6">
            <!-- Error Message -->
            <div v-if="error" class="p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
              </div>
            </div>

            <!-- Input Field -->
            <div class="space-y-2">
              <Label for="unique_code" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Kode Unik Akses
              </Label>
              <div class="relative">
                <Input
                  id="unique_code"
                  v-model="uniqueCode"
                  type="text"
                  required
                  placeholder="Contoh: STU001, ABC123"
                  class="pl-10 h-12 text-lg tracking-wider font-mono bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :disabled="isLoading"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 01-2 2m2-2h-3m-6 0a2 2 0 01-2-2m0 0a2 2 0 012-2m-2 2h3m0 0V5a2 2 0 012-2m0 0h3"/>
                  </svg>
                </div>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                Tanyakan kode unik kepada konselor BK jika belum memiliki
              </p>
            </div>

            <!-- Login Button -->
            <Button
              type="submit"
              :disabled="isLoading || !uniqueCode.trim()"
              class="w-full h-12 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-medium shadow-lg disabled:from-gray-300 disabled:to-gray-400"
            >
              <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <svg v-else class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              {{ isLoading ? 'Memproses...' : 'Masuk ke Portal' }}
            </Button>
          </form>

          <!-- Info Cards -->
          <div class="mt-6 grid grid-cols-1 gap-3">
            <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-lg">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <div>
                  <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Akses Cepat</p>
                  <p class="text-xs text-blue-700 dark:text-blue-200">Login langsung tanpa password</p>
                </div>
              </div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/30 p-3 rounded-lg">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <div>
                  <p class="text-sm font-medium text-green-900 dark:text-green-100">Aman & Terpercaya</p>
                  <p class="text-xs text-green-700 dark:text-green-200">Data dijamin kerahasiaan</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer Links -->
          <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="text-center space-y-3">
              <RouterLink 
                to="/guru/login" 
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
              >
                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"/>
                </svg>
                Masuk sebagai Konselor
              </RouterLink>
              
              <div class="flex items-center justify-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                <span>© 2024 BK Online</span>
                <span>•</span>
                <span>Sistem Konseling Digital</span>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Additional Info -->
      <div class="mt-6 text-center">
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Butuh bantuan? Hubungi konselor BK atau admin sekolah
        </p>
      </div>
    </div>
  </div>
</template>