<template>
  <div>
    <div class="main-container">
      <nav class="sidebar">
        <div style="margin-top: -30px;">
          <img src="/images/cerka.png" alt="Cerka" style="width: 170px; height: 170px;">
        </div>
        <div v-if="isTeacher">
          <ul>
            <li @click="switchComponent('kezdolap')">Kezdőlap</li>
            <li @click="switchComponent('Tosztaly')">Osztályok</li>
            <li @click="switchComponent('Tjegyek')">Jegyek</li>
            <li @click="switchComponent('Thianyzasok')">Hiányzások</li>
            <li @click="Logout()">Kijelentkezés</li>
          </ul>
        </div>
        <div v-else>
          <ul>
            <li @click="switchComponent('kezdolap')">Kezdőlap</li>
            <li @click="switchComponent('naptar')">Naptár</li>
            <li @click="switchComponent('orarend')">Órarend</li>
            <li @click="switchComponent('jegyek')">Jegyek</li>
            <li @click="switchComponent('uzenetek')">Üzenetek</li>
            <li @click="switchComponent('hianyzasok')">Hiányzások</li>
            <li @click="switchComponent('profil')">Profil</li>
            <li @click="Logout()">Kijelentkezés</li>
          </ul>
        </div>
      </nav>
      <div class="content-container">
        <component :is="currentComponent" />
      </div>
    </div>
  </div>
</template>

<script>
import naptar from "/resources/js/Pages/naptar.vue";
import jegyek from "/resources/js/Pages/jegyek.vue";
import kezdolap from "/resources/js/Pages/kezdolap.vue";
import orarend from "/resources/js/Pages/orarend.vue";
import uzenetek from "/resources/js/Pages/uzenetek.vue";
import hianyzasok from "/resources/js/Pages/hianyzasok.vue";
import data from "/resources/js/Pages/data.vue";
import Tjegyek from "/resources/js/Pages/Tjegyek.vue";
import Thianyzasok from "/resources/js/Pages/Thianyzasok.vue";
import Tosztaly from "/resources/js/Pages/Tosztaly.vue";

export default {
  name: "Main",
  components: {
    naptar,
    jegyek,
    kezdolap,
    orarend,
    uzenetek,
    hianyzasok,
    data,
    Tjegyek,
    Thianyzasok,
    Tosztaly,
  },
  data() {
    return {
      currentComponent: "kezdolap",
      role: []
    };
  },
  mounted() {
  fetch("http://localhost:8000/roles")
    .then(response => response.json())
    .then(data => {
      this.role = data;
      console.log("Fetched roles:", this.role);
    });
},
  computed: {
    userRole() {
      let roleData = this.$page.props.auth?.user?.role;
      if (!roleData && this.role.length > 0) {
        roleData = this.role.find(r => r.id === this.$page.props.auth.user.role_id);
      }
      return roleData ? roleData.name : "";
    },
    isTeacher() {
      return this.userRole === "Tanár";
    },
  },
  methods: {
    switchComponent(componentName) {
      this.currentComponent = componentName;
    },
    Logout() {
      delete axios.defaults.headers.common["Authorization"];
      this.$inertia.post("/logout", {}, { preserveState: true });
    },
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
