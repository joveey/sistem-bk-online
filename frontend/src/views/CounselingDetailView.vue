<script setup>
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import { useDataStore } from '@/stores/dataStore';
import { Button } from '@/components/ui/button';

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
const isSending = ref(false);

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
  });
};

onMounted(() => {
  dataStore.fetchReportDetail(reportId).then(() => {
    scrollToBottom();
  });
});

watch(chats, () => {
  scrollToBottom();
}, { deep: true });


const handleSendMessage = async () => {
  if (newMessage.value.trim() === '' || isSending.value) return;
  isSending.value = true;
  try {
    await dataStore.postChatMessage({ reportId, message: newMessage.value });
    newMessage.value = '';
  } catch (error) {
    alert('Gagal mengirim pesan.');
  } finally {
    isSending.value = false;
  }
};

const handleAccept = () => dataStore.acceptReport(reportId);
const handleComplete = () => dataStore.completeReport(reportId);

const getSenderName = (chat) => {
  if (chat.sender_type.includes('User')) {
    return report.value?.counselor?.name || 'Konselor';
  }
  if (chat.sender_type.includes('Student')) {
    return report.value?.is_anonymous ? 'Siswa (Anonim)' : report.value?.student?.name || 'Siswa';
  }
  return 'Tidak Dikenal';
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-6">
    <div class="max-w-7xl mx-auto">
      <Button @click="router.back()" variant="ghost" class="mb-4">
        &larr; Kembali
      </Button>

      <div v-if="isLoading && !report" class="text-center p-10 bg-white rounded-lg shadow">
        Memuat detail laporan...
      </div>
      <div v-else-if="!report" class="text-center p-10 bg-white rounded-lg shadow">
        Laporan tidak ditemukan.
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
           <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ report.title }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              Laporan dibuat pada {{ new Date(report.created_at).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
            </p>
          </div>
          <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="space-y-4 text-sm">
                <div>
                  <p class="font-semibold text-gray-500 dark:text-gray-400">Status</p>
                  <p class="capitalize font-bold text-lg" :class="{
                    'text-yellow-600': report.status === 'pending',
                    'text-blue-600': report.status === 'diterima',
                    'text-green-600': report.status === 'selesai'
                  }">{{ report.status }}</p>
                </div>
                <div>
                  <p class="font-semibold text-gray-500 dark:text-gray-400">Pelapor</p>
                  <p class="text-gray-800 dark:text-gray-200">{{ report.is_anonymous ? 'Anonim' : report.student?.name || 'Siswa' }}</p>
                </div>
                <div>
                  <p class="font-semibold text-gray-500 dark:text-gray-400">Jenis Konseling</p>
                  <p class="text-gray-800 dark:text-gray-200 capitalize">{{ report.type }}</p>
                </div>
                <div>
                  <p class="font-semibold text-gray-500 dark:text-gray-400">Deskripsi Masalah</p>
                  <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ report.description }}</p>
                </div>
                <div v-if="report.counselor">
                  <p class="font-semibold text-gray-500 dark:text-gray-400">Ditangani oleh</p>
                  <p class="text-gray-800 dark:text-gray-200">{{ report.counselor.name }}</p>
                </div>
            </div>
            <div v-if="authStore.isGuru" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
              <Button v-if="report.status === 'pending'" @click="handleAccept" class="w-full bg-blue-600 hover:bg-blue-700 text-white">
                Terima Laporan
              </Button>
              <Button v-if="report.status === 'diterima'" @click="handleComplete" class="w-full bg-green-600 hover:bg-green-700 text-white">
                Tandai Selesai
              </Button>
            </div>
          </div>
        </div>

        <div v-if="report.type === 'online' && report.status !== 'pending'" class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col h-[80vh]">
          <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 p-4 border-b border-gray-200 dark:border-gray-700">Percakapan</h2>
          
          <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
            <div v-for="chat in chats" :key="chat.id" class="flex" :class="{
              'justify-end': (authStore.isGuru && chat.sender_type.includes('User')) || (authStore.isSiswa && chat.sender_type.includes('Student')),
              'justify-start': (authStore.isGuru && chat.sender_type.includes('Student')) || (authStore.isSiswa && chat.sender_type.includes('User'))
            }">
              <div class="flex flex-col max-w-xs md:max-w-md lg:max-w-lg">
                <div class="px-4 py-3 rounded-2xl" :class="{
                  'bg-indigo-600 text-white': (authStore.isGuru && chat.sender_type.includes('User')) || (authStore.isSiswa && chat.sender_type.includes('Student')),
                  'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200': (authStore.isGuru && chat.sender_type.includes('Student')) || (authStore.isSiswa && chat.sender_type.includes('User'))
                }">
                  <p class="text-sm font-bold mb-1">{{ getSenderName(chat) }}</p>
                  <p class="text-base">{{ chat.message }}</p>
                </div>
                <span class="text-xs text-gray-400 mt-1 px-2" :class="{
                  'text-right': (authStore.isGuru && chat.sender_type.includes('User')) || (authStore.isSiswa && chat.sender_type.includes('Student')),
                  'text-left': (authStore.isGuru && chat.sender_type.includes('Student')) || (authStore.isSiswa && chat.sender_type.includes('User'))
                }">{{ formatDate(chat.created_at) }}</span>
              </div>
            </div>
            <div v-if="!chats.length" class="text-center text-gray-400 text-sm py-10">
              Mulai percakapan...
            </div>
          </div>
          
          <form @submit.prevent="handleSendMessage" class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex items-center space-x-3">
              <input v-model="newMessage" :disabled="isSending" placeholder="Ketik pesan Anda..." class="flex-1 block w-full border border-gray-300 dark:border-gray-600 rounded-full py-2 px-4 bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
              <Button type="submit" :disabled="isSending" class="rounded-full w-10 h-10 p-0 flex items-center justify-center bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
              </Button>
            </div>
          </form>
        </div>

        <div v-if="report.type === 'online' && report.status === 'pending'" class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md text-center flex items-center justify-center">
          <p class="text-gray-500 italic">Konselor harus menerima laporan ini untuk memulai percakapan.</p>
        </div>

        <div v-if="report.type === 'offline'" class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md text-center flex flex-col items-center justify-center">
          <svg class="h-16 w-16 text-indigo-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          <h2 class="text-lg font-bold text-gray-800">Konseling Tatap Muka (Offline)</h2>
          <p class="mt-2 text-gray-600">Tidak ada fitur chat untuk jenis konseling ini.</p>
        </div>

      </div>
    </div>
  </div>
</template>