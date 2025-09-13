<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = () => {
  authStore.logout();
};

// Dummy navigation links for demonstration
const navLinks = [
  { name: 'Dashboard', path: '/guru/dashboard' },
  { name: 'Manajemen Siswa', path: '/guru/dashboard' }, // Arahkan ke tab yang sesuai nanti
  { name: 'Laporan', path: '/guru/dashboard' }, // Arahkan ke tab yang sesuai nanti
];
</script>

<template>
  <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 bg-white dark:bg-gray-800 border-r dark:border-gray-700">
      <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="h-16 flex items-center justify-center px-4 border-b dark:border-gray-700">
          <h1 class="text-lg font-bold text-indigo-600 dark:text-indigo-400">BK Online</h1>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-2 py-4 space-y-2">
          <a
            v-for="link in navLinks"
            :key="link.name"
            :href="link.path"
            @click.prevent="router.push(link.path)"
            class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
            :class="{ 'bg-gray-100 dark:bg-gray-700': router.currentRoute.value.path === link.path }"
          >
            {{ link.name }}
          </a>
        </nav>

        <!-- Sidebar Footer/User Info -->
        <div class="px-4 py-4 border-t dark:border-gray-700">
          <div class="flex items-center space-x-3">
            <div class="flex-1">
              <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ authStore.user?.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ authStore.user?.email }}</p>
            </div>
            <button @click="handleLogout" class="p-2 rounded-full text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600" title="Logout">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
            </button>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
      <div class="p-4 sm:p-6 lg:p-8">
        <!-- Slot untuk merender konten halaman aktif -->
        <slot />
      </div>
    </main>
  </div>
</template>
