<script setup>
import { ref } from 'vue';
import { useDataStore } from '@/stores/dataStore';

const props = defineProps({
  isOpen: Boolean,
});
const emit = defineEmits(['close']);

const dataStore = useDataStore();

const reportData = ref({
  title: '',
  description: '',
  type: 'online',
  is_anonymous: false,
});
const errorMessage = ref('');

const handleSubmit = async () => {
  errorMessage.value = '';
  try {
    await dataStore.createReport(reportData.value);
    emit('close');
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errorMessage.value = Object.values(error.response.data.errors).flat().join(' ');
    } else {
      errorMessage.value = 'Terjadi kesalahan saat mengirim laporan.';
    }
  }
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-10 overflow-y-auto bg-gray-500 bg-opacity-75 transition-opacity">
    <div class="flex items-center justify-center min-h-screen">
      <div class="relative bg-white rounded-lg shadow-xl p-8 m-4 max-w-lg w-full">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Buat Laporan Konseling Baru</h3>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div v-if="errorMessage" class="p-3 bg-red-100 text-red-700 rounded-md text-sm">
            {{ errorMessage }}
          </div>
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Judul Laporan</label>
            <input v-model="reportData.title" type="text" id="title" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
          </div>
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Masalah</label>
            <textarea v-model="reportData.description" id="description" rows="4" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Jenis Konseling</label>
            <select v-model="reportData.type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
              <option value="online">Online (Chat)</option>
              <option value="offline">Offline (Tatap Muka)</option>
            </select>
          </div>
          <div class="flex items-center">
            <input v-model="reportData.is_anonymous" type="checkbox" id="is_anonymous" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
            <label for="is_anonymous" class="ml-2 block text-sm text-gray-900">Kirim sebagai Anonim</label>
          </div>
          <div class="pt-4 flex justify-end space-x-3">
            <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
              Batal
            </button>
            <button type="submit" :disabled="dataStore.isLoading" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:bg-indigo-400">
              {{ dataStore.isLoading ? 'Mengirim...' : 'Kirim Laporan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>