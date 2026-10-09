<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy - Instant Drug Search</title>
    <!-- Clean Bootstrap 5 Styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 650px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="mb-3 text-primary">Live Pharmacy Search</h3>
            <p class="text-muted">Type any medication name to search the inventory instantly.</p>

            <!-- Search Input Field -->
            <div class="mb-3">
                <input 
                    type="text" 
                    id="search-box" 
                    class="form-control form-control-lg" 
                    placeholder="Type a drug name (e.g. Paracetamol)..." 
                    autocomplete="off"
                >
            </div>

            <!-- Loading Spinner (Hidden by default) -->
            <div id="loading-spinner" class="text-center d-none my-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <span class="text-muted ms-2">Searching database...</span>
            </div>

            <!-- Results will be placed inside this list -->
            <ul id="results-list" class="list-group list-group-flush">
                <li class="list-group-item text-muted text-center py-3">Start typing above to see matching drugs.</li>
            </ul>
        </div>
    </div>
</div>

<!-- Simple Live Search Script -->
<script>
const searchBox = document.getElementById('search-box');
const resultsList = document.getElementById('results-list');
const spinner = document.getElementById('loading-spinner');

// Listen for every single keystroke in the input box
searchBox.addEventListener('input', function () {
    const searchText = searchBox.value.trim();

    // If the input box was cleared out, reset the list
    if (searchText.length === 0) {
        resultsList.innerHTML = '<li class="list-group-item text-muted text-center py-3">Start typing above to see matching drugs.</li>';
        spinner.classList.add('d-none');
        return;
    }

    // Show the small loading spinner
    spinner.classList.remove('d-none');

    // 1. Send the background request to our PHP API
    fetch('search_api.php?query=' + encodeURIComponent(searchText))
        .then(response => response.json()) // 2. Convert the incoming response into data
        .then(drugs => {
            // Hide the spinner once data arrives
            spinner.classList.add('d-none');

            // 3. Clear out whatever was in the results list before
            resultsList.innerHTML = '';

            // If no drugs matched what was typed
            if (drugs.length === 0) {
                resultsList.innerHTML = '<li class="list-group-item text-danger text-center py-3">No matching medications found.</li>';
                return;
            }

            // 4. Loop through each drug found and draw a neat row for it
            drugs.forEach(drug => {
                const item = document.createElement('li');
                item.className = 'list-group-item d-flex justify-content-between align-items-center py-3';
                
                // Safe text handling to keep our app clean
                item.innerHTML = `
                    <div>
                        <strong class="text-dark">${escapeHtml(drug.name)}</strong>
                        <br>
                        <small class="text-muted">Brand: ${escapeHtml(drug.brand || 'Generic')}</small>
                    </div>
                    <span class="badge bg-success rounded-pill px-3 py-2">$${parseFloat(drug.price).toFixed(2)}</span>
                `;
                
                resultsList.appendChild(item);
            });
        })
        .catch(error => {
            spinner.classList.add('d-none');
            resultsList.innerHTML = '<li class="list-group-item text-danger text-center">Error loading results. Check server connection.</li>';
        });
});

// Helper function to keep our text safe on screen
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

</body>
</html>