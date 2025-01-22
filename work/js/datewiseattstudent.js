$(document).ready(function () {

    if ($('#date-wise-stud-atten').length > 0) {

        // Attendance Chart Data
        const attendanceData = JSON.parse(document.getElementById('attendanceData').value);

        const attendanceChartData = {
            labels: ['Present', 'Absent', 'On-Duty', 'Not Marked'],
            datasets: [{
                data: [
                    attendanceData.summary.total_present ?? 0,
                    attendanceData.summary.total_absent ?? 0,
                    attendanceData.summary.total_on_duty ?? 0,
                    attendanceData.summary.total_not_marked ?? 0
                ],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#6c757d'],
                hoverOffset: 4
            }]
        };

        // Config for Attendance Chart
        const attendanceChartConfig = {
            type: 'pie',
            data: attendanceChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false, // Ensure it resizes correctly
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(2);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        };

        // Render Attendance Chart
        const attendanceChart = new Chart(
            document.getElementById('attendanceChart'),
            attendanceChartConfig
        );
    }
});