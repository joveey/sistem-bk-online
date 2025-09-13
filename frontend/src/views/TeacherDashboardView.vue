<script setup>
import { ref, onMounted, computed } from 'vue';
import { useDataStore } from '@/stores/dataStore';
import { useRouter } from 'vue-router';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

const dataStore = useDataStore();
const router = useRouter();

const newStudent = ref({ name: '', class: '', unique_code: '' });
const creationError = ref('');
const isDialogOpen = ref(false);

const students = computed(() => dataStore.students);
const reports = computed(() => dataStore.reports);
const isLoading = computed(() => dataStore.isLoading);

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

const navigateToDetail = (reportId) => {
  router.push(`/konseling/${reportId}`);
};
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold">Laporan & Siswa</h1>
      <p class="text-gray-500 dark:text-gray-400">Kelola laporan konseling dan data siswa.</p>
    </div>

    <Tabs default-value="reports" class="w-full">
      <TabsList class="grid w-full grid-cols-2">
        <TabsTrigger value="reports">Laporan Konseling</TabsTrigger>
        <TabsTrigger value="students">Manajemen Siswa</TabsTrigger>
      </TabsList>

      <TabsContent value="reports">
        <div class="bg-white dark:bg-gray-800/50 shadow rounded-lg p-6 mt-4">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Judul Laporan</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Pelapor</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="isLoading"><TableCell colspan="3" class="text-center">Memuat data...</TableCell></TableRow>
              <TableRow v-else-if="reports.length === 0"><TableCell colspan="3" class="text-center">Tidak ada laporan.</TableCell></TableRow>
              <TableRow v-else v-for="report in reports" :key="report.id" @click="navigateToDetail(report.id)" class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                <TableCell class="font-medium">{{ report.title }}</TableCell>
                <TableCell class="capitalize">{{ report.status }}</TableCell>
                <TableCell>{{ report.is_anonymous ? 'Anonim' : report.student?.name || '-' }}</TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </TabsContent>

      <TabsContent value="students">
        <div class="bg-white dark:bg-gray-800/50 shadow rounded-lg mt-4">
           <div class="px-6 py-4 flex justify-between items-center border-b dark:border-gray-700">
                <h2 class="text-lg font-medium">Daftar Siswa</h2>
                <Dialog v-model:open="isDialogOpen">
                  <DialogTrigger as-child><Button>+ Tambah Siswa</Button></DialogTrigger>
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
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Nama</TableHead>
                <TableHead>Kelas</TableHead>
                <TableHead>Kode Unik</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
               <TableRow v-if="isLoading"><TableCell colspan="3" class="text-center">Memuat data...</TableCell></TableRow>
               <TableRow v-else-if="students.length === 0"><TableCell colspan="3" class="text-center">Belum ada siswa.</TableCell></TableRow>
              <TableRow v-else v-for="student in students" :key="student.id">
                <TableCell class="font-medium">{{ student.name }}</TableCell>
                <TableCell>{{ student.class }}</TableCell>
                <TableCell>{{ student.unique_code }}</TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </TabsContent>
    </Tabs>
  </div>
</template>