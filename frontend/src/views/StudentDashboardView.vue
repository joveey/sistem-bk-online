<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { useDataStore } from '@/stores/dataStore';
import { useRouter } from 'vue-router';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import MotivationalVideos from '@/components/MotivationalVideos.vue';

const authStore = useAuthStore();
const dataStore = useDataStore();
const router = useRouter();

const isModalOpen = ref(false);
const reportData = ref({
  title: '',
  description: '',
  type: 'online',
  is_anonymous: false,
});
const errorMessage = ref('');

const user = computed(() => authStore.user);
const reports = computed(() => dataStore.reports);
const isLoading = computed(() => dataStore.isLoading);

// Statistics for student
const totalReports = computed(() => reports.value.length);
const pendingReports = computed(() => reports.value.filter(r => r.status === 'pending').length);
const activeReports = computed(() => reports.value.filter(r => r.status === 'diterima').length);
const completedReports = computed(() => reports.value.filter(r => r.status === 'selesai').length);

onMounted(() => {
  dataStore.fetchReports();
});

const handleLogout = () => {
  authStore.logout();
};

const openCreateModal = () => {
  isModalOpen.value = true;
  errorMessage.value = '';
  reportData.value = {
    title: '',
    description: '',
    type: 'online',
    is_anonymous: false,
  };
};

const closeCreateModal = () => {
  isModalOpen.value = false;
  errorMessage.value = '';
};

const handleSubmit = async () => {
  errorMessage.value = '';
  try {
    await dataStore.createReport(reportData.value);
    closeCreateModal();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errorMessage.value = Object.values(error.response.data.errors).flat().join(' ');
    } else {
      errorMessage.value = 'Terjadi kesalahan saat mengirim laporan.';
    }
  }
};

const navigateToDetail = (reportId) => {
  router.push(`/konseling/${reportId}`);
};

const getStatusColor = (status) => {
  switch(status) {
    case 'pending': return 'text-amber-700 bg-amber-50 border-amber-200 dark:text-amber-300 dark:bg-amber-900/20 dark:border-amber-800';
    case 'diterima': return 'text-blue-700 bg-blue-50 border-blue-200 dark:text-blue-300 dark:bg-blue-900/20 dark:border-blue-800';
    case 'selesai': return 'text-green-700 bg-green-50 border-green-200 dark:text-green-300 dark:bg-green-900/20 dark:border-green-800';
    default: return 'text-gray-700 bg-gray-50 border-gray-200 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-700';
  }
};

const getStatusIcon = (status) => {
  switch(status) {
    case 'pending': return 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
    case 'diterima': return 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z';
    case 'selesai': return 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
    default: return 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
  }
};

const formatDate = (dateString) => {
  if (!dateString) return 'Baru saja';
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};

const greetingTime = computed(() => {
  const hour = new Date().getHours();
  if (hour < 12) return 'Selamat Pagi';
  if (hour < 17) return 'Selamat Siang';
  return 'Selamat Sore';
});

const currentTime = computed(() => {
  return new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-slate-900 dark:to-gray-800">
    <!-- Header dengan glassmorphism effect -->
    <header class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-b border-gray-200/50 dark:border-gray-700/50 sticky top-0 z-40 shadow-lg shadow-blue-500/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center space-x-4">
            <div class="h-12 w-12 bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
              <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">BK Online - Portal Siswa</h1>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ currentTime }}</p>
            </div>
          </div>
          <div class="flex items-center space-x-3">
            <Button @click="openCreateModal" class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-600 hover:from-blue-600 hover:via-indigo-700 hover:to-purple-700 text-white shadow-lg hover:shadow-xl transition-all duration-300">
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Buat Laporan
            </Button>
            <Button @click="handleLogout" variant="outline" class="border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/20">
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              Logout
            </Button>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      <!-- Hero Welcome Section -->
      <div class="relative bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-500 rounded-3xl p-8 text-white overflow-hidden shadow-2xl">
        <!-- Background Elements -->
        <div class="absolute inset-0">
          <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-20 translate-x-20 blur-3xl"></div>
          <div class="absolute bottom-0 left-0 w-32 h-32 bg-yellow-300/20 rounded-full translate-y-16 -translate-x-16 blur-2xl"></div>
          <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-white/5 rounded-full blur-3xl"></div>
        </div>
        
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
          <div>
            <div class="inline-flex items-center px-4 py-2 bg-white/20 rounded-full text-sm font-medium mb-4">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              {{ greetingTime }}
            </div>
            <h2 class="text-4xl font-bold mb-4 leading-tight">
              {{ user?.name }}! 👋
            </h2>
            <p class="text-xl text-indigo-100 mb-6 leading-relaxed">
              Bagaimana perasaanmu hari ini? Jangan ragu untuk berbagi jika ada yang ingin kamu ceritakan. Kami siap mendengarkan.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
              <Button @click="openCreateModal" size="lg" class="bg-white/20 hover:bg-white/30 text-white border-white/30 backdrop-blur-sm">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Mulai Konseling Sekarang
              </Button>
              <div class="flex items-center text-indigo-100">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm">Konseling tersedia 24/7</span>
              </div>
            </div>
          </div>
          
          <div class="hidden lg:flex justify-center">
            <div class="relative">
              <div class="w-64 h-64 bg-white/10 rounded-full backdrop-blur-sm flex items-center justify-center">
                <svg class="w-32 h-32 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
              </div>
              <div class="absolute -top-4 -right-4 w-8 h-8 bg-yellow-300 rounded-full animate-bounce"></div>
              <div class="absolute -bottom-4 -left-4 w-6 h-6 bg-pink-300 rounded-full animate-pulse"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards Grid -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <Card class="border-0 shadow-xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-1">Total Laporan</p>
                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ totalReports }}</p>
                <p class="text-xs text-blue-500 dark:text-blue-300 mt-1">Semua konseling</p>
              </div>
              <div class="p-3 bg-blue-500 rounded-2xl shadow-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="border-0 shadow-xl bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-amber-600 dark:text-amber-400 mb-1">Menunggu</p>
                <p class="text-3xl font-bold text-amber-900 dark:text-amber-100">{{ pendingReports }}</p>
                <p class="text-xs text-amber-500 dark:text-amber-300 mt-1">Perlu persetujuan</p>
              </div>
              <div class="p-3 bg-amber-500 rounded-2xl shadow-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="border-0 shadow-xl bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400 mb-1">Berlangsung</p>
                <p class="text-3xl font-bold text-emerald-900 dark:text-emerald-100">{{ activeReports }}</p>
                <p class="text-xs text-emerald-500 dark:text-emerald-300 mt-1">Sedang konseling</p>
              </div>
              <div class="p-3 bg-emerald-500 rounded-2xl shadow-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="border-0 shadow-xl bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-purple-600 dark:text-purple-400 mb-1">Selesai</p>
                <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ completedReports }}</p>
                <p class="text-xs text-purple-500 dark:text-purple-300 mt-1">Berhasil diselesaikan</p>
              </div>
              <div class="p-3 bg-purple-500 rounded-2xl shadow-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Reports History -->
        <div class="lg:col-span-2">
          <Card class="border-0 shadow-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl">
            <CardHeader class="pb-4">
              <div class="flex justify-between items-center">
                <div>
                  <CardTitle class="text-2xl text-gray-900 dark:text-gray-100 flex items-center">
                    <svg class="mr-3 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Riwayat Konseling
                  </CardTitle>
                  <CardDescription>Pantau perkembangan laporan konseling Anda</CardDescription>
                </div>
                <Button @click="openCreateModal" size="sm" variant="outline" class="border-indigo-200 text-indigo-600 hover:bg-indigo-50 dark:border-indigo-700 dark:text-indigo-400 dark:hover:bg-indigo-900/20">
                  <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                  Tambah Laporan
                </Button>
              </div>
            </CardHeader>
            
            <CardContent>
              <div v-if="isLoading" class="flex justify-center items-center py-16">
                <div class="text-center">
                  <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                  <span class="text-gray-600 dark:text-gray-400">Memuat laporan...</span>
                </div>
              </div>
              
              <div v-else-if="!reports.length" class="text-center py-16">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                  <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Belum ada laporan konseling</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Mulai dengan membuat laporan konseling pertama Anda</p>
                <Button @click="openCreateModal" class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg">
                  <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                  Buat Laporan Pertama
                </Button>
              </div>

              <div v-else class="space-y-4">
                <div
                  v-for="report in reports"
                  :key="report.id"
                  @click="navigateToDetail(report.id)"
                  class="p-6 bg-gradient-to-r from-white to-gray-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl border border-gray-200 dark:border-gray-600 hover:border-indigo-300 dark:hover:border-indigo-500 hover:shadow-xl transition-all duration-300 cursor-pointer group transform hover:-translate-y-1"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center space-x-3 mb-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                          {{ report.title }}
                        </h3>
                        <span :class="['inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border', getStatusColor(report.status)]">
                          <svg :class="'w-3 h-3 mr-1'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStatusIcon(report.status)"/>
                          </svg>
                          {{ report.status }}
                        </span>
                      </div>
                      
                      <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2 leading-relaxed">
                        {{ report.description }}
                      </p>
                      
                      <div class="flex items-center space-x-6 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                          </svg>
                          {{ formatDate(report.created_at) }}
                        </div>
                        <div class="flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                          </svg>
                          {{ report.type === 'online' ? 'Online Chat' : 'Tatap Muka' }}
                        </div>
                        <div v-if="report.is_anonymous" class="flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2-2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                          </svg>
                          Anonim
                        </div>
                      </div>
                    </div>
                    
                    <div class="ml-4 opacity-0 group-hover:opacity-100 transition-opacity">
                      <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Quick Actions Sidebar -->
        <div class="space-y-6">
          <!-- Quick Actions Card -->
          <Card class="border-0 shadow-xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl">
            <CardHeader>
              <CardTitle class="text-lg text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="mr-2 h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Aksi Cepat
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
              <Button @click="openCreateModal" class="w-full justify-start bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 text-blue-700 border-blue-200 dark:from-blue-900/20 dark:to-indigo-900/20 dark:text-blue-400 dark:border-blue-800">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Konseling Baru
              </Button>
              
              <Button v-if="activeReports > 0" @click="navigateToDetail(reports.find(r => r.status === 'diterima')?.id)" variant="outline" class="w-full justify-start border-green-200 text-green-700 hover:bg-green-50 dark:border-green-800 dark:text-green-400 dark:hover:bg-green-900/20">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Lanjutkan Chat
              </Button>
            </CardContent>
          </Card>

          <!-- Tips Card -->
          <Card class="border-0 shadow-xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20">
            <CardHeader>
              <CardTitle class="text-lg text-green-800 dark:text-green-400 flex items-center">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Tips Hari Ini
              </CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-sm text-green-700 dark:text-green-300 leading-relaxed">
                "Jangan takut untuk memulai percakapan. Setiap masalah besar dimulai dari langkah kecil untuk mengatasinya. Kami di sini untuk mendengarkan dan membantu."
              </p>
            </CardContent>
          </Card>

          <!-- Contact Info -->
          <Card class="border-0 shadow-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20">
            <CardHeader>
              <CardTitle class="text-lg text-blue-800 dark:text-blue-400 flex items-center">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Butuh Bantuan?
              </CardTitle>
            </CardHeader>
            <CardContent class="text-sm text-blue-700 dark:text-blue-300 space-y-2">
              <p>📍 Ruang BK - Gedung Utama Lt.2</p>
              <p>🕒 Senin-Jumat: 08.00-15.00</p>
              <p>📞 Emergency: 24/7 Online</p>
            </CardContent>
          </Card>
        </div>
      </div>

      <!-- Motivational Videos Section -->
      <div>
        <MotivationalVideos />
      </div>
    </main>

    <!-- Create Report Modal -->
    <Dialog v-model:open="isModalOpen">
      <DialogContent class="sm:max-w-[600px] max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle class="text-xl flex items-center">
            <svg class="mr-2 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Laporan Konseling Baru
          </DialogTitle>
          <DialogDescription>
            Ceritakan masalah atau hal yang ingin Anda konsultasikan dengan konselor profesional kami
          </DialogDescription>
        </DialogHeader>
        
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div v-if="errorMessage" class="p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-400 rounded-r-lg">
            <div class="flex items-center">
              <svg class="h-5 w-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
            </div>
          </div>

          <div class="space-y-2">
            <Label for="title" class="text-sm font-medium flex items-center">
              <svg class="mr-1 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
              </svg>
              Judul Laporan
            </Label>
            <Input
              id="title"
              v-model="reportData.title"
              type="text"
              required
              placeholder="Contoh: Kesulitan bergaul dengan teman, Masalah keluarga, dll."
              class="h-12 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>

          <div class="space-y-2">
            <Label for="description" class="text-sm font-medium flex items-center">
              <svg class="mr-1 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
              </svg>
              Deskripsi Masalah
            </Label>
            <textarea
              id="description"
              v-model="reportData.description"
              rows="5"
              required
              placeholder="Ceritakan dengan detail masalah yang Anda hadapi. Semakin jelas informasinya, semakin baik konselor dapat membantu Anda. Jangan khawatir, semua informasi akan dijaga kerahasiaannya."
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 resize-none"
            ></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <Label class="text-sm font-medium flex items-center">
                <svg class="mr-1 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Jenis Konseling
              </Label>
              <select
                v-model="reportData.type"
                class="w-full h-12 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100"
              >
                <option value="online">💬 Online (Chat Real-time)</option>
                <option value="offline">🤝 Tatap Muka (di Ruang BK)</option>
              </select>
            </div>

            <div class="flex items-center justify-center space-x-3 pt-8">
              <input
                id="is_anonymous"
                v-model="reportData.is_anonymous"
                type="checkbox"
                class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2"
              />
              <Label for="is_anonymous" class="text-sm cursor-pointer flex items-center">
                <svg class="mr-1 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2-2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Kirim sebagai Anonim
              </Label>
            </div>
          </div>

          <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-start space-x-3">
              <svg class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div class="text-sm text-blue-700 dark:text-blue-300">
                <p class="font-medium mb-1">Tips untuk laporan yang efektif:</p>
                <ul class="space-y-1 text-xs">
                  <li>• Jelaskan situasi dengan detail</li>
                  <li>• Ceritakan perasaan yang Anda alami</li>
                  <li>• Sebutkan bantuan yang Anda harapkan</li>
                  <li>• Jangan ragu untuk bercerita dengan jujur</li>
                </ul>
              </div>
            </div>
          </div>

          <DialogFooter class="pt-6">
            <Button type="button" @click="closeCreateModal" variant="outline" class="mr-3">
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Batal
            </Button>
            <Button
              type="submit"
              :disabled="dataStore.isLoading"
              class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg hover:shadow-xl transition-all duration-300"
            >
              <svg v-if="dataStore.isLoading" class="animate-spin -ml-1 mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <svg v-else class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
              {{ dataStore.isLoading ? 'Mengirim Laporan...' : 'Kirim Laporan' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Dark mode scrollbar */
.dark ::-webkit-scrollbar-track {
  background: #374151;
}

.dark ::-webkit-scrollbar-thumb {
  background: #6b7280;
}

.dark ::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Smooth animations */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.slide-up {
  animation: slideUp 0.5s ease-out;
}
</style>