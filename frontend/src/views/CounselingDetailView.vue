<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { useDataStore } from '@/stores/dataStore';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const dataStore = useDataStore();

const reportId = route.params.id;
const newMessage = ref('');
const chatContainer = ref(null);

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
    // Scroll to bottom after message is sent
    await nextTick();
    scrollToBottom();
  } catch (error) {
    alert('Gagal mengirim pesan.');
  }
};

const handleAccept = () => dataStore.acceptReport(reportId);
const handleComplete = () => dataStore.completeReport(reportId);

const scrollToBottom = () => {
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
  }
};

const getStatusColor = (status) => {
  switch(status) {
    case 'pending': return 'text-amber-700 bg-amber-100 border-amber-200';
    case 'diterima': return 'text-blue-700 bg-blue-100 border-blue-200';
    case 'selesai': return 'text-green-700 bg-green-100 border-green-200';
    default: return 'text-gray-700 bg-gray-100 border-gray-200';
  }
};

const formatTime = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatDate = (dateString) => {
  if (!dateString) return 'Baru saja';
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};

// Auto scroll to bottom when new messages arrive
const { chats: watchedChats } = dataStore;
const unwatchChats = ref(null);
onMounted(() => {
  unwatchChats.value = watch(() => chats.value.length, () => {
    nextTick(() => scrollToBottom());
  });
});

onUnmounted(() => {
  if (unwatchChats.value) {
    unwatchChats.value();
  }
});
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-slate-900 dark:to-gray-800">
    <!-- Header -->
    <header class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-4">
          <div class="flex items-center space-x-4">
            <Button @click="router.back()" variant="outline" size="sm" class="flex items-center">
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Kembali
            </Button>
            <div>
              <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ report?.title || 'Detail Konseling' }}
              </h1>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ report?.is_anonymous ? 'Sesi Anonim' : `dengan ${report?.student?.name || 'Siswa'}` }}
              </p>
            </div>
          </div>
          
          <div v-if="report" class="flex items-center space-x-3">
            <span :class="['px-3 py-1 text-sm rounded-full font-medium border', getStatusColor(report.status)]">
              {{ report.status }}
            </span>
          </div>
        </div>
      </div>
    </header>

    <!-- Loading State -->
    <div v-if="isLoading && !report" class="flex items-center justify-center min-h-[400px]">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
        <p class="text-gray-600 dark:text-gray-400">Memuat detail konseling...</p>
      </div>
    </div>

    <!-- Not Found State -->
    <div v-else-if="!isLoading && !report" class="flex items-center justify-center min-h-[400px]">
      <Card class="max-w-md mx-4 text-center">
        <CardContent class="p-8">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Konseling Tidak Ditemukan</h3>
          <p class="text-gray-500 dark:text-gray-400 mb-4">
            Laporan tidak ditemukan atau Anda tidak memiliki akses ke sesi ini.
          </p>
          <Button @click="router.back()" variant="outline">
            Kembali ke Dashboard
          </Button>
        </CardContent>
      </Card>
    </div>

    <!-- Main Content -->
    <main v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Report Details Sidebar -->
        <div class="lg:col-span-1">
          <Card class="sticky top-24">
            <CardHeader>
              <CardTitle class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Detail Laporan</span>
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="grid grid-cols-1 gap-4">
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Status</p>
                  <span :class="['inline-block px-2 py-1 text-sm rounded-full font-medium', getStatusColor(report.status)]">
                    {{ report.status }}
                  </span>
                </div>
                
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Pelapor</p>
                  <p class="text-sm text-gray-900 dark:text-gray-100">
                    {{ report.is_anonymous ? '🔒 Anonim' : `👤 ${report.student?.name || 'Siswa'}` }}
                  </p>
                </div>
                
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Jenis</p>
                  <p class="text-sm text-gray-900 dark:text-gray-100">
                    {{ report.type === 'online' ? '💬 Online Chat' : '🤝 Tatap Muka' }}
                  </p>
                </div>
                
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal</p>
                  <p class="text-sm text-gray-900 dark:text-gray-100">
                    📅 {{ formatDate(report.created_at) }}
                  </p>
                </div>
              </div>

              <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Deskripsi Masalah</p>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                  <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">
                    {{ report.description }}
                  </p>
                </div>
              </div>

              <!-- Counselor Actions -->
              <div v-if="authStore.isGuru && report.status !== 'selesai'" class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Aksi Konselor</p>
                <Button 
                  v-if="report.status === 'pending'" 
                  @click="handleAccept" 
                  class="w-full bg-blue-600 hover:bg-blue-700 text-white"
                >
                  ✅ Terima Laporan
                </Button>
                <Button 
                  v-if="report.status === 'diterima'" 
                  @click="handleComplete" 
                  class="w-full bg-green-600 hover:bg-green-700 text-white"
                >
                  ✨ Tandai Selesai
                </Button>
              </div>
              
              <div v-if="report.status === 'selesai'" class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 text-center">
                  <svg class="mx-auto h-8 w-8 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <p class="text-sm font-medium text-green-800 dark:text-green-200">Konseling Selesai</p>
                  <p class="text-xs text-green-600 dark:text-green-300 mt-1">Terima kasih telah menggunakan layanan BK Online</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Chat Interface -->
        <div class="lg:col-span-2">
          <!-- Online Chat -->
          <div v-if="report.type === 'online' && report.status !== 'pending'">
            <Card class="h-[600px] flex flex-col">
              <CardHeader class="flex-shrink-0 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-t-lg">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    <CardTitle class="text-white">Room Chat Konseling</CardTitle>
                  </div>
                  <div class="text-sm text-indigo-100">
                    {{ chats.length }} pesan
                  </div>
                </div>
              </CardHeader>

              <!-- Chat Messages -->
              <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                <div v-if="chats.length === 0" class="text-center py-8">
                  <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                  </svg>
                  <p class="text-gray-500 dark:text-gray-400">Mulai percakapan dengan mengetik pesan di bawah</p>
                </div>

                <div v-else v-for="chat in chats" :key="chat.id" class="flex" 
                     :class="{
                       'justify-end': (authStore.isGuru && chat.sender_type.includes('User')) || (authStore.isSiswa && chat.sender_type.includes('Student')),
                       'justify-start': (authStore.isGuru && chat.sender_type.includes('Student')) || (authStore.isSiswa && chat.sender_type.includes('User'))
                     }">
                  <div class="max-w-xs md:max-w-md lg:max-w-lg group">
                    <!-- Message Bubble -->
                    <div :class="[
                      'px-4 py-3 rounded-2xl shadow-sm transition-all duration-200 group-hover:shadow-md',
                      (authStore.isGuru && chat.sender_type.includes('User')) || (authStore.isSiswa && chat.sender_type.includes('Student'))
                        ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-br-md'
                        : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-bl-md'
                    ]">
                      <p class="text-sm leading-relaxed">{{ chat.message }}</p>
                    </div>
                    
                    <!-- Timestamp -->
                    <div :class="[
                      'mt-1 text-xs opacity-70',
                      (authStore.isGuru && chat.sender_type.includes('User')) || (authStore.isSiswa && chat.sender_type.includes('Student'))
                        ? 'text-right text-indigo-600 dark:text-indigo-400'
                        : 'text-left text-gray-500 dark:text-gray-400'
                    ]">
                      {{ formatTime(chat.created_at) }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Message Input -->
              <CardContent class="flex-shrink-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <form @submit.prevent="handleSendMessage" class="flex items-end space-x-3">
                  <div class="flex-1">
                    <textarea
                      v-model="newMessage"
                      @keydown.enter.prevent="handleSendMessage"
                      placeholder="Ketik pesan Anda..."
                      rows="1"
                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 resize-none"
                      style="min-height: 44px; max-height: 120px;"
                    ></textarea>
                  </div>
                  <Button
                    type="submit"
                    :disabled="!newMessage.trim()"
                    class="h-11 w-11 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 disabled:from-gray-300 disabled:to-gray-400 shadow-lg"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                  </Button>
                </form>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                  Tekan Enter untuk mengirim • Shift+Enter untuk baris baru
                </p>
              </CardContent>
            </Card>
          </div>

          <!-- Pending Online Chat -->
          <div v-else-if="report.type === 'online' && report.status === 'pending'">
            <Card class="h-[600px] flex items-center justify-center">
              <CardContent class="text-center p-8">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                  Menunggu Persetujuan Konselor
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-md">
                  Laporan Anda sedang ditinjau oleh konselor. Chat akan tersedia setelah konselor menerima laporan ini.
                </p>
                <div class="inline-flex items-center px-4 py-2 bg-amber-50 dark:bg-amber-900/20 rounded-full">
                  <div class="w-2 h-2 bg-amber-400 rounded-full animate-pulse mr-2"></div>
                  <span class="text-sm text-amber-700 dark:text-amber-300">Sedang menunggu...</span>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Offline Counseling -->
          <div v-else-if="report.type === 'offline'">
            <Card class="h-[600px] flex items-center justify-center">
              <CardContent class="text-center p-8">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6">
                  <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
                  Konseling Tatap Muka
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md leading-relaxed">
                  Konseling ini akan dilakukan secara tatap muka. Silakan menghubungi konselor atau datang ke ruang BK untuk menjadwalkan pertemuan.
                </p>
                
                <div class="space-y-3 max-w-md mx-auto">
                  <div class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div class="text-left">
                      <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Lokasi</p>
                      <p class="text-xs text-blue-700 dark:text-blue-200">Ruang BK Gedung Utama Lt.2</p>
                    </div>
                  </div>
                  
                  <div class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-left">
                      <p class="text-sm font-medium text-green-900 dark:text-green-100">Jam Operasional</p>
                      <p class="text-xs text-green-700 dark:text-green-200">Senin - Jumat, 08:00 - 15:00</p>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
/* Custom scrollbar for chat */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Dark mode scrollbar */
.dark .overflow-y-auto::-webkit-scrollbar-track {
  background: #374151;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #6b7280;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Auto-expand textarea */
textarea {
  resize: none;
  overflow-y: auto;
  max-height: 120px;
}
</style>