// Detailed View Pagination
$(document).ready(function () {

    console.log('summaryattstudent.js loaded');

    if ($('#summary-attendance').length > 0) {

        const rowsPerPage = 5;

        document.querySelectorAll('tbody[id^="detailsBody-"]').forEach(body => {
            const rows = Array.from(body.children);
            const subjectCode = body.id.split('-')[1];
            const pagination = document.getElementById(`pagination-${subjectCode}`);

            function renderTable(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                rows.forEach((row, index) => {
                    row.style.display = index >= start && index < end ? '' : 'none';
                });
            }

            function renderPagination() {
                const totalPages = Math.ceil(rows.length / rowsPerPage);
                pagination.innerHTML = '';
                for (let i = 1; i <= totalPages; i++) {
                    const li = document.createElement('li');
                    li.className = 'page-item';
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.addEventListener('click', () => {
                        renderTable(i);
                        pagination.querySelectorAll('.page-item').forEach(el => el.classList.remove('active'));
                        li.classList.add('active');
                    });
                    pagination.appendChild(li);
                }
                if (pagination.firstChild) pagination.firstChild.classList.add('active');
                renderTable(1);
            }

            renderPagination();
        });

        // Bar Chart Data
        const barChartData = {
            labels: JSON.parse($('#summ-subjects').val()),
            datasets: [{
                label: 'Attendance Percentage',
                data: JSON.parse($('#summ-percentages').val()),
                backgroundColor: '#4e73df',
                borderColor: '#375a7f',
                borderWidth: 1
            }]
        };

        // Bar Chart Config
        const barChartConfig = {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Subjects'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Attendance (%)'
                        },
                        max: 100
                    }
                }
            }
        };

        // Pie Chart Data
        const pieChartData = {
            labels: JSON.parse($('#summ-subjects').val()),
            datasets: [{
                label: 'Subject Contribution',
                data: JSON.parse($('#summ-percentages').val()),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69']
            }]
        };

        // Pie Chart Config
        const pieChartConfig = {
            type: 'pie',
            data: pieChartData,
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.raw}%`
                        }
                    }
                }
            }
        };

        // Render Charts
        new Chart(document.getElementById('attendanceBarChart'), barChartConfig);
        new Chart(document.getElementById('attendancePieChart'), pieChartConfig);
    }
});