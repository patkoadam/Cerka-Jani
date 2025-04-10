<template>
  <div>
    <div class="main-container">
      <nav class="sidebar">
        <ul>
          <!-- Sidebar elemek, amelyekre kattintva vált a tartalom -->
          <li @click="switchComponent('kezdolap')">Kezdőlap</li>
          <li @click="switchComponent('naptar')">Naptár</li>
          <li @click="switchComponent('orarend')">Órarend</li>
          <li @click="switchComponent('jegyek')">Jegyek</li>
          <li @click="switchComponent('uzenetek')">Üzenetek</li>
          <li @click="switchComponent('hianyzasok')">Hiányzások</li>
          <li @click="switchComponent('profil')">Profil</li>
          <li @click="Logout()">Kijelentkezés</li>
        </ul>
      </nav>
      <div class="content-container">
        <component :is="currentComponent" />
      </div>
    </div>
  </div>
</template>

<script>

import naptar from "/resources/js/Pages/naptar.vue";
import jegyek from "/resources/js/pages/jegyek.vue";
import kezdolap from "/resources/js/Pages/kezdolap.vue";
import orarend from "/resources/js/Pages/orarend.vue";
import uzenetek from "/resources/js/Pages/uzenetek.vue";
import hianyzasok from "/resources/js/Pages/hianyzasok.vue";
import data from "/resources/js/Pages/data.vue";

export default {
  components: {
    naptar,
    jegyek,
    kezdolap,
    orarend,
    uzenetek,
    hianyzasok,
    data
  },
  data() {
    return {
      currentComponent: "Login"
    };
  },
  methods: {
    switchComponent(componentName) {
      this.currentComponent = componentName;
    },

    Logout() {
      delete axios.defaults.headers.common["Authorization"];
      this.$inertia.post("/logout", {}, { preserveState: true });
    }
  },
};
</script>

<style>
.main-container {
  display: flex;
  background: #F0F4F8;
  height: 100vh;
}

.sidebar {
  width: 200px;
  background: #1E3A8A;
  color: white;
  padding: 20px;
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
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

.content-container {
  flex: 1;
  margin-left: 220px;
  padding: 20px;
  background: #FFFFFF;
  border-radius: 10px;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
  overflow-y: auto;
}
</style>