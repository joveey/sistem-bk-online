<script setup>
import { ref, onMounted, computed } from 'vue';
import { useDataStore } from '@/stores/dataStore';
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

const dataStore = useDataStore();
const authStore = useAuthStore();
const router = useRouter();

const newStudent = ref({ name: '', class: '', unique_code: '' });
const creationError = ref('');
const isDialogOpen = ref(false);
const activeTab = ref('dashboard');

const students = computed(() => dataStore.students);
const reports = computed(() => dataStore.reports);
const isLoading = computed(() => dataStore.isLoading);

// Statistics computed properties
const totalReports = computed(() => reports.value.length);
const pendingReports = computed(() => reports.value.filter(report => report.status === 'pending').length);
const acceptedReports = computed(() => reports.value.filter(report => report.status === 'diterima').length);
const completedReports = computed(() => reports.value.filter(report => report.status === 'selesai').length);
const anonymousReports = computed(() => reports.value.filter(report => report.is_anonymous).length);
const onlineReports = computed(() => reports.value.filter(report => report.type === 'online').length);
const offlineReports = computed(() => reports.value.filter(report => report.type === 'offline').length);

// Recent reports (last 5)
const recentReports = computed(() => {
  return [...reports.value]
    .sort((a, b) => new Date(b.created_at || b.id) - new Date(a.created_at || a.id))
    .slice(0, 5);
});

onMounted(() => {
  dataStore.fetchReports();
  dataStore.fetchStudents();
});

const handleCreateStudent = async () => {
  creationError.value = '';
  try {
    await dataStore.createStudent(newStudent.value);
    isDialogOpen.value = false;
    newStudent.value = { name: '', class: '', unique_code: '' };
  } catch (error) {
    if (error.response?.status === 422) {
      creationError.value = Object.values(error.response.data.errors).flat().join(' ');
    } else {
      creationError.value = 'Terjadi kesalahan pada server.';
    }
  }
};

const handleLogout = async () => {
  await authStore.logout();
};

const navigateToDetail = (reportId) => {
  router.push(`/konseling/${reportId}`);
};

const handleNavClick = (section) => {
  activeTab.value = section;
};

const getStatusColor = (status) => {
  switch(status) {
    case 'pending': return 'text-yellow-600 bg-yellow-50';
    case 'diterima': return 'text-blue-600 bg-blue-50';
    case 'selesai': return 'text-green-600 bg-green-50';
    default: return 'text-gray-600 bg-gray-50';
  }
};

const formatDate = (dateString) => {
  if (!dateString) return 'Baru saja';
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  });
};
</script>

<template>
  <div class="flex h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="h-16 flex items-center justify-center px-4 border-b border-gray-200 dark:border-gray-700">
          <h1 class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            BK Online
          </h1>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1">
          <button
            @click="handleNavClick('dashboard')"
            class="w-full text-left flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
            :class="activeTab === 'dashboard' 
              ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-600' 
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
            </svg>
            Dashboard
          </button>
          
          <button
            @click="handleNavClick('reports')"
            class="w-full text-left flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
            :class="activeTab === 'reports' 
              ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-600' 
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Laporan Konseling
          </button>
          
          <button
            @click="handleNavClick('students')"
            class="w-full text-left flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
            :class="activeTab === 'students' 
              ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-600' 
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
          >
            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            Manajemen Siswa
          </button>
        </nav>

        <!-- Footer -->
        <div class="px-3 py-4 border-t border-gray-200 dark:border-gray-700">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
              <span class="text-white text-sm font-medium">J</span>
            </div>
            <div class="ml-3 flex-1">
              <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Joveey</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">Konselor BK</p>
            </div>
            <button @click="handleLogout" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Logout">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-auto">
      <div class="p-6">
        <!-- Dashboard Tab -->
        <div v-if="activeTab === 'dashboard'">
          <!-- Header -->
          <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Selamat datang di sistem BK Online. Berikut ringkasan aktivitas konseling.</p>
          </div>

          <!-- Statistics Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <Card class="border-0 shadow-sm bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20">
              <CardContent class="p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Laporan</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ totalReports }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card class="border-0 shadow-sm bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20">
              <CardContent class="p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-lg">
                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Menunggu</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ pendingReports }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card class="border-0 shadow-sm bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20">
              <CardContent class="p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Selesai</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ completedReports }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card class="border-0 shadow-sm bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20">
              <CardContent class="p-6">
                <div class="flex items-center">
                  <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-lg">
                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ students.length }}</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Quick Stats and Recent Reports -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Quick Stats -->
            <Card class="border-0 shadow-sm">
              <CardHeader>
                <CardTitle class="text-lg">Statistik Laporan</CardTitle>
                <CardDescription>Breakdown berdasarkan jenis dan status</CardDescription>
              </CardHeader>
              <CardContent class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                  <span class="text-sm font-medium">Konseling Online</span>
                  <span class="text-sm font-bold text-blue-600">{{ onlineReports }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                  <span class="text-sm font-medium">Konseling Offline</span>
                  <span class="text-sm font-bold text-green-600">{{ offlineReports }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                  <span class="text-sm font-medium">Laporan Anonim</span>
                  <span class="text-sm font-bold text-purple-600">{{ anonymousReports }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                  <span class="text-sm font-medium">Sedang Ditangani</span>
                  <span class="text-sm font-bold text-orange-600">{{ acceptedReports }}</span>
                </div>
              </CardContent>
            </Card>

            <!-- Recent Reports -->
            <Card class="border-0 shadow-sm">
              <CardHeader>
                <CardTitle class="text-lg">Laporan Terbaru</CardTitle>
                <CardDescription>5 laporan yang baru masuk</CardDescription>
              </CardHeader>
              <CardContent>
                <div class="space-y-3">
                  <div v-if="recentReports.length === 0" class="text-center py-6 text-gray-500">
                    Belum ada laporan
                  </div>
                  <div v-else v-for="report in recentReports" :key="report.id" 
                       @click="navigateToDetail(report.id)"
                       class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
                    <div class="flex-1">
                      <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ report.title }}</p>
                      <p class="text-xs text-gray-500">{{ report.is_anonymous ? 'Anonim' : (report.student?.name || 'Siswa') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                      <span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusColor(report.status)]">
                        {{ report.status }}
                      </span>
                      <span class="text-xs text-gray-400">{{ formatDate(report.created_at) }}</span>
                    </div>
                  </div>
                </div>
                <div v-if="recentReports.length > 0" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                  <Button @click="handleNavClick('reports')" variant="outline" class="w-full">
                    Lihat Semua Laporan
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>

        <!-- Reports Tab -->
        <div v-else-if="activeTab === 'reports'" class="space-y-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Laporan Konseling</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola dan tanggapi laporan konseling siswa</p>
          </div>
          
          <Card class="border-0 shadow-sm">
            <CardContent class="p-6">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Judul Laporan</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Pelapor</TableHead>
                    <TableHead>Tanggal</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-if="isLoading"><TableCell colspan="4" class="text-center">Memuat data...</TableCell></TableRow>
                  <TableRow v-else-if="reports.length === 0"><TableCell colspan="4" class="text-center">Tidak ada laporan.</TableCell></TableRow>
                  <TableRow v-else v-for="report in reports" :key="report.id" @click="navigateToDetail(report.id)" class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                    <TableCell class="font-medium">{{ report.title }}</TableCell>
                    <TableCell>
                      <span :class="['px-2 py-1 text-xs rounded-full font-medium', getStatusColor(report.status)]">
                        {{ report.status }}
                      </span>
                    </TableCell>
                    <TableCell>{{ report.is_anonymous ? 'Anonim' : report.student?.name || '-' }}</TableCell>
                    <TableCell class="text-gray-500">{{ formatDate(report.created_at) }}</TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </div>

        <!-- Students Tab -->
        <div v-else-if="activeTab === 'students'" class="space-y-6">
          <div class="flex justify-between items-center">
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Manajemen Siswa</h1>
              <p class="text-gray-600 dark:text-gray-400">Kelola data siswa dan kode akses</p>
            </div>
            <Dialog v-model:open="isDialogOpen">
              <DialogTrigger as-child>
                <Button class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700">
                  + Tambah Siswa
                </Button>
              </DialogTrigger>
              <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                  <DialogTitle>Tambah Siswa Baru</DialogTitle>
                  <DialogDescription>Isi detail siswa di bawah ini. Kode unik akan digunakan siswa untuk login.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="handleCreateStudent" class="grid gap-4 py-4">
                  <div v-if="creationError" class="p-3 text-center bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-md text-sm">{{ creationError }}</div>
                  <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="name" class="text-right">Nama</Label>
                    <Input id="name" v-model="newStudent.name" required class="col-span-3" />
                  </div>
                  <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="class" class="text-right">Kelas</Label>
                    <Input id="class" v-model="newStudent.class" required class="col-span-3" />
                  </div>
                  <div class="grid grid-cols-4 items-center gap-4">
                    <Label for="unique_code" class="text-right">Kode Unik</Label>
                    <Input id="unique_code" v-model="newStudent.unique_code" required class="col-span-3" />
                  </div>
                  <DialogFooter>
                    <Button type="submit" :disabled="isLoading">{{ isLoading ? 'Menyimpan...' : 'Simpan Siswa' }}</Button>
                  </DialogFooter>
                </form>
              </DialogContent>
            </Dialog>
          </div>
          
          <Card class="border-0 shadow-sm">
            <CardContent class="p-6">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Nama</TableHead>
                    <TableHead>Kelas</TableHead>
                    <TableHead>Kode Unik</TableHead>
                    <TableHead>Status</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-if="isLoading"><TableCell colspan="4" class="text-center">Memuat data...</TableCell></TableRow>
                  <TableRow v-else-if="students.length === 0"><TableCell colspan="4" class="text-center">Belum ada siswa.</TableCell></TableRow>
                  <TableRow v-else v-for="student in students" :key="student.id">
                    <TableCell class="font-medium">{{ student.name }}</TableCell>
                    <TableCell>{{ student.class }}</TableCell>
                    <TableCell>
                      <code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-sm">{{ student.unique_code }}</code>
                    </TableCell>
                    <TableCell>
                      <span class="px-2 py-1 text-xs rounded-full font-medium bg-green-100 text-green-800">Aktif</span>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </div>
      </div>
    </main>
  </div>
</template>