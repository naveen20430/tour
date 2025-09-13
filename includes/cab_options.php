<?php
/**
 * Cab Options Helper Functions
 * Manages cab types, pricing, and related functionality
 */

class CabOptions {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get all available cab types
     */
    public function getAllCabTypes() {
        return $this->db->fetchAll(
            "SELECT * FROM cab_types WHERE status = 'active' ORDER BY base_price ASC"
        );
    }
    
    /**
     * Get cab type by name
     */
    public function getCabTypeByName($name) {
        return $this->db->fetch(
            "SELECT * FROM cab_types WHERE name = ? AND status = 'active'", 
            [$name]
        );
    }
    
    /**
     * Calculate cab price based on tour duration and type
     */
    public function calculateCabPrice($cab_type_name, $duration_days = 1, $extra_km = 0) {
        $cab_type = $this->getCabTypeByName($cab_type_name);
        
        if (!$cab_type) {
            return 0;
        }
        
        // Base price covers the tour duration
        $base_cost = $cab_type['base_price'] * $duration_days;
        
        // Additional cost for extra kilometers
        $extra_cost = $extra_km * $cab_type['price_per_km'];
        
        return $base_cost + $extra_cost;
    }
    
    /**
     * Get cab options for dropdown display
     */
    public function getCabOptionsForDropdown() {
        $cab_types = $this->getAllCabTypes();
        $options = [];
        
        foreach ($cab_types as $cab) {
            $features = json_decode($cab['features'], true) ?: [];
            $feature_text = !empty($features) ? ' (' . implode(', ', array_slice($features, 0, 2)) . ')' : '';
            
            $options[] = [
                'value' => $cab['name'],
                'text' => $cab['display_name'] . ' - ₹' . number_format($cab['base_price'], 0) . '/day' . $feature_text,
                'price' => $cab['base_price'],
                'max_passengers' => $cab['max_passengers'],
                'description' => $cab['description']
            ];
        }
        
        return $options;
    }
    
    /**
     * Check if selected cab can accommodate the number of people
     */
    public function canAccommodate($cab_type_name, $number_of_people) {
        $cab_type = $this->getCabTypeByName($cab_type_name);
        
        if (!$cab_type) {
            return false;
        }
        
        return $number_of_people <= $cab_type['max_passengers'];
    }
    
    /**
     * Get recommended cab types for given number of people
     */
    public function getRecommendedCabs($number_of_people) {
        $all_cabs = $this->getAllCabTypes();
        $recommended = [];
        
        foreach ($all_cabs as $cab) {
            if ($cab['max_passengers'] >= $number_of_people) {
                $recommended[] = $cab;
            }
        }
        
        return $recommended;
    }
}

/**
 * Static function to get cab display name
 */
function getCabDisplayName($cab_type) {
    $cab_names = [
        'sedan' => 'Sedan',
        'xuv_tavera' => 'Xylo / XUV / TAVERA', 
        'innova' => 'Innova'
    ];
    
    return $cab_names[$cab_type] ?? ucfirst($cab_type);
}

/**
 * Get default cab pricing (fallback if database is not available)
 */
function getDefaultCabPricing() {
    return [
        'sedan' => [
            'name' => 'sedan',
            'display_name' => 'Sedan',
            'base_price' => 2500.00,
            'max_passengers' => 4,
            'description' => 'Comfortable sedan car suitable for small groups'
        ],
        'xuv_tavera' => [
            'name' => 'xuv_tavera',
            'display_name' => 'Xylo / XUV / TAVERA',
            'base_price' => 3500.00,
            'max_passengers' => 7,
            'description' => 'SUV vehicles perfect for medium groups'
        ],
        'innova' => [
            'name' => 'innova',
            'display_name' => 'Innova',
            'base_price' => 4500.00,
            'max_passengers' => 7,
            'description' => 'Premium Toyota Innova for comfortable group travel'
        ]
    ];
}
?>
