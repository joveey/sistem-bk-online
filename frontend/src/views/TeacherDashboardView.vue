<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { useDataStore } from '@/stores/dataStore';
import { useRouter } from 'vue-router';

// Inisialisasi store dan router
const authStore = useAuthStore();
const dataStore = useDataStore();
const router = useRouter();

// State untuk UI komponen
const activeTab = ref('reports'); // 'reports' atau 'students'
const isModalOpen = ref(false);

// State untuk form tambah siswa
const newStudent = ref({
  name: '',
  class: '',
  unique_code: '',
});
const creationError = ref(''); // Untuk menyimpan pesan error dari backend

// Mengambil data dari store menggunakan computed properties
const user = computed(() => authStore.user);
const students = computed(() => dataStore.students);
const reports = computed(() => dataStore.reports);

// Mengambil data awal saat komponen pertama kali dimuat
onMounted(() => {
  dataStore.fetchReports();
  dataStore.fetchStudents();
});

const handleLogout = () => {
  authStore.logout();
};

// Fungsi untuk membuka modal tambah siswa
const openCreateStudentModal = () => {
  isModalOpen.value = true;
  creationError.value = ''; // Bersihkan error lama
  // Reset form
  newStudent.value = { name: '', class: '', unique_code: '' };
};

// Fungsi untuk menutup modal
const closeCreateStudentModal = () => {
  isModalOpen.value = false;
};

// Fungsi untuk menangani submit form tambah siswa
const handleCreateStudent = async () => {
  creationError.value = ''; // Bersihkan error sebelum submit
  try {
    await dataStore.createStudent(newStudent.value);
    closeCreateStudentModal(); // Tutup modal jika berhasil
  } catch (error) {
    // Cek apakah ini error validasi (422) dari backend
    if (error.response && error.response.status === 422) {
      const errors = error.response.data.errors;
      // Gabungkan semua pesan error dari Laravel menjadi satu string
      creationError.value = Object.values(errors).flat().join(' ');
    } else {
      // Untuk error lainnya (misal: server down)
      creationError.value = 'Gagal menambah siswa. Terjadi kesalahan pada server.';
    }
  }
};

// Navigasi ke halaman detail laporan
const navigateToDetail = (reportId) => {
    router.push(`/konseling/${reportId}`);
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow-sm">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div>
            <h1 class="text-xl font-bold text-gray-800">Dasbor Konselor</h1>
            <p class="text-sm text-gray-500">Selamat datang, {{ user?.name }}!</p>
          </div>
          <button
            @click="handleLogout"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
          >
            Logout
          </button>
        </div>
      </div>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
          <button
            @click="activeTab = 'reports'"
            :class="[
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors',
              activeTab === 'reports'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
            ]"
          >
            Laporan Konseling
          </button>
          <button
            @click="activeTab = 'students'"
            :class="[
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors',
              activeTab === 'students'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
            ]"
          >
            Manajemen Siswa
          </button>
        </nav>
      </div>

      <div class="mt-8">
        <div v-if="activeTab === 'reports'">
          <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Daftar Laporan Masuk</h2>
             <ul class="divide-y divide-gray-200">
                <li v-for="report in reports" :key="report.id" @click="navigateToDetail(report.id)" class="py-4 flex justify-between items-center cursor-pointer hover:bg-gray-50 px-2 rounded transition-colors">
                    <div>
                        <p class="text-sm font-medium text-indigo-600">{{ report.title }}</p>
                        <p class="text-sm text-gray-500">Status: {{ report.status }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="report.is_anonymous ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'">
                        {{ report.is_anonymous ? 'Anonim' : 'Siswa Diketahui' }}
                    </span>
                </li>
                 <li v-if="!reports.length && !dataStore.isLoading" class="py-4 text-center text-gray-500">
                    Tidak ada laporan yang ditemukan.
                </li>
            </ul>
          </div>
        </div>

        <div v-if="activeTab === 'students'">
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 flex justify-between items-center border-b">
                <h2 class="text-lg font-medium text-gray-900">Daftar Siswa Terdaftar</h2>
                <button @click="openCreateStudentModal" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors">
                    + Tambah Siswa
                </button>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Unik</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="student in students" :key="student.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ student.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.class }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.unique_code }}</td>
                  </tr>
                   <tr v-if="!students.length && !dataStore.isLoading">
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            Belum ada siswa yang terdaftar.
                        </td>
                    </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>

    <div v-if="isModalOpen" class="fixed inset-0 z-10 overflow-y-auto bg-gray-500 bg-opacity-75 transition-opacity">
        <div class="flex items-center justify-center min-h-screen">
            <div class="relative bg-white rounded-lg shadow-xl p-8 m-4 max-w-lg w-full">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Tambah Siswa Baru</h3>
                <form @submit.prevent="handleCreateStudent" class="space-y-4">
                    <div v-if="creationError" class="p-3 bg-red-100 text-red-700 rounded-md text-sm">
                        {{ creationError }}
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Siswa</label>
                        <input v-model="newStudent.name" type="text" id="name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                     <div>
                        <label for="class" class="block text-sm font-medium text-gray-700">Kelas</label>
                        <input v-model="newStudent.class" type="text" id="class" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                     <div>
                        <label for="unique_code" class="block text-sm font-medium text-gray-700">Nomor Induk / Kode Unik</label>
                        <input v-model="newStudent.unique_code" type="text" id="unique_code" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="closeCreateStudentModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit" :disabled="dataStore.isLoading" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 disabled:bg-indigo-400 transition-colors">
                          {{ dataStore.isLoading ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

  </div>
</template>