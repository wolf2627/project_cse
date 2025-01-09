<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Weekly Timetable</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ece9e6, #ffffff);
            min-height: 100vh;
        }
        .day-card {
            min-height: 200px;
            border: none;
            border-radius: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .day-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .card-header {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .card-body {
            font-size: 0.9rem;
        }
        .icon {
            font-size: 2rem;
        }
        .no-classes {
            color: #888;
            font-style: italic;
        }
        .day-header {
            background: linear-gradient(135deg, #2193b0, #6dd5ed);
            color: #fff;
            border-radius: 15px 15px 0 0;
            padding: 10px;
            text-align: center;
        }
        .current-day {
            background: linear-gradient(135deg, #f7971e, #ffd200);
        }
        .class-details {
            padding: 10px;
            border: 1px dashed #ddd;
            border-radius: 10px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4 fw-bold">Weekly Timetable</h1>

        <!-- Weekly Timetable Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="weeklyTimetable">
            <!-- Dynamic day cards -->
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dynamic Content Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const timetableData = {
                Monday: [
                    { class: "CSE112", semester: "5", batch: "2022-2026", section: "A", time: "08:45-09:40" },
                    { class: "CSE203", semester: "1", batch: "2024-2028", section: "B", time: "02:35-03:25" }
                ],
                Tuesday: [
                    { class: "CSE112", semester: "5", batch: "2022-2026", section: "A", time: "09:40-10:35" }
                ],
                Wednesday: [
                    { class: "CSE112", semester: "5", batch: "2022-2026", section: "A", time: "10:55-11:45" }
                ],
                Thursday: [],
                Friday: [
                    { class: "CSE204", semester: "2", batch: "2024-2028", section: "C", time: "01:00-01:50" }
                ],
                Saturday: [],
                Sunday: []
            };

            const today = new Date().toLocaleString("en-US", { weekday: "long" });
            const timetableContainer = document.getElementById("weeklyTimetable");

            Object.keys(timetableData).forEach((day) => {
                const dayClasses = timetableData[day];
                let classDetails = "";

                if (dayClasses.length > 0) {
                    dayClasses.forEach((slot) => {
                        classDetails += `
                            <div class="class-details mb-2">
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
                    <div class="col">
                        <div class="card day-card shadow-sm ${day === today ? 'current-day' : ''}">
                            <div class="day-header">
                                <i class="icon fa-solid fa-calendar-day"></i>
                                <div>${day}</div>
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
</body>
</html>
