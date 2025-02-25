// Initialize Bootstrap popover for the kural-container
$(document).ready(function () {
    $('#kural-container').popover({
        trigger: 'hover',
        placement: 'top',
    });
});

// Fetch and update the Thirukkural dynamically
function fetchNewKural() {
    fetch('/api/app/addons/thirukkural')
        .then(response => response.json())
        .then(data => {
            // Update the lines inside the kural-container
            $('#line1').text(data.Line1);
            $('#line2').text(data.Line2);

            // Update popover content dynamically
            $('#kural-container').attr('data-bs-content', `
                <div class="thil-explanation">
                    <div class="text-center mb-2">
                        <strong class="text-primary">திருக்குறள்</strong>
                    </div>
                    <div class="mb-2">
                        <strong>குறள் எண்:</strong> ${data.Number}
                    </div>
                    <div class="mb-2">
                        <strong>அதிகாரம்:</strong> ${data.adikaram_name}
                    </div>
                    <div class="mb-2">
                        <strong>குறள்:</strong><br> ${data.Line1}<br> ${data.Line2}
                    </div>
                    <div class="mb-2">
                        <strong>குறள் விளக்கம்:</strong><br> ${data.mk}.
                    </div>
                    <div class="mb-2">
                        <strong>Translation:</strong><br> ${data.Translation}.
                    </div>
                    <div>
                        <strong>Explanation:</strong><br> ${data.explanation}.
                    </div>
                </div>
            `);

            // Reinitialize popover after updating content
            $('#kural-container').popover('dispose').popover({
                trigger: 'hover',
                placement: 'bottom',
            });
        })
        .catch(error => console.error('Error fetching new Kural:', error));
}

// Fetch a new Thirukkural immediately and every minute
fetchNewKural();
// Detect hover over the kural-container
let isHovered = false;

const kuralContainer = document.getElementById('kural-container');
kuralContainer.addEventListener('mouseenter', () => {
    isHovered = true;
});

kuralContainer.addEventListener('mouseleave', () => {
    isHovered = false;
});

// Fetch new Thirukkural only when not hovered
setInterval(() => {
    if (!isHovered) {
        fetchNewKural();
    }
}, 60000); // 60000 ms = 1 minute