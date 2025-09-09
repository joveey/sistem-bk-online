import { defineStore } from 'pinia';
import { ref } from 'vue';
import apiClient from '@/api/axios';

export const useDataStore = defineStore('data', () => {
    // === STATE ===
    const reports = ref([]);
    const students = ref([]);
    const reportDetail = ref(null);
    const chats = ref([]);
    const isLoading = ref(false);

    // === ACTIONS ===

    /**
     * Mengambil daftar laporan dari backend.
     */
    async function fetchReports() {
        isLoading.value = true;
        try {
            const response = await apiClient.get('/reports');
            reports.value = response.data.data || response.data;
        } catch (error) {
            console.error('Failed to fetch reports:', error);
            reports.value = [];
        } finally {
            isLoading.value = false;
        }
    }

    /**
     * Mengambil daftar semua siswa (hanya untuk guru).
     */
    async function fetchStudents() {
        isLoading.value = true;
        try {
            const response = await apiClient.get('/guru/students');
            students.value = response.data.data || response.data;
        } catch (error) {
            console.error('Failed to fetch students:', error);
            students.value = [];
        } finally {
            isLoading.value = false;
        }
    }

    /**
     * Mengirim data siswa baru ke backend.
     * @param {object} studentData
     */
    async function createStudent(studentData) {
        isLoading.value = true;
        try {
            const response = await apiClient.post('/guru/students', studentData);
            students.value.push(response.data.data);
        } catch (error) {
            console.error('Error creating student:', error);
            throw error;
        } finally {
            isLoading.value = false;
        }
    }

    /**
     * Mengirim data laporan baru ke backend.
     * @param {object} reportData
     */
    async function createReport(reportData) {
        isLoading.value = true;
        try {
            const response = await apiClient.post('/reports', reportData);
            reports.value.unshift(response.data.data);
        } catch (error) {
            console.error('Error creating report:', error);
            throw error;
        } finally {
            isLoading.value = false;
        }
    }

    /**
     * Mengambil detail dari satu laporan spesifik.
     * @param {string|number} id - ID Laporan.
     */
    async function fetchReportDetail(id) {
        isLoading.value = true;
        // Reset state untuk mencegah kebocoran data dari halaman sebelumnya
        reportDetail.value = null;
        chats.value = [];
        try {
            const response = await apiClient.get(`/reports/${id}`);
            reportDetail.value = response.data;
            if (response.data.type === 'online') {
                await fetchChats(id);
            }
        } catch (error) {
            console.error('Failed to fetch report details:', error);
        } finally {
            isLoading.value = false;
        }
    }

    /**
     * Mengambil riwayat chat untuk sebuah laporan.
     * @param {string|number} reportId
     */
    async function fetchChats(reportId) {
        try {
            const response = await apiClient.get(`/reports/${reportId}/chats`);
            chats.value = response.data;
        } catch (error) {
            console.error('Failed to fetch chats:', error);
        }
    }

    /**
     * Mengirim pesan chat baru.
     * @param {object} payload - { reportId, message }
     */
    async function postChatMessage(payload) {
        const response = await apiClient.post(`/reports/${payload.reportId}/chats`, { message: payload.message });
        chats.value.push(response.data);
    }

    /**
     * Aksi Guru: Menerima laporan.
     * @param {string|number} reportId
     */
    async function acceptReport(reportId) {
        const response = await apiClient.put(`/reports/${reportId}/accept`);
        if (reportDetail.value && reportDetail.value.id == reportId) {
            reportDetail.value = response.data;
        }
    }

    /**
     * Aksi Guru: Menandai laporan selesai.
     * @param {string|number} reportId
     */
    async function completeReport(reportId) {
        const response = await apiClient.put(`/reports/${reportId}/complete`);
        if (reportDetail.value && reportDetail.value.id == reportId) {
            reportDetail.value = response.data;
        }
    }

    return {
        reports,
        students,
        isLoading,
        reportDetail,
        chats,
        fetchReports,
        fetchStudents,
        createStudent,
        createReport,
        fetchReportDetail,
        fetchChats,
        postChatMessage,
        acceptReport,
        completeReport,
    };
});

