<?php
// Simple debug page to test AJAX search functionality
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX Search Debug</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/ajax-search.css">
    <style>
        body { padding: 20px; }
        .debug-info { 
            background: #f8f9fa; 
            border: 1px solid #dee2e6; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 10px 0; 
        }
        .test-btn { margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>AJAX Search Debug Page</h1>
        
        <div class="debug-info">
            <h3>1. Test Search API Direct</h3>
            <button onclick="testSearchAPI()" class="btn btn-primary test-btn">Test API with 'tour'</button>
            <button onclick="testSearchAPI('beach')" class="btn btn-secondary test-btn">Test API with 'beach'</button>
            <div id="apiResult" style="margin-top:10px;"></div>
        </div>
        
        <div class="debug-info">
            <h3>2. Test Search Popup Elements</h3>
            <button onclick="testPopupElements()" class="btn btn-info test-btn">Check Popup Elements</button>
            <button onclick="openSearchPopup()" class="btn btn-warning test-btn">Open Search Popup</button>
            <div id="elementCheck" style="margin-top:10px;"></div>
        </div>
        
        <div class="debug-info">
            <h3>3. Manual Search Input</h3>
            <input type="text" id="manualSearchInput" placeholder="Type search term" class="form-control">
            <button onclick="performManualSearch()" class="btn btn-success test-btn">Search</button>
            <div id="manualResult" style="margin-top:10px;"></div>
        </div>
    </div>

    <!-- Search Popup (copied from footer) -->
    <div class="search-popup">
        <div class="search-popup__overlay"></div>
        <div class="search-popup__content">
            <div class="search-popup__header">
                <h3 class="search-popup__title">Search Tours, Destinations & Blogs</h3>
                <button class="search-popup__close" aria-label="Close search">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form role="search" method="get" class="search-popup__form" id="ajaxSearchForm">
                <div class="search-input-wrapper">
                    <input type="text" id="ajaxSearchInput" placeholder="Search tours, destinations, blogs..." autocomplete="off" />
                    <button type="submit" aria-label="search submit" class="search-submit-btn">
                        <span>
                            <i class="flaticon-search"></i>
                            <i class="fas fa-search" style="display: none;"></i>
                        </span>
                    </button>
                    <div class="search-loading" id="searchLoading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </div>
                
                <div class="search-filters">
                    <button type="button" class="search-filter-btn active" data-type="all">
                        <i class="fas fa-search"></i> All
                    </button>
                    <button type="button" class="search-filter-btn" data-type="tours">
                        <i class="fas fa-map-marked-alt"></i> Tours
                    </button>
                    <button type="button" class="search-filter-btn" data-type="destinations">
                        <i class="fas fa-globe"></i> Destinations
                    </button>
                    <button type="button" class="search-filter-btn" data-type="blog">
                        <i class="fas fa-blog"></i> Blog
                    </button>
                </div>
            </form>
            
            <div class="search-results" id="searchResults">
                <div class="search-welcome">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h4>Start typing to search</h4>
                    <p>Find tours, destinations, and blog posts instantly</p>
                </div>
            </div>
        </div>
    </div>

<script>
// Test functions
function testSearchAPI(query = 'tour') {
    const resultDiv = document.getElementById('apiResult');
    resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing API...';
    
    fetch(`ajax/search.php?q=${encodeURIComponent(query)}&type=all`)
        .then(response => response.json())
        .then(data => {
            resultDiv.innerHTML = '<pre style="background:#f8f9fa;padding:10px;border-radius:3px;">' + 
                                  JSON.stringify(data, null, 2) + '</pre>';
        })
        .catch(error => {
            resultDiv.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
        });
}

function testPopupElements() {
    const resultDiv = document.getElementById('elementCheck');
    const popup = document.querySelector('.search-popup');
    const input = document.querySelector('#ajaxSearchInput');
    const filters = document.querySelectorAll('.search-filter-btn');
    const loading = document.querySelector('#searchLoading');
    const results = document.querySelector('#searchResults');
    
    let html = '<ul>';
    html += '<li>Search Popup: ' + (popup ? '✅ Found' : '❌ Not found') + '</li>';
    html += '<li>Search Input: ' + (input ? '✅ Found' : '❌ Not found') + '</li>';
    html += '<li>Filter Buttons: ' + (filters.length + ' found') + '</li>';
    html += '<li>Loading Spinner: ' + (loading ? '✅ Found' : '❌ Not found') + '</li>';
    html += '<li>Results Container: ' + (results ? '✅ Found' : '❌ Not found') + '</li>';
    html += '</ul>';
    
    resultDiv.innerHTML = html;
}

function openSearchPopup() {
    const popup = document.querySelector('.search-popup');
    if (popup) {
        popup.classList.add('search-popup--visible');
        setTimeout(() => {
            const input = document.querySelector('#ajaxSearchInput');
            if (input) input.focus();
        }, 300);
    }
}

function performManualSearch() {
    const query = document.getElementById('manualSearchInput').value.trim();
    const resultDiv = document.getElementById('manualResult');
    
    if (query.length < 2) {
        resultDiv.innerHTML = '<div class="alert alert-warning">Please enter at least 2 characters</div>';
        return;
    }
    
    resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
    
    fetch(`ajax/search.php?q=${encodeURIComponent(query)}&type=all`)
        .then(response => response.json())
        .then(data => {
            console.log('Manual search result:', data);
            let html = '<div class="alert alert-info">Total Results: ' + (data.total || 0) + '</div>';
            
            if (data.results) {
                if (data.results.tours && data.results.tours.length > 0) {
                    html += '<h5>Tours (' + data.results.tours.length + '):</h5><ul>';
                    data.results.tours.forEach(tour => {
                        html += '<li>' + tour.title + '</li>';
                    });
                    html += '</ul>';
                }
                
                if (data.results.destinations && data.results.destinations.length > 0) {
                    html += '<h5>Destinations (' + data.results.destinations.length + '):</h5><ul>';
                    data.results.destinations.forEach(dest => {
                        html += '<li>' + dest.name + '</li>';
                    });
                    html += '</ul>';
                }
            }
            
            resultDiv.innerHTML = html;
        })
        .catch(error => {
            resultDiv.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
        });
}
</script>

<!-- Load the AJAX search JavaScript -->
<script src="assets/js/design-enhancements.js"></script>

</body>
</html>