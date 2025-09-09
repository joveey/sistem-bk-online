import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

// Import Views
import StudentLoginView from '../views/StudentLoginView.vue';
import TeacherLoginView from '../views/TeacherLoginView.vue';
// Placeholder views for dashboards (buat file kosongnya agar tidak error)
import StudentDashboardView from '../views/StudentDashboardView.vue';
import TeacherDashboardView from '../views/TeacherDashboardView.vue';
import CounselingDetailView from '../views/CounselingDetailView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'student-login',
      component: StudentLoginView,
      meta: { requiresGuest: true }
    },
    {
      path: '/guru/login',
      name: 'teacher-login',
      component: TeacherLoginView,
      meta: { requiresGuest: true }
    },
    {
      path: '/dashboard',
      name: 'student-dashboard',
      component: StudentDashboardView,
      meta: { requiresAuth: true, role: 'student' }
    },
    {
      path: '/guru/dashboard',
      name: 'teacher-dashboard',
      component: TeacherDashboardView,
      meta: { requiresAuth: true, role: 'counselor' }
    },
    {
      path: '/konseling/:id',
      name: 'counseling-detail',
      component: CounselingDetailView,
      meta: { requiresAuth: true } // Otorisasi lebih spesifik dilakukan di dalam komponen
    }
  ]
});

// Navigation Guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const isAuthenticated = authStore.isAuthenticated;
  const userRole = authStore.role;

  // Jika route butuh login
  if (to.meta.requiresAuth) {
    if (!isAuthenticated) {
      // Jika tidak login, redirect ke halaman login siswa sebagai default
      next({ name: 'student-login' });
    } else {
      // Jika sudah login, cek role jika diperlukan
      if (to.meta.role && to.meta.role !== userRole) {
        // Role tidak sesuai, redirect ke dashboard yang sesuai
        if (userRole === 'counselor') {
          next({ name: 'teacher-dashboard' });
        } else {
          next({ name: 'student-dashboard' });
        }
      } else {
        // Login dan role sesuai (atau role tidak dicek), lanjutkan
        next();
      }
    }
  } 
  // Jika route hanya untuk tamu (halaman login)
  else if (to.meta.requiresGuest && isAuthenticated) {
    // Jika sudah login, redirect ke dashboard yang sesuai
    if (userRole === 'counselor') {
      next({ name: 'teacher-dashboard' });
    } else {
      next({ name: 'student-dashboard' });
    }
  } 
  // Untuk route yang tidak memiliki meta-field
  else {
    next();
  }
});


export default router;
