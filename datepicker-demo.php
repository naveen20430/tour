<?php
require_once 'config/config.php';

// Set page variables
$page_title = 'Attractive Datepicker Demo - ' . getSetting('site_name');
$current_page = 'demo';

// Include header
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-5">
                <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700;">🗓️ Attractive Datepicker Demo</h1>
                <p class="lead text-muted">Beautiful, modern datepickers that match your website theme</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Basic Datepicker -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100" style="border-radius: 20px; padding: 25px;">
                <h5 class="card-title mb-3">📅 Basic Datepicker</h5>
                <p class="text-muted small mb-3">Simple date selection with modern styling</p>
                <div class="form-group">
                    <label class="form-label">Select Date</label>
                    <input type="date" class="form-control datepicker-input" placeholder="Choose a date" data-today-min>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Features:</strong> Calendar icon, smooth animations, minimum date validation
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Date Range Picker -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100" style="border-radius: 20px; padding: 25px;">
                <h5 class="card-title mb-3">📊 Date Range Picker</h5>
                <p class="text-muted small mb-3">Perfect for booking check-in and check-out dates</p>
                <div class="form-group">
                    <label class="form-label">Select Date Range</label>
                    <input type="text" class="form-control check-in-out-date" placeholder="Select check-in and check-out dates">
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Features:</strong> Range selection, dual months on desktop, responsive design
                    </small>
                </div>
            </div>
        </div>
        
        <!-- DateTime Picker -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100" style="border-radius: 20px; padding: 25px;">
                <h5 class="card-title mb-3">⏰ DateTime Picker</h5>
                <p class="text-muted small mb-3">Date and time selection for precise scheduling</p>
                <div class="form-group">
                    <label class="form-label">Select Date & Time</label>
                    <input type="text" class="form-control datetime-picker" placeholder="Choose date and time" data-today-min>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Features:</strong> 12-hour format, time picker, AM/PM selector
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Tour Date Picker -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100" style="border-radius: 20px; padding: 25px;">
                <h5 class="card-title mb-3">🎯 Tour Date Picker</h5>
                <p class="text-muted small mb-3">Special datepicker with availability indicators</p>
                <div class="form-group">
                    <label class="form-label">Select Tour Date</label>
                    <input type="text" class="form-control tour-date-picker" placeholder="Choose tour date">
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Features:</strong> Availability dots, disabled fully booked dates, future dates only
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Multiple Dates Picker -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100" style="border-radius: 20px; padding: 25px;">
                <h5 class="card-title mb-3">📆 Multiple Dates</h5>
                <p class="text-muted small mb-3">Select multiple dates for events or availability</p>
                <div class="form-group">
                    <label class="form-label">Select Multiple Dates</label>
                    <input type="text" class="form-control multi-date" placeholder="Choose multiple dates" data-today-min>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Features:</strong> Multiple selection, comma-separated display, clear selection
                    </small>
                </div>
            </div>
        </div>
        
        <!-- No Weekends Picker -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100" style="border-radius: 20px; padding: 25px;">
                <h5 class="card-title mb-3">🏢 Business Days Only</h5>
                <p class="text-muted small mb-3">Date picker that excludes weekends</p>
                <div class="form-group">
                    <label class="form-label">Select Business Day</label>
                    <input type="text" class="form-control no-weekends" placeholder="Weekdays only" data-today-min>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>Features:</strong> Weekends disabled, business days only, custom validation
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Usage Examples -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card" style="border-radius: 20px; padding: 30px;">
                <h3 class="mb-4">🔧 How to Use</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>HTML Examples:</h5>
                        <pre style="background: #f8f9fa; padding: 20px; border-radius: 10px; overflow-x: auto;"><code>&lt;!-- Basic Date Picker --&gt;
&lt;input type="date" class="form-control datepicker-input"&gt;

&lt;!-- Date Range Picker --&gt;
&lt;input type="text" class="form-control date-range"&gt;

&lt;!-- DateTime Picker --&gt;
&lt;input type="text" class="form-control datetime-picker"&gt;

&lt;!-- Multiple Dates --&gt;
&lt;input type="text" class="form-control multi-date"&gt;

&lt;!-- With Custom Attributes --&gt;
&lt;input type="text" class="form-control datepicker-input" 
       data-today-min 
       data-max-date="2024-12-31"&gt;</code></pre>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Available Attributes:</h5>
                        <ul class="list-unstyled">
                            <li><code>data-today-min</code> - Minimum date is today</li>
                            <li><code>data-min-date="2024-01-01"</code> - Custom min date</li>
                            <li><code>data-max-date="2024-12-31"</code> - Custom max date</li>
                            <li><code>data-range</code> - Enable range selection</li>
                            <li><code>data-time</code> - Enable time picker</li>
                            <li><code>data-multiple</code> - Multiple date selection</li>
                            <li><code>data-no-weekends</code> - Disable weekends</li>
                            <li><code>data-disable-dates="2024-12-25,2024-01-01"</code> - Disable specific dates</li>
                        </ul>
                        
                        <h5 class="mt-4">CSS Classes:</h5>
                        <ul class="list-unstyled">
                            <li><code>.datepicker-input</code> - Basic datepicker</li>
                            <li><code>.date-range</code> - Range picker</li>
                            <li><code>.datetime-picker</code> - Date and time</li>
                            <li><code>.multi-date</code> - Multiple dates</li>
                            <li><code>.no-weekends</code> - Weekdays only</li>
                            <li><code>.tour-date-picker</code> - Tour booking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript API -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card" style="border-radius: 20px; padding: 30px;">
                <h3 class="mb-4">⚡ JavaScript API</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Programmatic Control:</h5>
                        <pre style="background: #f8f9fa; padding: 20px; border-radius: 10px; overflow-x: auto;"><code>// Set date programmatically
DatepickerUtils.setDate('#my-datepicker', '2024-12-25');

// Get selected date
const selectedDates = DatepickerUtils.getDate('#my-datepicker');

// Clear date
DatepickerUtils.clearDate('#my-datepicker');

// Destroy datepicker
DatepickerUtils.destroy('#my-datepicker');</code></pre>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Demo Buttons:</h5>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="DatepickerUtils.setDate('.datepicker-input', '2024-12-25')">
                                Set Christmas Date
                            </button>
                            <button class="btn btn-success" onclick="alert('Selected: ' + DatepickerUtils.getDate('.datepicker-input'))">
                                Get Selected Date
                            </button>
                            <button class="btn btn-warning" onclick="DatepickerUtils.clearDate('.datepicker-input')">
                                Clear Date
                            </button>
                            <button class="btn btn-info" onclick="window.reinitializeDatepickers()">
                                Reinitialize All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Demo page specific styling */
pre code {
    font-size: 0.85rem;
    color: #495057;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

code {
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.9em;
    color: #e83e8c;
}
</style>

<?php include 'includes/footer.php'; ?>