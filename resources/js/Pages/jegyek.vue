<template>
  <div class="container my-5">
    <div class="card">
      <div class="card-header">
        <h1 class="mb-0">Saját Jegyeim</h1>
      </div>
      <div class="card-body">
        <!-- Hibák, üzenetek megjelenítése -->
        <div v-if="message" class="alert"
          :class="{ 'alert-success': messageType === 'success', 'alert-danger': messageType === 'error' }">
          {{ message }}
        </div>

        <!-- Jegyek táblázatának megjelenítése responsív módon -->
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="thead-light">
              <tr>
                <th>Tantárgy</th>
                <th>Jegy</th>
                <th>Tanár</th>
                <th>Dátum</th>
                <th>Megjegyzés</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="grade in grades.grades.data" :key="grade.id">
                <td>{{ grade.subject ? grade.subject.name : 'N/A' }}</td>
                <td>{{ grade.grade }}</td>
                <td>{{ grade.teacher ? grade.teacher.name : 'N/A' }}</td>
                <td>{{ grade.graded_at }}</td>
                <td>{{ grade.remarks }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Lapozás Bootstrap-os oldalszámozással -->
        <nav aria-label="Page navigation">
          <ul class="pagination justify-content-center">
            <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
              <button class="page-link" @click="prevPage" :disabled="!pagination.prev_page_url">Előző</button>
            </li>
            <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
              <button class="page-link" @click="nextPage" :disabled="!pagination.next_page_url">Következő</button>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>
  
<script>
import axios from 'axios';

export default {
  name: 'StudentGradesComponent',
  data() {
    return {
      grades: {},
      message: '',
      messageType: '',
      pagination: {}
    };
  },
  created() {
    this.fetchGrades();
  },
  methods: {
    // Diák saját jegyeinek lekérése
    fetchGrades(pageUrl = '/grades') {
      axios.get(pageUrl)
        .then(response => {
          console.log('API válasz:', response.data);
          this.grades = response.data;
          this.pagination = response.data;
        })
        .catch(error => {
          this.message = 'Hiba történt a jegyek lekérésekor.';
          this.messageType = 'error';
          console.error('Hiba a jegyek lekérésekor:', error);
        });
    },
    // Lapozás előző oldalra
    prevPage() {
      if (this.pagination.prev_page_url) {
        this.fetchGrades(this.pagination.prev_page_url);
      }
    },
    // Lapozás következő oldalra
    nextPage() {
      if (this.pagination.next_page_url) {
        this.fetchGrades(this.pagination.next_page_url);
      }
    }
  }
};
</script>
  
<!-- Ha Bootstrap-et használsz, nem feltétlenül van szükség extra scoped stílusokra -->
<style scoped>
/* Például, ha szeretnél egy kis extra margin-t a táblázat alatt */
.table-responsive {
  margin-bottom: 1.5rem;
}
</style>
