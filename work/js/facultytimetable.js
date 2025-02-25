$(document).ready(function () {

    console.log("Faculty Timetable JS loaded");

    if ($('.faculty-timetable-cont').length > 0) {

        console.log("Faculty Timetable page detected");

        // DOM Elements
        const timetableContainer = document.getElementById("weeklyTimetable");
        const showAllButton = document.getElementById("showAllButton");


        const facultyId = document.getElementById("faculty_id").value;

        // Fetch timetable data from API
        $.ajax({
            url: "/api/app/get/tt/facultytimetable",
            type: "POST",
            data: {
                faculty_id: facultyId
            },
            success: function (response) {
                if (response.success) {
                    const timetableData = response.timetable;
                    populateTimetable(timetableData);
                } else {
                    console.error("Failed to fetch timetable data");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error occurred while fetching timetable:", error);
            }
        });

        // Populate timetable
        function populateTimetable(timetableData) {
            const daysOrder = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const today = new Date().toLocaleString("en-US", {
                weekday: "long"
            });

            daysOrder.forEach((day, index) => {
                const dayClasses = timetableData[day] || [];
                let classDetails = "";

                if (dayClasses.length > 0) {
                    dayClasses.forEach((slot) => {
                        classDetails += `
                    <div class="class-details mb-2 bg-dark-light">
                        <p><strong>${slot.department} - ${slot.class} - ${slot.section}</strong></p>
                        <p>${slot.time} | Sem: ${slot.semester}</p>
                    </div>
                `;
                    });
                } else {
                    classDetails = `<p class="no-classes">No classes scheduled</p>`;
                }

                timetableContainer.innerHTML += `
            <div class="col" data-order="${index}">
                <div class="card day-card shadow-sm ${day === today ? "current-day" : ""}" data-day="${day}">
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

            // Sort and reinitialize Masonry
            const elements = Array.from(timetableContainer.children);
            elements.sort((a, b) => a.getAttribute("data-order") - b.getAttribute("data-order"));
            elements.forEach((element) => timetableContainer.appendChild(element));

            const msnry = new Masonry(timetableContainer, {
                itemSelector: ".col",
                columnWidth: ".col",
                percentPosition: true
            });

            // Initially blur other days
            toggleShowAll(msnry);
        }

        // Toggle between showing all days and only the current day
        function toggleShowAll(msnry) {
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

    }
});