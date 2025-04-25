<template>
  <div class="main-container">
    <!-- Sidebar -->
    <nav :class="['sidebar', { open: isSidebarOpen }]">
      <div class="logo-container">
        <img src="/images/cerka.png" alt="Cerka logo">
      </div>
      <ul v-if="isTeacher">
        <li @click="switchComponent('kezdolap')">Kezdőlap</li>
        <li @click="switchComponent('naptar')">Naptár</li>
        <li @click="switchComponent('Tosztaly')">Osztályok</li>
        <li @click="switchComponent('Torarend')">Órarend</li>
        <li @click="switchComponent('Tjegyek')">Jegyek</li>
        <li @click="switchComponent('profile')">Profil</li>
        <li @click="logout()">Kijelentkezés</li>
      </ul>
      <ul v-else>
        <li @click="switchComponent('kezdolap')">Kezdőlap</li>
        <li @click="switchComponent('naptar')">Naptár</li>
        <li @click="switchComponent('osztaly')">Osztály</li>
        <li @click="switchComponent('orarend')">Órarend</li>
        <li @click="switchComponent('jegyek')">Jegyek</li>
        <li @click="switchComponent('profile')">Profil</li>
        <li @click="logout()">Kijelentkezés</li>
      </ul>
    </nav>

    <!-- Content -->
    <div class="content-container">
      <!-- Hamburger on mobile -->
      <button class="sidebar-toggle" @click="toggleSidebar" aria-label="Toggle menu">
        ☰
      </button>

      <component :is="currentComponent" />
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import naptar from '/resources/js/Pages/naptar.vue';
import profile from '/resources/js/Pages/profile.vue';
import kezdolap from '/resources/js/Pages/kezdolap.vue';
import jegyek from '/resources/js/Pages/jegyek.vue';
import orarend from '/resources/js/Pages/orarend.vue';
import osztaly from '/resources/js/Pages/osztaly.vue';
import Torarend from '/resources/js/Pages/Torarend.vue';
import Tjegyek from '/resources/js/Pages/Tjegyek.vue';
import Tosztaly from '/resources/js/Pages/Tosztaly.vue';

export default {
  name: 'Main',
  components: {
    naptar,
    profile,
    kezdolap,
    jegyek,
    orarend,
    osztaly,
    Torarend,
    Tjegyek,
    Tosztaly,
  },
  data() {
    return {
      currentComponent: 'kezdolap',
      roleList: [],
      isSidebarOpen: false,
    };
  },
  computed: {
    userRole() {
      let roleData = this.$page.props.auth?.user?.role;
      if (!roleData && this.roleList.length) {
        roleData = this.roleList.find(r => r.id === this.$page.props.auth.user.role_id);
      }
      return roleData?.name || '';
    },
    isTeacher() {
      return this.userRole === 'Tanár';
    },
  },
  methods: {
    switchComponent(name) {
      this.currentComponent = name;
      this.isSidebarOpen = false; // auto-close on mobile
    },
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    },
    logout() {
      delete axios.defaults.headers.common['Authorization'];
      this.$inertia.post('/logout', {}, { preserveState: true });
    },
    async fetchRoles() {
      try {
        const response = await fetch('http://localhost:8000/roles');
        this.roleList = await response.json();
      } catch (e) {
        console.error('Unable to fetch roles:', e);
      }
    }
  },
  async mounted() {
    await this.fetchRoles();
  }
};
</script>

<style scoped>
/* Base layout */
.main-container {
  display: flex;
  min-height: 100vh;
  background: #F0F4F8;
}

/* Sidebar */
.sidebar {
  width: 220px;
  background: #1E3A8A;
  color: white;
  padding: 20px;
  box-sizing: border-box;
  transition: transform 0.3s ease;
}

.sidebar .logo-container {
  text-align: center;
  margin-bottom: 30px;
}

.sidebar .logo-container img {
  max-width: 100%;
  height: auto;
}

.sidebar ul {
  list-style: none;
  padding: 0;
}

.sidebar li {
  margin-bottom: 15px;
  cursor: pointer;
  padding: 10px;
  background: #23395B;
  border-radius: 5px;
  text-align: center;
  transition: background 0.3s;
}

.sidebar li:hover {
  background: #1E3A8A;
}

/* Content */
.content-container {
  flex: 1;
  padding: 20px;
  background: #FFF;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
  overflow-y: auto;
  position: relative;
}

/* Hamburger toggle */
.sidebar-toggle {
  display: none;
  position: absolute;
  top: 15px;
  left: 15px;
  font-size: 24px;
  background: transparent;
  border: none;
  color: #1E3A8A;
  cursor: pointer;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .main-container {
    flex-direction: column;
  }

  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    transform: translateX(-100%);
    z-index: 1000;
  }

  .sidebar.open {
    transform: translateX(0);
  }

  .content-container {
    margin-left: 0;
    padding-top: 60px;
  }

  .sidebar-toggle {
    display: block;
  }
}</style>
