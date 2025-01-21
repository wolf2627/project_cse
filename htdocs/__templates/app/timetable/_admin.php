<style>
    .day-card {
        min-height: 200px;
        border: none;
        border-radius: 15px;
        transition: transform 0.3s, box-shadow 0.3s, filter 0.3s;
    }

    .day-card:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .blurred {
        filter: blur(4px);
        pointer-events: none;
    }

    .blurred:hover {
        filter: none;
        pointer-events: auto;
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
        color: #fff;
    }

    .class-details {
        padding: 10px;
        border: 1px dashed #ddd;
        border-radius: 10px;
        /* background: #f9f9f9; */
    }

    .no-classes {
        color: #888;
        font-style: italic;
    }

    .show-all-btn {
        position: fixed;
        /* top: 20px;
        right: 20px;
        z-index: 1000; */
        /* background-color: #007bff; */
        /* color: white; */
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    /* .show-all-btn:hover {
        background-color: #0056b3;
    } */
</style>


<div class="container mt-4">
    <!-- Show All Button -->
    <button class="show-all-btn btn btn-primary" id="showAllButton" onclick="toggleShowAll()">Show All</button>
    <h1 class="text-center mb-4 fw-bold">Weekly Timetable</h1>

    <!-- Weekly Timetable Cards -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="weeklyTimetable">
        <!-- Dynamic day cards -->
    </div>
</div>



<!-- Masonry JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js"></script>

<!-- Dynamic Content Script -->
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

        const daysOrder = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        const today = new Date().toLocaleString("en-US", {
            weekday: "long"
        });
        const timetableContainer = document.getElementById("weeklyTimetable");
        const showAllButton = document.getElementById("showAllButton");

        // Populate timetable
        daysOrder.forEach((day, index) => {
            const dayClasses = timetableData[day];
            let classDetails = "";

            if (dayClasses.length > 0) {
                dayClasses.forEach((slot) => {
                    classDetails += `
                            <div class="class-details mb-2 bg-dark-light">
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
                    <div class="col" data-order="${index}">
                        <div class="card day-card shadow-sm ${day === today ? 'current-day' : ''}" data-day="${day}">
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

        // Sort elements by data-order attribute
        const elements = Array.from(timetableContainer.children);
        elements.sort((a, b) => a.getAttribute('data-order') - b.getAttribute('data-order'));
        elements.forEach(element => timetableContainer.appendChild(element));

        // Initialize Masonry
        const msnry = new Masonry(timetableContainer, {
            itemSelector: '.col',
            columnWidth: '.col',
            percentPosition: true
        });

        // Toggle between showing all days and only the current day
        function toggleShowAll() {
            const dayCards = document.querySelectorAll(".day-card");
            const isShowingAll = showAllButton.textContent === "Show All";

            dayCards.forEach((card) => {
                if (!card.classList.contains("current-day")) {
                    if (isShowingAll) {
                        card.classList.remove("blurred");
                    } else {
                        card.classList.add("blurred");
                    }
                }
            });

            showAllButton.textContent = isShowingAll ? "Show Only Today" : "Show All";
            msnry.layout();
        }

        window.toggleShowAll = toggleShowAll;

        // Initially blur other days
        toggleShowAll();
    });
</script>