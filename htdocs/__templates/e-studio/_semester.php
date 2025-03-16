<style>
    /* Remove default blue color for active links */
    #semester-page .nav-link.active {
        background-color: transparent;
        color: white;
    }

    /* Hover effect for all links */
    #semester-page .nav-link:hover {
        background-color: #007bff;
        color: white;
    }

    /* Active button hover effect */
    #semester-page .nav-link.active:hover {
        /* background-color: #0056b3; */
    }

    /* Container for the semester navigation with a colorful background */
    #semester-page .semester-box {
        /* background: linear-gradient(to right, #6a11cb, #2575fc); */
        border-radius: 3px;
        padding: 3px;
        margin-bottom: 15px;
        /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
        border: 2px solid #f39c12;
    }

    #semester-page .semester-box .nav-pills {
        /* display: flex; */
        justify-content: center;
        gap: 5px;
    }

    #semester-page .nav-link {
        font-weight: bold;
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 5px;
    }

    #semester-page .nav-link.active {
        background-color: #f39c12;
        color: white;
    }

    #semester-page .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }

    #semester-page .card {
        width: 15rem;
        height: auto;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        border: 2px solid #007bff;
        /* border-radius: 8px; */
    }

    #semester-page .card-body {
        flex-grow: 1;
    }

    #semester-page .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-color: #f39c12;
    }

    #semester-page .card-link {
        text-decoration: none;
    }

    /* Subject details styles */
    #subject-details {
        display: none;
    }

    /* Styles for different sections */
    .section {
        margin: 20px 0;
    }

    .section a {
        display: block;
        margin-top: 10px;
        color: #007bff;
        text-decoration: none;
    }

    .section a:hover {
        color: #0056b3;
    }

    .pdf-card,
    .video-card,
    .material-card {
        border: 1px solid #ddd;
        padding: 10px;
        margin: 10px;
        border-radius: 8px;
    }

    .pdf-card h3,
    .video-card h3,
    .material-card h3 {
        margin: 0 0 10px;
    }

    .pdf-card a,
    .video-card a,
    .material-card a {
        color: #007bff;
        text-decoration: none;
    }

    .pdf-card a:hover,
    .video-card a:hover,
    .material-card a:hover {
        text-decoration: underline;
    }

    /* Style for embedded YouTube iframe */
    .video-iframe {
        width: 100%;
        height: 200px;
        /* Reduced height */
        border: none;
    }

    /* Fix for subject navigation links */
    .subject-nav-box {
        /* background: linear-gradient(to right, #ff7e5f, #feb47b); */
        border-radius: 8px;
        padding: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    /* Style for subject navigation links on hover */
    .subject-nav-box .nav-link.active {
        background-color: #f39c12;
        color: white;
    }

    .subject-nav-box .nav-link:hover {
        background-color: #0056b3;
        color: white;
    }

    /* Adjust the cards for consistent layout */
    .card {
        width: 15rem;
        height: auto;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        border: 2px solid #007bff;
        border-radius: 8px;
    }

    .card-body {
        flex-grow: 1;
    }

    /* Grid layout for YouTube videos */
    .video-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
    }

    .video-card {
        width: 15rem;
        margin: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .video-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .video-card iframe {
        width: 100%;
        height: 200px;
        border: none;
    }

    .video-card .card-body {
        padding: 10px;
    }

    .video-card .card-title {
        margin: 10px 0;
        font-weight: bold;
    }
</style>


<div class="container" id="semester-page">
    <!-- Semester Navigation Box -->
    <div class="semester-box">
        <header class="d-flex justify-content-center py-1">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="#" class="nav-link active" data-semester="1">Semester 1</a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-semester="2">Semester 2</a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-semester="3">Semester 3</a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-semester="4">Semester 4</a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-semester="5">Semester 5</a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-semester="6">Semester 6</a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-semester="7">Semester 7</a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-semester="8">Semester 8</a></li>
            </ul>
        </header>
    </div>

    <!-- Subjects Container -->
    <div id="subjects-container" class="card-container">
        <!-- Subjects will be dynamically loaded here based on the selected semester -->
    </div>

    <!-- Subject Details Section (hidden by default) -->
    <div id="subject-details">
        <h1 id="subject-title"></h1>
        <div class="subject-nav-box">
            <nav class="nav">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a href="#assignments" class="nav-link active">Assignments</a></li>
                    <li class="nav-item"><a href="#lecture-videos" class="nav-link">Lecture Videos</a></li>
                    <li class="nav-item"><a href="#syllabus" class="nav-link">Syllabus</a></li>
                    <li class="nav-item"><a href="#materials" class="nav-link">Materials</a></li>
                </ul>
            </nav>
        </div>

        <div id="assignments" class="section">
            <h2>Assignments</h2>
            <div id="pdf-cards-container" class="card-container">
                <!-- Dynamic cards will be inserted here -->
            </div>
        </div>
        <div id="lecture-videos" class="section">
            <h2>Lecture Videos</h2>
            <div class="video-container">
                <!-- Dynamic embedded YouTube iframes will be inserted here -->
            </div>
        </div>
        <div id="syllabus" class="section">
            <h2>Syllabus</h2>
            <div class="pdf-card">
                <h3>Course Syllabus</h3>
                <p>Here is the syllabus for the subject.</p>
            </div>
        </div>
        <div id="materials" class="section">
            <h2>Materials</h2>
            <div class="material-card-container card-container">
                <!-- Materials will be displayed here -->
            </div>
        </div>
    </div>
</div>

<script>
    const subjects = {
        1: [{
                title: "Problem solving and python programming",
                description: "Learn the basics of programming using Python.",
                link: "subject1"
            },
            {
                title: "Calculus for Engineers",
                description: "Introduction to differential calculus.",
                link: "subject2"
            },
        ],

        2: [{
            title: "Quantum physics",
            description: "Learn about emerging quantum basics",
            link: "subject3"
        }]
    };

    function loadSubjects(semester) {
        const subjectsContainer = document.getElementById("subjects-container");
        subjectsContainer.innerHTML = '';

        const subjectList = subjects[semester];
        if (subjectList) {
            subjectList.forEach(subject => {
                const card = document.createElement("div");
                card.classList.add("card");

                const cardBody = document.createElement("div");
                cardBody.classList.add("card-body");

                const cardTitle = document.createElement("h5");
                cardTitle.classList.add("card-title");
                cardTitle.textContent = subject.title;

                const cardDescription = document.createElement("p");
                cardDescription.classList.add("card-text");
                cardDescription.textContent = subject.description;

                const cardLink = document.createElement("a");
                cardLink.classList.add("card-link");
                cardLink.href = "#";
                cardLink.textContent = "More Details";

                cardLink.addEventListener('click', function() {
                    loadSubjectDetails(subject);
                });

                cardBody.appendChild(cardTitle);
                cardBody.appendChild(cardDescription);
                cardBody.appendChild(cardLink);
                card.appendChild(cardBody);
                subjectsContainer.appendChild(card);
            });
        }
    }

    function loadSubjectDetails(subject) {
        // Hide the semester subjects and show the subject details
        document.getElementById("subjects-container").style.display = "none";
        document.getElementById("subject-details").style.display = "block";

        // Set the title of the subject
        document.getElementById("subject-title").textContent = subject.title;

        loadPdfCards(); // Load PDF cards for Assignments
        loadLectureVideos(); // Load Lecture Videos
        loadMaterials(); // Load Materials

        setupSubjectNavLinks(); // Setup the subject navigation links
    }

    function setupSubjectNavLinks() {
        const navLinks = document.querySelectorAll('.subject-nav-box .nav-link');

        navLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();

                // Remove active class from all links
                navLinks.forEach(l => l.classList.remove('active'));

                // Add active class to clicked link
                this.classList.add('active');

                // Show the corresponding section
                const targetSection = document.querySelector(this.getAttribute('href'));
                document.querySelectorAll('.section').forEach(section => section.style.display = 'none');
                targetSection.style.display = 'block';
            });
        });

        // Set the default active link and section
        document.querySelector('.subject-nav-box .nav-link.active').click();
    }

    const semesterLinks = document.querySelectorAll('#semester-page .nav-link');
    semesterLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();

            // Remove 'active' class from all links
            semesterLinks.forEach(l => l.classList.remove('active'));

            // Add 'active' class to the clicked link
            this.classList.add('active');

            // Get the semester number from the clicked link's data-semester attribute
            const semester = this.getAttribute('data-semester');
            loadSubjects(semester);
        });
    });

    loadSubjects(1); // Load default semester (Semester 1)

    // Sample array of PDF data for Assignments
    const pdfAssignments = [{
            title: "Assignment 1",
            pdfUrl: "path/to/assignment1.pdf"
        },
        {
            title: "Assignment 2",
            pdfUrl: "path/to/assignment2.pdf"
        },
        {
            title: "Assignment 3",
            pdfUrl: "path/to/assignment3.pdf"
        }
    ];

    // Sample array of YouTube links for Lecture Videos
    const videoLinks = [{
            title: "Water Jug Problem",
            url: "https://www.youtube.com/embed/v_OnLO0evhE?si=-c_F6Vdx19yaD8uV"
        },
        {
            title: "Lecture 2",
            url: "https://www.youtube.com/embed/v_OnLO0evhE?si=-c_F6Vdx19yaD8uV"
        }
    ];

    // Function to create PDF cards
    function loadPdfCards() {
        const container = document.getElementById('pdf-cards-container');
        pdfAssignments.forEach(pdf => {
            const card = document.createElement('div');
            card.classList.add('pdf-card');

            card.innerHTML = `
                <h3>${pdf.title}</h3>
                <a href="${pdf.pdfUrl}" target="_blank">View PDF</a>
            `;
            container.appendChild(card);
        });
    }

    // Function to embed YouTube videos with updated iframe size
    function loadLectureVideos() {
        const container = document.querySelector('.video-container');
        container.innerHTML = ''; // Clear existing content

        videoLinks.forEach(video => {
            const videoCard = document.createElement('div');
            videoCard.classList.add('video-card');

            videoCard.innerHTML = `
                <iframe src="${video.url}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <div class="card-body">
                    <h5 class="card-title">${video.title}</h5>
                    <p class="card-text">Some details about this video.</p>
                </div>
            `;

            container.appendChild(videoCard);
        });
    }


    // Function to load materials as cards
    function loadMaterials() {
        const materials = [{
                title: "Material 1",
                url: "material1.pdf"
            },
            {
                title: "Material 2",
                url: "material2.pdf"
            }
        ];

        const container = document.querySelector('.material-card-container');
        materials.forEach(material => {
            const card = document.createElement('div');
            card.classList.add('material-card');
            card.innerHTML = `
                <h3>${material.title}</h3>
                <a href="${material.url}" target="_blank">Download</a>
            `;
            container.appendChild(card);
        });
    }
</script>