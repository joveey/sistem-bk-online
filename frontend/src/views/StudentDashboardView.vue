<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { useDataStore } from '@/stores/dataStore';
import { useRouter } from 'vue-router'; // 1. Impor useRouter
import CreateReportModal from '@/components/CreateReportModal.vue';

const authStore = useAuthStore();
const dataStore = useDataStore();
const router = useRouter(); // 2. Inisialisasi router

const isModalOpen = ref(false);

const user = computed(() => authStore.user);
const reports = computed(() => dataStore.reports);

onMounted(() => {
  dataStore.fetchReports();
});

const handleLogout = () => {
  authStore.logout();
};

const openCreateModal = () => {
  isModalOpen.value = true;
};

const closeCreateModal = () => {
  isModalOpen.value = false;
};

// 3. Buat fungsi untuk navigasi ke halaman detail
const navigateToDetail = (reportId) => {
    router.push(`/konseling/${reportId}`);
};
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Header Dasbor -->
    <header class="bg-white shadow-sm">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div>
            <h1 class="text-xl font-bold text-gray-800">Dasbor Siswa</h1>
            <p class="text-sm text-gray-500">Selamat datang, {{ user?.name }}!</p>
          </div>
          <button @click="handleLogout" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
            Logout
          </button>
        </div>
      </div>
    </header>

    <!-- Konten Utama -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-lg font-medium text-gray-900">Riwayat Laporan Anda</h2>
          <button @click="openCreateModal" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
            + Buat Laporan Baru
          </button>
        </div>

        <!-- Daftar Laporan -->
        <ul v-if="!dataStore.isLoading" class="divide-y divide-gray-200">
          <!-- 4. Tambahkan @click dan styling untuk membuatnya interaktif -->
          <li v-for="report in reports" 
              :key="report.id" 
              @click="navigateToDetail(report.id)" 
              class="py-4 flex justify-between items-center cursor-pointer hover:bg-gray-50 px-2 rounded-md transition-colors"
          >
            <div>
              <p class="text-sm font-medium text-indigo-600">{{ report.title }}</p>
              <p class="text-sm text-gray-500">Status: <span class="font-semibold">{{ report.status }}</span></p>
            </div>
             <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="report.is_anonymous ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'">
                {{ report.is_anonymous ? 'Anonim' : 'Identitas Terbuka' }}
            </span>
          </li>
          <li v-if="!reports.length" class="py-4 text-center text-gray-500">
            Anda belum membuat laporan apapun.
          </li>
        </ul>
        <div v-else class="text-center py-4 text-gray-500">
          Memuat laporan...
        </div>
      </div>
    </main>

    <!-- Modal untuk membuat laporan -->
    <CreateReportModal :is-open="isModalOpen" @close="closeCreateModal" />

  </div>
</template>

