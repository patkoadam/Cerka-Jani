<template>
    <div class="schedule-container">
        <h2 class="schedule-header">Saját tanórarendem</h2>
        <div class="d-flex justify-content-between mb-3">
            <button @click="prevWeek" class="btn btn-secondary">Előző hét</button>
            <span>{{ weekDisplay }}</span>
            <button @click="nextWeek" class="btn btn-secondary">Következő hét</button>
        </div>

        <div v-if="assignments.length" class="schedule-grid">
            <!-- időoszlop -->
            <div class="time-column" style="margin-top: auto;">
                <div v-for="slot in timeSlots" :key="slot.time" class="time-slot">
                    {{ slot.time }}–{{ slot.endTime }}
                </div>
            </div>

            <!-- napok -->
            <div class="days-column">
                <div v-for="day in days" :key="day" class="day-column">
                    <h5>{{ day }}</h5>
                    <div v-for="slot in timeSlots" :key="slot.time" class="hour-cell">
                        <span v-if="assigned(day, slot.time)">
                            {{ assigned(day, slot.time).subject.name }}
                        </span>
                        <span v-else class="text-muted">–</span>
                    </div>
                </div>
            </div>
        </div>

        <p v-else class="text-muted">Nincs órarend ehhez a héthez.</p>
    </div>
</template>
  
<script>
import axios from 'axios';

export default {
    name: 'StudentSchedule',
    data() {
        return {
            currentMonday: this.getMonday(new Date()),
            days: ['Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek'],
            timeSlots: [
                { time: '08:00', endTime: '08:40' },
                { time: '08:55', endTime: '09:35' },
                { time: '09:50', endTime: '10:30' },
                { time: '10:55', endTime: '11:35' },
                { time: '11:50', endTime: '12:30' },
                { time: '12:45', endTime: '13:25' },
                { time: '13:40', endTime: '14:20' },
                { time: '14:30', endTime: '15:10' },
                { time: '15:20', endTime: '16:00' },
                { time: '16:10', endTime: '16:50' },
                { time: '17:00', endTime: '17:40' },
                { time: '17:50', endTime: '18:30' },
                { time: '18:40', endTime: '19:20' }
            ],
            assignments: []
        };
    },
    computed: {
        weekDisplay() {
            const start = this.currentMonday;
            const end = this.addDays(start, 6);
            return `${this.formatDate(start)} – ${this.formatDate(end)}`;
        }
    },
    methods: {
        async loadAssignments() {
            const start = this.formatDate(this.currentMonday);
            const end = this.formatDate(this.addDays(this.currentMonday, 6));
            console.log('Lekérdezés kezdete:', start, end);
            try {
                const response = await axios.get('/self/schedule', { params: { start, end } });
                console.log('Szerver válasz:', response.data);
                const hunDays = ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'];
                this.assignments = response.data.map(ev => ({
                    dayName: hunDays[new Date(ev.date).getDay()],
                    time: ev.time.slice(0, 5),
                    subject: ev.subject
                }));
            } catch (error) {
                console.error('Hiba az órarend lekérésekor:', error);
                this.assignments = [];
            }
        },

        assigned(day, time) {
            return this.assignments.find(a => a.dayName === day && a.time === time) || null;
        },

        prevWeek() {
            this.currentMonday = this.addDays(this.currentMonday, -7);
            this.loadAssignments();
        },
        nextWeek() {
            this.currentMonday = this.addDays(this.currentMonday, 7);
            this.loadAssignments();
        },

        // segédfüggvények
        getMonday(d) {
            const date = new Date(d);
            const day = date.getDay();
            const diff = date.getDate() - day + (day === 0 ? -6 : 1);
            return new Date(date.setDate(diff));
        },
        addDays(d, n) {
            const r = new Date(d);
            r.setDate(r.getDate() + n);
            return r;
        },
        formatDate(d) {
            const Y = d.getFullYear();
            const M = String(d.getMonth() + 1).padStart(2, '0');
            const D = String(d.getDate()).padStart(2, '0');
            return `${Y}-${M}-${D}`;
        }
    },
    mounted() {
        this.loadAssignments();
    }
};
</script>
  
<style scoped>
.schedule-container {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
}

.schedule-grid {
    display: flex;
}

.time-column {
    width: 120px;
    display: flex;
    flex-direction: column;
}

.time-slot {
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid #23395B;
    background: #E3E8F0;
    font-weight: bold;
    color: #1E3A8A;
}

.days-column {
    flex: 1;
    display: flex;
}

.day-column {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.hour-cell {
    height: 50px;
    width: 100%;
    border: 1px solid #23395B;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F0F4F8;
}
</style>
  