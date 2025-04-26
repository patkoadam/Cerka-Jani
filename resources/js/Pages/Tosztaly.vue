<template>
<div id="hatter"style="height: 100%;">
    <div class="class-groups-container">
        <h2>Osztályok</h2>

        <!-- Új osztály létrehozása -->
        <form @submit.prevent="createClass" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col">
                    <label for="class-name" class="form-label">Osztály neve</label>
                    <input id="class-name" type="text" v-model="newClass.name" class="form-control"
                        placeholder="Új osztály neve..." required />
                </div>
                <div class="col">
                    <label for="class-desc" class="form-label">Leírás</label>
                    <input id="class-desc" type="text" v-model="newClass.description" class="form-control"
                        placeholder="(opcionális)" />
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Létrehozás</button>
                </div>
            </div>
        </form>

        <!-- Létező osztályok listája -->
        <div v-for="group in classGroups" :key="group.id" class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ group.name }}</h5>
                <p class="card-text" v-if="group.description">{{ group.description }}</p>

                <!-- Diákok listája -->
                <ul class="list-group mb-3">
                    <li v-for="student in classStudents[group.id] || []" :key="student.id"
                        class="list-group-item d-flex justify-content-between align-items-center">
                        {{ student.name }} ({{ student.email }})
                        <button class="btn btn-sm btn-danger"
                            @click="removeStudent(group.id, student.id)">Eltávolítás</button>
                    </li>
                    <li v-if="!(classStudents[group.id] || []).length" class="list-group-item text-muted">
                        Nincs diák ebben az osztályban.
                    </li>
                </ul>

                <!-- Diák hozzáadása -->
                <div class="input-group">
                    <input type="text" v-model="groupSearch[group.id]" @input="searchStudents(group.id)"
                        class="form-control" placeholder="Keress diákot név vagy email alapján..." />
                    <button class="btn btn-outline-secondary" @click="addStudent(group.id, selectedForGroup[group.id])"
                        :disabled="!selectedForGroup[group.id]">
                        +
                    </button>
                </div>
                <ul class="list-group mt-2" v-if="groupResults[group.id] && groupResults[group.id].length">
                    <li v-for="student in groupResults[group.id]" :key="student.id"
                        class="list-group-item list-group-item-action" @click="selectStudent(group.id, student)">
                        {{ student.name }} ({{ student.email }})
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
</template>
  
<script>
import axios from 'axios';

export default {
    name: 'ClassGroups',
    data() {
        return {
            newClass: { name: '', description: '' },
            classGroups: [],
            classStudents: {},       // { [groupId]: [students] }
            groupSearch: {},         // { [groupId]: query }
            groupResults: {},        // { [groupId]: [search results] }
            selectedForGroup: {},    // { [groupId]: studentId }
        };
    },
    methods: {
        async loadClassGroups() {
            try {
                const { data } = await axios.get('/class-groups');
                this.classGroups = data;
                data.forEach(g => {
                    this.fetchStudents(g.id);
                    this.groupSearch[g.id] = '';
                    this.groupResults[g.id] = [];
                    this.selectedForGroup[g.id] = null;
                    this.classStudents[g.id] = [];
                });
            } catch (e) {
                console.error(e);
            }
        },

        async createClass() {
            if (!this.newClass.name.trim()) return;
            try {
                const payload = {
                    name: this.newClass.name.trim(),
                    description: this.newClass.description.trim() || null
                };
                const { data } = await axios.post('/class-groups', payload);
                // reset form
                this.newClass.name = '';
                this.newClass.description = '';
                this.classGroups.push(data);
                this.classStudents[data.id] = [];
                this.groupSearch[data.id] = '';
                this.groupResults[data.id] = [];
                this.selectedForGroup[data.id] = null;
            } catch (e) {
                console.error(e);
            }
        },

        async fetchStudents(groupId) {
            try {
                const { data } = await axios.get(`/class-groups/${groupId}/students`);
                this.classStudents[groupId] = data;
            } catch (e) {
                console.error(e);
            }
        },

        async searchStudents(groupId) {
            const q = this.groupSearch[groupId];
            if (!q || q.length < 3) {
                this.groupResults[groupId] = [];
                return;
            }
            try {
                const { data } = await axios.get('/students', { params: { query: q } });
                this.groupResults[groupId] = data;
            } catch (e) {
                console.error(e);
            }
        },

        selectStudent(groupId, student) {
            this.selectedForGroup[groupId] = student.id;
            this.groupSearch[groupId] = student.name;
            this.groupResults[groupId] = [];
        },

        async addStudent(groupId, studentId) {
            if (!studentId) return;
            try {
                await axios.post(`/class-groups/${groupId}/students`, { user_id: studentId });
                this.fetchStudents(groupId);
                this.groupSearch[groupId] = '';
                this.selectedForGroup[groupId] = null;
            } catch (e) {
                console.error(e);
            }
        },

        async removeStudent(groupId, studentId) {
            try {
                await axios.delete(`/class-groups/${groupId}/students/${studentId}`);
                this.fetchStudents(groupId);
            } catch (e) {
                console.error(e);
            }
        }
    },
    mounted() {
        this.loadClassGroups();
    }
};
</script>
  
<style scoped>
#hatter{
  background: linear-gradient(to bottom, #ffffff, #3d79e7);
}
.class-groups-container {
    padding: 1rem;
}

.card {
    border-radius: 0.5rem;
}

.input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.input-group .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
</style>
  