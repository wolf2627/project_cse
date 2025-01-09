<style>
    .day-card {
        border: none;
        border-radius: 10px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .day-card:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        font-size: 1.25rem;
        font-weight: bold;
        text-transform: uppercase;
    }

    .card-body {
        font-size: 0.9rem;
    }

    .class-details {
        padding: 10px;
        border: 1px dashed #ddd;
        border-radius: 8px;
        background: var(--bs-body-bg);
        margin-bottom: 10px;
    }

    .no-classes {
        color: var(--bs-secondary);
        font-style: italic;
        text-align: center;
    }

    .highlight-today {
        border: 2px solid #007bff;
    }
</style>

<div class="container mt-4">
    <h1 class="text-center mb-4 fw-bold">Weekly Timetable</h1>
    <!-- Weekly Timetable Cards -->
    <div class="row g-4" id="weeklyTimetable">
        <!-- Timetable content will be dynamically inserted here -->
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        
        const timetableData = {
            Monday: [{
                    class: "CSE112",
                    semester: "5",
                    batch: "2022-2026",
                    section: "A",
                    time: "08:45-09:40"
                },
                {
                    class: "CSE203",
                    semester: "1",
                    batch: "2024-2028",
                    section: "B",
                    time: "02:35-03:25"
                }
            ],
            Tuesday: [{
                class: "CSE112",
                semester: "5",
                batch: "2022-2026",
                section: "A",
                time: "09:40-10:35"
            }],
            Wednesday: [{
                class: "CSE112",
                semester: "5",
                batch: "2022-2026",
                section: "A",
                time: "10:55-11:45"
            }],
            Thursday: [],
            Friday: [{
                class: "CSE204",
                semester: "2",
                batch: "2024-2028",
                section: "C",
                time: "01:00-01:50"
            }],
            Saturday: [],
            Sunday: []
        };

        const timetableContainer = document.getElementById("weeklyTimetable");
        const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        const today = new Date().getDay();
        const todayName = daysOfWeek[today];

        Object.keys(timetableData).forEach((day) => {
            const dayClasses = timetableData[day];
            let classDetails = "";

            if (dayClasses.length > 0) {
                dayClasses.forEach((slot) => {
                    classDetails += `
                    <div class="class-details">
                        <p><strong>Class:</strong> ${slot.class}</p>
                        <p><strong>Time:</strong> ${slot.time}</p>
                        <p><strong>Semester:</strong> ${slot.semester}</p>
                        <p><strong>Batch:</strong> ${slot.batch}</p>
                        <p><strong>Section:</strong> ${slot.section}</p>
                    </div>
                `;
                });
            } else {
                classDetails = `<p class="no-classes">No classes scheduled</p>`;
            }

            timetableContainer.innerHTML += `
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card day-card shadow-sm ${day === todayName ? 'highlight-today' : ''}">
                    <div class="card-header bg-primary text-white text-center">
                        ${day}
                    </div>
                    <div class="card-body">
                        ${classDetails}
                    </div>
                </div>
            </div>
        `;
        });
    });
</script>
