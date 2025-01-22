<div class="mt-2 faculty-timetable-cont">
    <!-- Show All Button -->
    <input type="hidden" id="faculty_id" value="<?=Session::getUser()->getFacultyId() ; ?>">
    <h1 class="text-center mb-4 fw-bold">Weekly Timetable</h1>
    <button class="show-all-btn btn btn-primary mb-2 mt-2" id="showAllButton" onclick="toggleShowAll()">Show All</button>

    <!-- Weekly Timetable Cards -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-6 g-6" id="weeklyTimetable">
        <!-- Dynamic day cards -->
    </div>
</div>