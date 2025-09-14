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
  <div class="min-h-screen flex">
    <!-- Left Side - Login Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-white dark:bg-gray-900 relative overflow-hidden">
      <!-- Animated background elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-600/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-indigo-400/20 to-pink-600/20 rounded-full blur-3xl animate-pulse delay-700"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-gradient-to-r from-cyan-400/10 to-blue-600/10 rounded-full blur-2xl animate-pulse delay-1000"></div>
      </div>

      <div class="w-full max-w-lg space-y-8 relative z-10">
        <!-- Header -->
        <div class="text-center">
          <div class="mx-auto h-24 w-24 bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-600 rounded-3xl flex items-center justify-center shadow-2xl mb-6 transform hover:rotate-3 transition-all duration-300 hover:scale-105">
            <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
          </div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
            BK Online
          </h1>
          <p class="text-gray-600 dark:text-gray-400 text-lg">
            Portal Konseling Digital Siswa
          </p>
        </div>

        <!-- Login Form -->
        <Card class="border-0 shadow-2xl bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl">
          <form @submit.prevent="handleLogin">
            <CardHeader class="text-center pb-6">
              <CardTitle class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                Selamat Datang!
              </CardTitle>
              <CardDescription class="text-gray-600 dark:text-gray-400 text-base">
                Masukkan kode unik untuk mengakses layanan konseling
              </CardDescription>
            </CardHeader>
            
            <CardContent class="space-y-6">
              <!-- Error Message -->
              <div v-if="error" class="p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-400 rounded-r-lg">
                <div class="flex items-center">
                  <svg class="h-5 w-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
                </div>
              </div>

              <!-- Code Input -->
              <div class="space-y-3">
                <Label for="unique_code" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                  Kode Unik Akses
                </Label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <Input
                    id="unique_code"
                    v-model="uniqueCode"
                    type="text"
                    required
                    placeholder="Masukkan kode unik Anda"
                    class="pl-12 h-14 text-lg font-medium bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-xl"
                    :disabled="isLoading"
                  />
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                  <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Kode unik diberikan oleh konselor BK
                </p>
              </div>

              <!-- Login Button -->
              <Button
                type="submit"
                :disabled="isLoading || !uniqueCode.trim()"
                class="w-full h-14 text-lg font-semibold bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-600 hover:from-blue-600 hover:via-indigo-700 hover:to-purple-700 text-white shadow-xl hover:shadow-2xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl"
              >
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ isLoading ? 'Memverifikasi...' : 'Masuk ke Portal BK' }}
              </Button>

              <!-- Divider -->
              <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                  <span class="px-4 bg-white dark:bg-gray-800 text-gray-500">atau</span>
                </div>
              </div>

              <!-- Teacher Login Link -->
              <div class="text-center">
                <RouterLink 
                  to="/guru/login" 
                  class="inline-flex items-center px-6 py-3 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-xl transition-all duration-200 hover:scale-105"
                >
                  <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"/>
                  </svg>
                  Masuk sebagai Konselor
                </RouterLink>
              </div>
            </CardContent>
          </form>
        </Card>

        <!-- Footer -->
        <div class="text-center">
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
            Butuh bantuan? Hubungi konselor BK atau admin sekolah
          </p>
          <p class="text-xs text-gray-400 dark:text-gray-500">
            © 2024 BK Online - Sistem Konseling Digital
          </p>
        </div>
      </div>
    </div>

    <!-- Right Side - Visual -->
    <div class="hidden lg:flex lg:flex-1 relative bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-600 overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
          <pattern id="student-grid" width="10" height="10" patternUnits="userSpaceOnUse">
            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
          </pattern>
          <rect width="100" height="100" fill="url(#student-grid)" />
        </svg>
      </div>

      <!-- Floating Elements -->
      <div class="absolute top-16 left-16 w-40 h-40 bg-white/20 rounded-full blur-2xl animate-pulse"></div>
      <div class="absolute bottom-20 right-20 w-32 h-32 bg-yellow-300/30 rounded-full blur-xl animate-pulse delay-1000"></div>
      <div class="absolute top-1/3 right-1/4 w-24 h-24 bg-pink-300/40 rounded-full blur-lg animate-pulse delay-700"></div>

      <!-- Main Content -->
      <div class="relative z-10 flex flex-col justify-center items-center text-white px-12 text-center">
        <div class="mb-8">
          <!-- Icon -->
          <div class="w-40 h-40 bg-white/20 rounded-full backdrop-blur-sm flex items-center justify-center mb-8 mx-auto transform hover:scale-105 transition-all duration-300">
            <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
          </div>
          
          <h2 class="text-5xl font-bold mb-6 leading-tight">
            Ruang Aman<br />
            <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
              Untuk Bercerita
            </span>
          </h2>
          
          <p class="text-xl text-blue-100 mb-8 leading-relaxed max-w-lg">
            Tempat terpercaya untuk berbagi masalah, mendapatkan bimbingan, dan menemukan solusi bersama konselor profesional.
          </p>

          <!-- Features -->
          <div class="grid grid-cols-1 gap-4 text-left max-w-md mx-auto">
            <div class="flex items-center space-x-3 bg-white/10 rounded-lg p-3 backdrop-blur-sm">
              <div class="w-8 h-8 bg-green-400 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <div>
                <p class="font-medium text-white">Konseling 24/7</p>
                <p class="text-xs text-blue-100">Akses kapan saja dibutuhkan</p>
              </div>
            </div>
            
            <div class="flex items-center space-x-3 bg-white/10 rounded-lg p-3 backdrop-blur-sm">
              <div class="w-8 h-8 bg-purple-400 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2-2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <div>
                <p class="font-medium text-white">100% Rahasia</p>
                <p class="text-xs text-blue-100">Privasi terjamin</p>
              </div>
            </div>
            
            <div class="flex items-center space-x-3 bg-white/10 rounded-lg p-3 backdrop-blur-sm">
              <div class="w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
              </div>
              <div>
                <p class="font-medium text-white">Chat Real-time</p>
                <p class="text-xs text-blue-100">Komunikasi langsung</p>
              </div>
            </div>
          </div>
        </div>

        <!-- CTA Text -->
        <div class="text-center">
          <p class="text-lg text-blue-100 font-medium">
            Siap untuk memulai perubahan positif?
          </p>
          <p class="text-blue-200 text-sm mt-2">
            Dapatkan kode akses dari konselor BK Anda
          </p>
        </div>
      </div>

      <!-- Bottom Wave -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-32" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg">
          <path d="M0,60 C300,120 900,0 1200,60 L1200,120 L0,120 Z" fill="white" fill-opacity="0.1"/>
        </svg>
      </div>
    </div>
  </div>
</template>