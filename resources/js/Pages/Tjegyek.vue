<template>
    <div class="grades-page container">
      <h2>Jegyek kezelése</h2>
  
      <!-- Osztály választó -->
      <div class="mb-4">
        <select v-model="selectedClassId" @change="loadData" class="form-select">
          <option value="">-- Válassz osztályt --</option>
          <option v-for="c in classes" :key="c.id" :value="c.id">
            {{ c.name }}
          </option>
        </select>
      </div>
  
      <!-- Diákok és jegyek -->
      <div v-if="students.length">
        <div v-for="student in students" :key="student.id" class="card mb-3">
          <div class="card-body">
            <h5>{{ student.name }}</h5>
            <div v-for="(grades, subj) in student.grades" :key="subj" class="mb-3">
              <h6>{{ subj }}</h6>
              <ul class="list-group mb-2">
                <li v-for="g in grades" :key="g.id" class="list-group-item d-flex justify-content-between">
                  <div>
                    <strong>{{ g.grade }}</strong>
                    <small class="text-muted">({{ g.dated }})</small>
                    <p v-if="g.remarks" class="mb-0 small text-secondary">"{{ g.remarks }}"</p>
                  </div>
                </li>
              </ul>
              <!-- Jegy és megjegyzés hozzáadása -->
              <div class="input-group">
                <input
                  type="number"
                  v-model.number="newGrades[student.id][subj]"
                  class="form-control"
                  min="1"
                  max="5"
                  step="1"
                  placeholder="Jegy"
                />
                <input
                  type="text"
                  v-model="newRemarks[student.id][subj]"
                  class="form-control"
                  placeholder="Megjegyzés (opcionális)"
                />
                <button class="btn btn-success" @click="addGrade(student.id, subj)">+
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <p v-else class="text-muted">Válassz osztályt a jegyek megtekintéséhez.</p>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  export default {
    name: 'GradesPage',
    data() {
      return {
        classes: [],
        subjects: [],
        students: [],
        selectedClassId: '',
        newGrades: {},
        newRemarks: {},
      };
    },
    methods: {
      async loadData() {
        if (!this.selectedClassId) return;
        const { data } = await axios.get('/grades', {
          params: { class_id: this.selectedClassId }
        });
        this.classes = data.classes;
        this.subjects = data.subjects;
        this.students = data.students;
  
        const gradesTemp = {};
        const remarksTemp = {};
        this.students.forEach(s => {
          gradesTemp[s.id] = {};
          remarksTemp[s.id] = {};
          this.subjects.forEach(sub => {
            gradesTemp[s.id][sub.name] = '';
            remarksTemp[s.id][sub.name] = '';
          });
        });
        this.newGrades = gradesTemp;
        this.newRemarks = remarksTemp;
      },
      async addGrade(studentId, subjectName) {
        const subj = this.subjects.find(s => s.name === subjectName);
        const value = this.newGrades[studentId][subjectName];
        const note = this.newRemarks[studentId][subjectName] || null;
        if (!value) return;
        await axios.post('/grades', {
          student_id: studentId,
          subject_id: subj.id,
          grade: value,
          remarks: note
        });
        await this.loadData();
      }
    },
    async mounted() {
      const { data } = await axios.get('/grades');
      this.classes = data.classes;
      this.subjects = data.subjects;
    }
  };
  </script>
  
  <style scoped>
  .grades-page { padding: 20px; }
  </style>