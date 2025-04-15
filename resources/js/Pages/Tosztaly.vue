<template>
    <div>
        <h2>Új Osztály Létrehozása</h2>
        <form @submit.prevent="createClass">
            <div class="mb-3">
                <label for="className" class="form-label">Osztály neve</label>
                <input type="text" id="className" v-model="newClass.name" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="classDesc" class="form-label">Leírás</label>
                <input type="text" id="classDesc" v-model="newClass.description" class="form-control" />
            </div>
            <button type="submit" class="btn btn-primary">Létrehozás</button>
        </form>

        <!-- Miután az osztály létrejött -->
        <div v-if="classGroup.id" class="mt-4">
            <h3>Diákok hozzáadása az osztályhoz</h3>
            <!-- Kereső mező a diákokra -->
            <div class="mb-3">
                <input type="text" v-model="searchQuery" @input="searchStudents"
                    placeholder="Keress diákokat név vagy email alapján..." class="form-control" />
            </div>
            <!-- Keresési eredmények listája -->
            <div v-if="searchResults.length">
                <div v-for="student in searchResults" :key="student.id"
                    class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ student.name }} ({{ student.email }})</span>
                    <button class="btn btn-sm btn-success" @click="addStudent(student.id)">
                        Hozzáadás
                    </button>
                </div>
            </div>
            <!-- Aktuálisan az osztályhoz tartozó diákok listája -->
            <div class="mt-3" v-if="classGroupStudents.length">
                <h4>Aktuális osztálytagok:</h4>
                <ul class="list-group">
                    <li v-for="student in classGroupStudents" :key="student.id"
                        class="list-group-item d-flex justify-content-between align-items-center">
                        {{ student.name }}
                        <button class="btn btn-sm btn-danger" @click="removeStudent(student.id)">
                            Eltávolítás
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
  
<script>
import axios from "axios";

export default {
    name: "Osztalyok",
    data() {
        return {
            newClass: {
                name: "",
                description: "",
            },
            classGroup: {},           // Az újaként létrehozott osztály adatai
            searchQuery: "",
            searchResults: [],        // A keresési eredmények listája (csak a keresés alapján)
            classGroupStudents: [],   // Az osztályba felvett diákok listája
            message: "",
        };
    },
    methods: {
        createClass() {
            axios
                .post("/api/class-groups", this.newClass)
                .then((response) => {
                    this.classGroup = response.data; // response.data.id → kellene, hogy legyen!
                    this.message = "Osztály sikeresen létrejött!";
                    this.fetchClassGroupStudents();
                    console.log(this.classGroup);
                })
                .catch((error) => {
                    console.error("Osztály létrehozása hiba:", error);
                    this.message = "Hiba az osztály létrehozásakor.";
                });
        },

        fetchClassGroupStudents() {
            if (!this.classGroup.id) {
                console.error("Class group ID is undefined.");
                return;
            }
            axios
                .get(`/api/class-groups/${this.classGroup.id}/students`)
                .then((response) => {
                    this.classGroupStudents = response.data;
                    console.log(this.classGroupStudents);
                })
                .catch((error) => {
                    console.error("Hiba az osztálytagok lekérésekor:", error);
                });
        },
        addStudent(studentId) {
            // Diák hozzáadása az osztályhoz
            axios
                .post(`/api/class-groups/${this.classGroup.id}/students`, { user_id: studentId })
                .then(() => {
                    this.fetchClassGroupStudents();
                    // Opció: töröld a hozzáadott diákot a keresési eredmények közül
                    this.searchResults = this.searchResults.filter(s => s.id !== studentId);
                })
                .catch((error) => {
                    console.error("Hiba diák hozzáadásakor:", error);
                });
        },
        removeStudent(studentId) {
            // Diák eltávolítása az osztályból
            axios
                .delete(`/api/class-groups/${this.classGroup.id}/students/${studentId}`)
                .then(() => {
                    this.fetchClassGroupStudents();
                })
                .catch((error) => {
                    console.error("Hiba diák eltávolításakor:", error);
                });
        },
        searchStudents() {
            // Minimum 3 karakternél indítsuk a keresést
            if (this.searchQuery.length > 2) {
                axios
                    .get(`/api/students?query=${this.searchQuery}`)
                    .then((response) => {
                        this.searchResults = response.data;
                    })
                    .catch((error) => {
                        console.error("Hiba diákok keresésekor:", error);
                    });
            } else {
                this.searchResults = [];
            }
        },
    },
};
</script>
  
<style scoped>
/* Használd a Bootstrap-os osztályokat vagy szükséges egyedi stílusokat */
</style>
  