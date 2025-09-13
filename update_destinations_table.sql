-- Update destinations table to add missing best_time_to_visit field
ALTER TABLE destinations ADD COLUMN best_time_to_visit VARCHAR(100) DEFAULT NULL AFTER city;

-- Update existing destinations with sample best_time_to_visit data
UPDATE destinations SET best_time_to_visit = CASE 
    WHEN slug = 'dubai-emirates' THEN 'November to March'
    WHEN slug = 'paris-france' THEN 'April to June, September to October'
    WHEN slug = 'bali-indonesia' THEN 'April to October'
    WHEN slug = 'tokyo-japan' THEN 'March to May, September to November'
    WHEN slug = 'santorini-greece' THEN 'April to October'
    ELSE 'Year Round'
END
WHERE slug IN ('dubai-emirates', 'paris-france', 'bali-indonesia', 'tokyo-japan', 'santorini-greece');
