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
  <div class="min-h-screen flex">
    <!-- Left Side - Login Form -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-white dark:bg-gray-900">
      <div class="w-full max-w-md space-y-8">
        <!-- Header -->
        <div class="text-center">
          <div class="mx-auto h-20 w-20 bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-500 rounded-3xl flex items-center justify-center shadow-2xl mb-6 transform rotate-3 hover:rotate-0 transition-transform duration-300">
            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
          </div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
            Portal Konselor
          </h1>
          <p class="text-gray-600 dark:text-gray-400 text-lg">
            Sistem BK Online - Akses Profesional
          </p>
        </div>

        <!-- Login Form -->
        <Card class="border-0 shadow-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl">
          <form @submit.prevent="handleLogin">
            <CardHeader class="text-center pb-6">
              <CardTitle class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                Selamat Datang Kembali
              </CardTitle>
              <CardDescription class="text-gray-600 dark:text-gray-400">
                Masukkan kredensial Anda untuk mengakses dashboard konselor
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

              <!-- Email Input -->
              <div class="space-y-2">
                <Label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                  Email Address
                </Label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                  </div>
                  <Input 
                    id="email" 
                    v-model="credentials.email" 
                    type="email" 
                    placeholder="konselor@sekolah.edu" 
                    required 
                    class="pl-10 h-12 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>

              <!-- Password Input -->
              <div class="space-y-2">
                <Label for="password" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                  Password
                </Label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                  </div>
                  <Input 
                    id="password" 
                    v-model="credentials.password" 
                    type="password" 
                    placeholder="••••••••" 
                    required 
                    class="pl-10 h-12 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>

              <!-- Login Button -->
              <Button 
                type="submit" 
                :disabled="isLoading"
                class="w-full h-12 text-lg font-semibold bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-500 hover:from-indigo-600 hover:via-purple-700 hover:to-pink-600 text-white shadow-lg hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isLoading ? 'Memverifikasi...' : 'Masuk ke Dashboard' }}
              </Button>

              <!-- Divider -->
              <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                  <span class="px-4 bg-white dark:bg-gray-800 text-gray-500">atau</span>
                </div>
              </div>

              <!-- Student Login Link -->
              <div class="text-center">
                <RouterLink 
                  to="/" 
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-lg transition-colors duration-200"
                >
                  <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                  </svg>
                  Masuk sebagai Siswa
                </RouterLink>
              </div>
            </CardContent>
          </form>
        </Card>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
          <p>© 2024 Sistem BK Online. Semua hak dilindungi.</p>
        </div>
      </div>
    </div>

    <!-- Right Side - Visual -->
    <div class="hidden lg:flex lg:flex-1 relative bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-500 overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
          <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
          </pattern>
          <rect width="100" height="100" fill="url(#grid)" />
        </svg>
      </div>

      <!-- Floating Elements -->
      <div class="absolute top-20 left-20 w-32 h-32 bg-white/20 rounded-full blur-xl animate-pulse"></div>
      <div class="absolute bottom-32 right-16 w-40 h-40 bg-pink-300/30 rounded-full blur-2xl animate-pulse delay-700"></div>
      <div class="absolute top-1/3 right-1/3 w-24 h-24 bg-yellow-300/40 rounded-full blur-lg animate-pulse delay-1000"></div>

      <!-- Main Content -->
      <div class="relative z-10 flex flex-col justify-center items-center text-white px-12 text-center">
        <div class="mb-12">
          <div class="w-32 h-32 bg-white/20 rounded-3xl backdrop-blur-sm flex items-center justify-center mb-8 mx-auto transform hover:scale-105 transition-transform duration-300">
            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
          </div>
          
          <h2 class="text-5xl font-bold mb-6 leading-tight">
            Membangun<br />
            <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
              Masa Depan
            </span><br />
            Siswa
          </h2>
          
          <p class="text-xl text-indigo-100 mb-8 leading-relaxed max-w-md">
            Platform digital untuk konselor profesional dalam memberikan bimbingan dan konseling terbaik bagi siswa.
          </p>

          <!-- Features -->
          <div class="space-y-4 text-left max-w-sm mx-auto">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <p class="text-indigo-100">Kelola laporan siswa secara efisien</p>
            </div>
            
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <p class="text-indigo-100">Chat real-time dengan siswa</p>
            </div>
            
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <p class="text-indigo-100">Dashboard analitik lengkap</p>
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-6 w-full max-w-md">
          <div class="text-center">
            <div class="text-3xl font-bold text-yellow-300 mb-1">500+</div>
            <div class="text-sm text-indigo-200">Siswa Terdaftar</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-yellow-300 mb-1">98%</div>
            <div class="text-sm text-indigo-200">Kepuasan</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-yellow-300 mb-1">24/7</div>
            <div class="text-sm text-indigo-200">Support</div>
          </div>
        </div>
      </div>

      <!-- Bottom Wave -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-24" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg">
          <path d="M0,60 C300,120 900,0 1200,60 L1200,120 L0,120 Z" fill="white" fill-opacity="0.1"/>
        </svg>
      </div>
    </div>
  </div>
</template>