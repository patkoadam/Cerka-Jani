<template>
    <div class="schedule-container">
        <div class="row">
            <!-- Naptár bal oldali rész -->
            <div class="col-9">
                <h2 class="schedule-header">Heti időbeosztás</h2>
                <div class="schedule-grid">
                    <!-- Idősáv oszlop -->
                    <div class="time-column" style="margin-top: auto;">
                        <div v-for="slot in timeSlots" :key="slot.time" class="time-slot">
                            {{ slot.time }} - {{ slot.endTime }}
                        </div>
                    </div>
                    <!-- Napok oszlopa -->
                    <div class="days-column">
                        <div v-for="day in days" :key="day" class="day-column">
                            <h3>{{ day }}</h3>
                            <div v-for="slot in timeSlots" :key="slot.time" class="hour-cell"
                                @click="openModal(day, slot.time, slot.endTime)">
                                <!-- Kilistázzuk az adott nap és időponthoz tartozó eseményeket -->
                                <div v-for="event in getEvents(day, slot.time)" :key="event.id" class="event-text"
                                    :title="event.title">
                                    {{ truncateText(event.title, 10) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Heti programok jobb oldali listája -->
            <div class="col-3">
                <h2 class="schedule-header">Heti programok</h2>
                <div class="events-list" v-if="events.length">
                    <ul>
                        <li v-for="e in events" :key="e.id" class="event-item">
                            <div class="event-card">
                                <div class="event-time">
                                    {{ e.day }} - {{ e.time }} - {{ e.endTime }}
                                </div>
                                <div class="event-title">{{ e.title }}</div>
                            </div>
                        </li>
                    </ul>
                </div>
                <p v-else>Nincsenek események.</p>
            </div>
        </div>

        <!-- Modal: Új esemény hozzáadása -->
        <div v-if="showModal" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h5 class="modal-title">Új esemény hozzáadása</h5>
                    <button class="btn-close" @click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <input v-model="newEventTitle" type="text" class="form-control" placeholder="Esemény címe" />
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="closeModal">Mégse</button>
                    <button class="btn btn-primary" @click="saveEvent">Mentés</button>
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
            // Napok, amiket a naptárban használunk
            days: ["Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat", "Vasárnap"],
            // Idősávok, amelyek cellákra bontják a naptárat
            timeSlots: [
                { time: "08:00", endTime: "08:40" },
                { time: "08:55", endTime: "09:35" },
                { time: "09:50", endTime: "10:30" },
                { time: "10:55", endTime: "11:35" },
                { time: "11:50", endTime: "12:30" },
                { time: "12:45", endTime: "13:25" },
                { time: "13:40", endTime: "14:20" },
                { time: "14:30", endTime: "15:10" },
                { time: "15:20", endTime: "16:00" },
                { time: "16:10", endTime: "16:50" },
                { time: "17:00", endTime: "17:40" },
                { time: "17:50", endTime: "18:30" },
                { time: "18:40", endTime: "19:20" }
            ],
            // Az események tömbje, melyet a GET lekérés tölt be
            events: [],
            // Új esemény felvételéhez szükséges változók
            newEventTitle: "",
            selectedDay: "",
            selectedTime: "",
            selectedEndTime: "",
            showModal: false
        };
    },
    mounted() {

        fetch('http://localhost:8000/sanctum/csrf-cookie', {
            credentials: 'include',
        }).then(console.log("asd"));

        fetch("http://localhost:8000/api/events")
            .then(response => response.json())
            .then(data => {
                // Magyar napnevek, 0 = Vasárnap, 1 = Hétfő, ...
                const hunDays = ["Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat"];


                // A kapott adatokat átalakítjuk a naptárban elvárt formátumra
                const transformed = data.map(event => {
                    // Példa: event.day = "2025-03-09", event.time = "10:55:00"
                    const date = new Date(event.day);
                    const dayIndex = date.getDay(); // 0..6 (0 = Vasárnap)

                    const dayName = hunDays[dayIndex];
                    console.log(dayName);

                    // "HH:MM:SS" → "HH:MM"
                    const shortTime = event.time ? event.time.slice(0, 5) : "";
                    const shortEndTime = event.end_time ? event.end_time.slice(0, 5) : "";

                    return {
                        ...event,
                        day: dayName,       // például "Hétfő"
                        time: shortTime,    // például "10:55"
                        endTime: shortEndTime
                    };
                });

                // Most már az events tömb napja/time-ja egyezik a naptár logikájával
                this.events = transformed;
            })
            .catch(error => console.error("Error fetching events:", error));
    },
    methods: {
        // Segédfüggvény: a kiválasztott napnév alapján kiszámolja a dátumot (aktuális hét)
        getDateForDay(dayName) {
            const mapping = {
                "Hétfő": 1,
                "Kedd": 2,
                "Szerda": 3,
                "Csütörtök": 4,
                "Péntek": 5,
                "Szombat": 6,
                "Vasárnap": 0
            };
            let currentDate = new Date();
            let targetDay = mapping[dayName];
            let currentDay = currentDate.getDay(); // 0-6, ahol 0 a Vasárnap
            let diff = targetDay - currentDay;
            let targetDate = new Date(currentDate);
            targetDate.setDate(currentDate.getDate() + diff);
            let year = targetDate.getFullYear();
            let month = String(targetDate.getMonth() + 1).padStart(2, "0");
            let day = String(targetDate.getDate()).padStart(2, "0");
            return `${year}-${month}-${day}`;
        },
        // Megnyitja a modalt a cella kiválasztása alapján
        openModal(day, time, endTime) {
            this.selectedDay = day;
            this.selectedTime = time;
            this.selectedEndTime = endTime;
            this.newEventTitle = "";
            this.showModal = true;
        },
        // Bezárja a modalt
        closeModal() {
            this.showModal = false;
        },
        // Új esemény mentése: helyben hozzáadjuk, majd elküldjük a backendnek
        saveEvent() {
            if (!this.newEventTitle.trim()) return;
            // Számoljuk ki a dátumot a kiválasztott nap alapján (aktuális hét)
            const date = this.getDateForDay(this.selectedDay);
            const newEvent = {
                id: Date.now(), // ideiglenes azonosító
                date: date,     // A backend számára kötelező mező
                day: this.selectedDay,
                time: this.selectedTime,
                endTime: this.selectedEndTime,
                title: this.newEventTitle
            };
            // Helyben frissítjük az eseményeket
            this.events.push(newEvent);

            // Egyszerű POST kérés, az új eseményt küldjük a szerverre
            fetch("http://localhost:8000/api/events", {
                method: "POST",
                credentials: 'include',
                headers: { 
                    "Content-Type": "application/json",
                    'Accept': 'application/json'
                 },
                body: JSON.stringify(newEvent)
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Event saved:", data);
                })
                .catch(error => console.error("Error saving event:", error));
            this.closeModal();
        },
        // Megkeresi azokat az eseményeket, amelyek egyeznek a cella napjával és kezdési időpontjával
        getEvents(day, time) {

            return this.events.filter(event => event.day === day && event.time === time);
        },
        // Levágja a szöveget, ha túl hosszú
        truncateText(text, length) {
            return text.length > length ? text.substring(0, length) + "..." : text;
        }
    }
};
</script>

<style scoped>
/* Konténer a táblázatnak és a modalnak */
.schedule-container {
    margin: 0 auto;
    padding: 20px;
    width: auto;
    background: #fff;
    border-radius: 10px;
}

.schedule-table th,
.schedule-table td {
    vertical-align: middle;
    text-align: center;
    cursor: pointer;
}

/* Az esemény rövid szövege a cellában */
.event-text {
    display: inline-block;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Eseménylista stílusok */
.events-list {
    margin-top: 20px;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
}

.event-item {
    margin-bottom: 10px;
}

.event-card {
    background: #fff;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.event-time {
    font-weight: bold;
    color: #1e3a8a;
}

/* Modal (felugró ablak) */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 300px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    margin: 10px 0;
}

.btn-close {
    background: transparent;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
}




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
}

.sidebar a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 10px;
    background: #23395B;
    border-radius: 5px;
    text-align: center;
    cursor: pointer;
}

.sidebar a:hover {
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

.schedule-container {
    background: #FFFFFF;
    border-radius: 10px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    padding: 20px;
    width: 100%;
    overflow-y: auto;
}

.schedule-header {
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 10px;
    color: #1E3A8A;
}

.schedule-grid {
    display: flex;
    align-items: stretch;
    width: 100%;
}

.time-column {
    width: 120px;
    display: flex;
    flex-direction: column;
}

.time-slot {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid #23395B;
    color: #1E3A8A;
    font-weight: bold;
    background: #E3E8F0;
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
    height: 60px;
    border: 1px solid #23395B;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
    background: #F0F4F8;
    width: 100%;
}
</style>