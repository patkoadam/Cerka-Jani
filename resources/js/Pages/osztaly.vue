<template>
    <div>
        <h2>Heti órarend</h2>
        <div class="d-flex justify-content-between mb-3">
            <button disabled class="btn btn-secondary">&laquo; Előző</button>
            <span>{{ weekDisplay }}</span>
            <button disabled class="btn btn-secondary">Következő &raquo;</button>
        </div>
        <div class="schedule-grid">
            <div class="time-column">…</div>
            <div class="days-column">
                <div v-for="day in days" :key="day" class="day-column">
                    <h5>{{ day }}</h5>
                    <div v-for="slot in timeSlots" :key="slot.time" class="hour-cell">
                        <span v-if="getAssigned(day, slot.time)">{{ getAssigned(day, slot.time).subject.name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script>
import axios from 'axios';
export default {
    data() {
        return {
            days: [/*…*/], timeSlots: [/*…*/],
            currentMonday: getMonday(new Date()), assignments: []
        }
    },
    computed: { weekDisplay() { /*…*/ } },
    methods: {
        loadAssignments() { /* ugyanaz mint Teacher, csak fetchSchedules */ },
        getAssigned(day, time) { /*…*/ }
    },
    mounted() { this.loadAssignments() }

}

function getMonday(d) {
    const date = new Date(d);
    const day = date.getDay();
    const diff = date.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(date.setDate(diff));
}

// YYYY-MM-DD formátum
function formatDate(date) {
    const y = date.getFullYear();
    let m = (date.getMonth() + 1).toString();
    let d = date.getDate().toString();
    if (m.length < 2) m = '0' + m;
    if (d.length < 2) d = '0' + d;
    return `${y}-${m}-${d}`;
}

// nap hozzáadása
function addDays(date, days) {
    const r = new Date(date);
    r.setDate(r.getDate() + days);
    return r;
}

// ISO dátumból magyar napnév
function daysFromDate(iso) {
    const hun = ["Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat"];
    return hun[new Date(iso).getDay()];
}

// date mezőbe a kiválasztott nap ISO dátuma
function getDateForDay(mondayDate, dayName) {
    const mapping = {
        "Hétfő": 1,
        "Kedd": 2,
        "Szerda": 3,
        "Csütörtök": 4,
        "Péntek": 5,
        "Szombat": 6,
        "Vasárnap": 0,
    };
    const idx = mapping[dayName];
    // hétfő indexe 1, vasárnap 0 → így állítjuk be
    const base = new Date(mondayDate);
    const diff = (idx === 0 ? 6 : idx - 1);
    base.setDate(base.getDate() + diff);
    return formatDate(base);
}
</script>
<style scoped></style>