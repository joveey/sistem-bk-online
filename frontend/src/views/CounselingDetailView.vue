<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { useDataStore } from '@/stores/dataStore';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const dataStore = useDataStore();

const reportId = route.params.id;
const newMessage = ref('');

const report = computed(() => dataStore.reportDetail);
const chats = computed(() => dataStore.chats);
const user = computed(() => authStore.user);
const isLoading = computed(() => dataStore.isLoading);

onMounted(() => {
  dataStore.fetchReportDetail(reportId);
});

const handleSendMessage = async () => {
  if (newMessage.value.trim() === '') return;
  try {
    await dataStore.postChatMessage({ reportId, message: newMessage.value });
    newMessage.value = '';
  } catch (error) {
    alert('Gagal mengirim pesan.');
  }
};

const handleAccept = () => dataStore.acceptReport(reportId);
const handleComplete = () => dataStore.completeReport(reportId);
</script>

<template>
  <div class="min-h-screen bg-gray-100 p-4 sm:p-8">
    <div class="container mx-auto">
      <button @click="router.back()" class="mb-6 text-sm font-medium text-indigo-600 hover:text-indigo-800">
        &larr; Kembali ke Dasbor
      </button>

      <!-- Indikator Loading Awal -->
      <div v-if="isLoading && !report" class="text-center text-gray-500">
        Memuat detail laporan...
      </div>

      <!-- Pesan Jika Laporan Tidak Ditemukan -->
      <div v-if="!isLoading && !report" class="text-center text-gray-500 bg-white p-8 rounded-lg shadow">
        Laporan tidak ditemukan atau Anda tidak memiliki akses.
      </div>

      <!-- Tampilan Utama Jika Laporan Ditemukan -->
      <div v-if="report" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Detail Laporan (Sisi Kiri) -->
        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
          <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ report.title }}</h1>
          <div class="space-y-4 text-sm">
            <div>
              <p class="font-semibold text-gray-600">Status</p>
              <p class="capitalize font-bold" :class="{
                'text-yellow-600': report.status === 'pending',
                'text-blue-600': report.status === 'diterima',
                'text-green-600': report.status === 'selesai'
              }">{{ report.status }}</p>
            </div>
            <div>
              <p class="font-semibold text-gray-600">Pelapor</p>
              <p>{{ report.is_anonymous ? 'Anonim' : report.student?.name || 'Siswa' }}</p>
            </div>
            <div>
              <p class="font-semibold text-gray-600">Jenis Konseling</p>
              <p class="capitalize">{{ report.type }}</p>
            </div>
            <div>
              <p class="font-semibold text-gray-600">Deskripsi Masalah</p>
              <p class="text-gray-800 whitespace-pre-wrap">{{ report.description }}</p>
            </div>
            <div v-if="report.counselor">
              <p class="font-semibold text-gray-600">Ditangani oleh</p>
              <p>{{ report.counselor.name }}</p>
            </div>
          </div>

          <!-- Tombol Aksi untuk Guru -->
          <div v-if="authStore.isGuru" class="mt-6 border-t pt-4 space-y-2">
             <button v-if="report.status === 'pending'" @click="handleAccept" class="w-full text-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Terima Laporan
            </button>
             <button v-if="report.status === 'diterima'" @click="handleComplete" class="w-full text-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                Tandai Selesai
            </button>
            <p v-if="report.status === 'selesai'" class="text-center text-sm text-gray-500">Laporan telah selesai.</p>
          </div>
        </div>

        <!-- Kolom Chat (Sisi Kanan) -->
        
        <!-- Tampilkan Antarmuka Chat HANYA JIKA laporan 'online' DAN status BUKAN 'pending' -->
        <div v-if="report.type === 'online' && report.status !== 'pending'" class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md flex flex-col" style="height: 75vh;">
          <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Percakapan</h2>
          <div class="flex-1 overflow-y-auto pr-4 space-y-4">
            <!-- Pesan Chat -->
            <div v-for="chat in chats" :key="chat.id" class="flex" :class="{
              'justify-end': (authStore.isGuru && chat.sender_type.includes('User')) || (authStore.isSiswa && chat.sender_type.includes('Student')),
              'justify-start': (authStore.isGuru && chat.sender_type.includes('Student')) || (authStore.isSiswa && chat.sender_type.includes('User'))
            }">
              <div class="max-w-xs md:max-w-md lg:max-w-lg px-4 py-2 rounded-lg" :class="{
                'bg-indigo-500 text-white': (authStore.isGuru && chat.sender_type.includes('User')) || (authStore.isSiswa && chat.sender_type.includes('Student')),
                'bg-gray-200 text-gray-800': (authStore.isGuru && chat.sender_type.includes('Student')) || (authStore.isSiswa && chat.sender_type.includes('User'))
              }">
                <p class="text-sm">{{ chat.message }}</p>
              </div>
            </div>
            <div v-if="!chats.length" class="text-center text-gray-400 text-sm">
                Mulai percakapan...
            </div>
          </div>
          <!-- Input Pesan Baru -->
          <form @submit.prevent="handleSendMessage" class="mt-4 border-t pt-4">
            <div class="flex items-center space-x-3">
              <textarea v-model="newMessage" @keyup.enter.prevent="handleSendMessage" placeholder="Ketik pesan Anda..." rows="1" class="flex-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"></textarea>
              <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-full hover:bg-indigo-700">
                Kirim
              </button>
            </div>
          </form>
        </div>

        <!-- Tampilkan Pesan Placeholder JIKA laporan 'online' TAPI status MASIH 'pending' -->
        <div v-if="report.type === 'online' && report.status === 'pending'" class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md text-center flex items-center justify-center">
            <p class="text-gray-500 italic">
                Konselor harus menerima laporan ini terlebih dahulu untuk memulai percakapan.
            </p>
        </div>

        <!-- Tampilkan Pesan Placeholder JIKA laporan 'offline' -->
        <div v-if="report.type === 'offline'" class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md text-center flex items-center justify-center">
          <div>
            <h2 class="text-lg font-bold text-gray-800">Konseling Tatap Muka (Offline)</h2>
            <p class="mt-4 text-gray-600">Tidak ada fitur chat untuk jenis konseling ini.</p>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

