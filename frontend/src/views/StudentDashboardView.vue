<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { useDataStore } from '@/stores/dataStore';
import { useRouter } from 'vue-router';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

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
    case 'pending': return 'text-amber-600 bg-amber-50 border-amber-200';
    case 'diterima': return 'text-blue-600 bg-blue-50 border-blue-200';
    case 'selesai': return 'text-green-600 bg-green-50 border-green-200';
    default: return 'text-gray-600 bg-gray-50 border-gray-200';
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
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900/20">
    <!-- Header -->
    <header class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center space-x-4">
            <div class="h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Portal Siswa</h1>
              <p class="text-sm text-gray-500 dark:text-gray-400">Selamat datang, {{ user?.name }}!</p>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <Button @click="openCreateModal" class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg">
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Buat Laporan
            </Button>
            <Button @click="handleLogout" variant="outline" class="border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300">
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              Logout
            </Button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Welcome Section -->
      <div class="mb-8">
        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl p-6 text-white relative overflow-hidden">
          <div class="absolute inset-0 bg-black/10"></div>
          <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Halo, {{ user?.name }}! 👋</h2>
            <p class="text-indigo-100 mb-4">Bagaimana perasaanmu hari ini? Jangan ragu untuk berbagi jika ada yang ingin kamu ceritakan.</p>
            <div class="flex items-center space-x-4">
              <Button @click="openCreateModal" variant="secondary" class="bg-white/20 hover:bg-white/30 text-white border-white/30">
                Mulai Konseling
              </Button>
              <span class="text-sm text-indigo-100">Konseling tersedia 24/7</span>
            </div>
          </div>
          <!-- Decorative elements -->
          <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-16 translate-x-16"></div>
          <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <Card class="border-0 shadow-lg bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20">
          <CardContent class="p-6">
            <div class="flex items-center">
              <div class="p-3 bg-blue-500 rounded-xl">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-blue-600">Total Laporan</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ totalReports }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="border-0 shadow-lg bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20">
          <CardContent class="p-6">
            <div class="flex items-center">
              <div class="p-3 bg-amber-500 rounded-xl">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-amber-600">Menunggu</p>
                <p class="text-2xl font-bold text-amber-900 dark:text-amber-100">{{ pendingReports }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="border-0 shadow-lg bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20">
          <CardContent class="p-6">
            <div class="flex items-center">
              <div class="p-3 bg-emerald-500 rounded-xl">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-emerald-600">Berlangsung</p>
                <p class="text-2xl font-bold text-emerald-900 dark:text-emerald-100">{{ activeReports }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="border-0 shadow-lg bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20">
          <CardContent class="p-6">
            <div class="flex items-center">
              <div class="p-3 bg-purple-500 rounded-xl">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-purple-600">Selesai</p>
                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ completedReports }}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Reports List -->
      <Card class="border-0 shadow-xl">
        <CardHeader>
          <div class="flex justify-between items-center">
            <div>
              <CardTitle class="text-xl text-gray-900 dark:text-gray-100">Riwayat Laporan Konseling</CardTitle>
              <CardDescription>Kelola dan pantau perkembangan laporan Anda</CardDescription>
            </div>
            <Button @click="openCreateModal" variant="outline" class="border-indigo-200 text-indigo-600 hover:bg-indigo-50">
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Tambah Laporan
            </Button>
          </div>
        </CardHeader>
        
        <CardContent>
          <div v-if="isLoading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <span class="ml-2 text-gray-600">Memuat laporan...</span>
          </div>
          
          <div v-else-if="!reports.length" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Belum ada laporan</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan membuat laporan konseling pertama Anda</p>
            <Button @click="openCreateModal" class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white">
              Buat Laporan Pertama
            </Button>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="report in reports"
              :key="report.id"
              @click="navigateToDetail(report.id)"
              class="p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-lg transition-all duration-200 cursor-pointer group"
            >
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-2">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                      {{ report.title }}
                    </h3>
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', getStatusColor(report.status)]">
                      <svg :class="'w-3 h-3 mr-1'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getStatusIcon(report.status)"/>
                      </svg>
                      {{ report.status }}
                    </span>
                  </div>
                  
                  <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2">
                    {{ report.description }}
                  </p>
                  
                  <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                      </svg>
                      {{ formatDate(report.created_at) }}
                    </span>
                    <span class="flex items-center">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                      </svg>
                      {{ report.type === 'online' ? 'Online' : 'Tatap Muka' }}
                    </span>
                    <span v-if="report.is_anonymous" class="flex items-center">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                      </svg>
                      Anonim
                    </span>
                  </div>
                </div>
                
                <div class="ml-4">
                  <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </main>

    <!-- Create Report Modal -->
    <Dialog v-model:open="isModalOpen">
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle class="text-xl">Buat Laporan Konseling Baru</DialogTitle>
          <DialogDescription>
            Ceritakan masalah atau hal yang ingin Anda konsultasikan dengan konselor
          </DialogDescription>
        </DialogHeader>
        
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div v-if="errorMessage" class="p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
            <div class="flex items-center">
              <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
            </div>
          </div>

          <div class="space-y-2">
            <Label for="title" class="text-sm font-medium">Judul Laporan</Label>
            <Input
              id="title"
              v-model="reportData.title"
              type="text"
              required
              placeholder="Contoh: Masalah dengan teman sekelas"
              class="h-11"
            />
          </div>

          <div class="space-y-2">
            <Label for="description" class="text-sm font-medium">Deskripsi Masalah</Label>
            <textarea
              id="description"
              v-model="reportData.description"
              rows="5"
              required
              placeholder="Ceritakan dengan detail masalah yang Anda hadapi. Semakin jelas informasinya, semakin baik konselor dapat membantu Anda."
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
            ></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label class="text-sm font-medium">Jenis Konseling</Label>
              <select
                v-model="reportData.type"
                class="w-full h-11 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
              >
                <option value="online">💬 Online (Chat)</option>
                <option value="offline">🤝 Tatap Muka (Offline)</option>
              </select>
            </div>

            <div class="flex items-center space-x-2 pt-8">
              <input
                id="is_anonymous"
                v-model="reportData.is_anonymous"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
              />
              <Label for="is_anonymous" class="text-sm cursor-pointer">
                Kirim sebagai Anonim
              </Label>
            </div>
          </div>

          <DialogFooter class="pt-6">
            <Button type="button" @click="closeCreateModal" variant="outline" class="mr-2">
              Batal
            </Button>
            <Button
              type="submit"
              :disabled="dataStore.isLoading"
              class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white"
            >
              <svg v-if="dataStore.isLoading" class="animate-spin -ml-1 mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              {{ dataStore.isLoading ? 'Mengirim...' : 'Kirim Laporan' }}
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
</style>