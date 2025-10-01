<?php
require_once 'config.php';
require_once 'includes/header.php';
?>

<div style="padding: 50px; text-align: center;">
    <h1>AJAX Search Test Page</h1>
    <p>Click the search button in the header to test the AJAX search functionality.</p>
    <br>
    <button onclick="testSearch()" class="btn btn-primary">Open Search Popup (Direct Test)</button>
    
    <div style="margin-top: 30px;">
        <h3>Test Instructions:</h3>
        <ol style="text-align: left; max-width: 600px; margin: 0 auto;">
            <li>Click the search icon in the header</li>
            <li>Try typing search terms like "tour", "beach", "mountain"</li>
            <li>Test the filter buttons (All, Tours, Destinations, Blog)</li>
            <li>Check if results load properly</li>
            <li>Verify the close functionality works</li>
        </ol>
    </div>
</div>

<script>
function testSearch() {
    const searchPopup = document.querySelector('.search-popup');
    if (searchPopup) {
        searchPopup.classList.add('search-popup--visible');
        setTimeout(() => {
            const searchInput = searchPopup.querySelector('#ajaxSearchInput');
            if (searchInput) searchInput.focus();
        }, 300);
    } else {
        alert('Search popup not found. Make sure footer.php is included.');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>