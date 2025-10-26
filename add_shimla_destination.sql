-- Add Shimla destination data
-- Run this SQL file to add Shimla destination to your database

USE travhub_db;

-- Insert Shimla destination
INSERT INTO destinations (
    name, 
    slug, 
    description, 
    short_description, 
    country, 
    city, 
    featured_image, 
    popular, 
    status,
    meta_title,
    meta_description
) VALUES (
    'Shimla',
    'shimla',
    'Shimla, the capital of Himachal Pradesh, is one of India\'s most popular hill stations. Known as the "Queen of Hills," Shimla offers a perfect blend of natural beauty, colonial architecture, and pleasant weather year-round. The town is set amidst pine-clad mountains and offers spectacular views of the snow-capped Himalayas. 

Key Attractions:
- Mall Road: The heart of Shimla with shops, restaurants, and colonial buildings
- The Ridge: A large open space with stunning mountain views
- Christ Church: A historic neo-Gothic church built in 1857
- Jakhu Temple: Dedicated to Lord Hanuman, situated on Jakhu Hill
- Viceregal Lodge: Former summer residence of British viceroys
- Kufri: Nearby hill station perfect for skiing and adventure activities
- Green Valley: Scenic valley offering breathtaking views
- Scandal Point: Popular meeting point with panoramic views

Best Time to Visit:
- Summer (April to June): Pleasant weather, perfect for sightseeing
- Winter (December to February): Snowfall and winter sports
- Monsoon (July to September): Lush green landscapes but heavy rainfall

Activities:
- Heritage walks through colonial architecture
- Shopping for woolens and handicrafts on Mall Road
- Toy Train ride on the UNESCO World Heritage Kalka-Shimla Railway
- Trekking and hiking in nearby hills
- Ice skating at Asia\'s largest open-air rink (in winter)
- Adventure sports in Kufri

Shimla offers a perfect escape from the hustle and bustle of city life, making it an ideal destination for families, couples, and solo travelers alike.',
    
    'The Queen of Hills, Shimla offers stunning Himalayan views, colonial charm, and pleasant weather. Perfect destination for families and couples seeking mountain getaway.',
    
    'India',
    'Shimla',
    'assets/images/destinations/shimla.jpg',
    1,
    'active',
    'Shimla Tourism - Best Hill Station Tours & Packages | TravHub',
    'Explore Shimla, the Queen of Hills. Book best Shimla tour packages with TravHub. Experience colonial architecture, Mall Road shopping, and stunning Himalayan views.'
);

-- Get the inserted destination ID
SET @shimla_id = LAST_INSERT_ID();

-- Insert some sample tours for Shimla (optional)
INSERT INTO tours (
    title,
    slug,
    destination_id,
    description,
    short_description,
    price,
    discount_price,
    duration_days,
    duration_nights,
    max_people,
    min_people,
    featured_image,
    inclusions,
    exclusions,
    difficulty_level,
    tour_type,
    featured,
    popular,
    status,
    meta_title,
    meta_description
) VALUES 
(
    'Shimla Manali Honeymoon Package',
    'shimla-manali-honeymoon-package',
    @shimla_id,
    'A perfect romantic getaway combining the charm of Shimla with the adventure of Manali. This honeymoon package includes visits to Mall Road, Kufri, Solang Valley, and Rohtang Pass. Enjoy comfortable accommodations, candlelight dinners, and create memories that last a lifetime.',
    'Romantic 6-day honeymoon package covering Shimla and Manali with luxury stays and special arrangements for couples.',
    25000.00,
    22500.00,
    6,
    5,
    2,
    2,
    'assets/images/tours/shimla-manali.jpg',
    '- 5 Nights accommodation in 4-star hotels
- Daily breakfast and dinner
- Private cab for all transfers and sightseeing
- Candlelight dinner
- Flower bed decoration
- Welcome drink on arrival
- All applicable taxes',
    '- Lunch during the tour
- Entry fees to monuments and attractions
- Adventure activity charges
- Personal expenses
- Travel insurance
- Anything not mentioned in inclusions',
    'easy',
    'mountain',
    1,
    1,
    'active',
    'Shimla Manali Honeymoon Package - Best Price | TravHub',
    'Book romantic Shimla Manali honeymoon package. 6 days of luxury stays, sightseeing, and special couple arrangements. Best price guaranteed.'
),
(
    'Shimla Kufri Adventure Tour',
    'shimla-kufri-adventure-tour',
    @shimla_id,
    'An exciting 4-day adventure tour perfect for families and groups. Explore the colonial charm of Shimla and enjoy thrilling activities in Kufri including horse riding, yak riding, and skiing (in winter). Visit major attractions like Mall Road, Jakhu Temple, and Christ Church.',
    'Thrilling 4-day adventure tour covering Shimla and Kufri with exciting outdoor activities.',
    12000.00,
    10500.00,
    4,
    3,
    10,
    2,
    'assets/images/tours/shimla-kufri.jpg',
    '- 3 Nights accommodation
- Daily breakfast
- Cab for sightseeing
- Guide services
- All transfers
- All taxes',
    '- Meals not mentioned
- Entry fees
- Adventure activity charges
- Personal expenses
- Travel insurance',
    'moderate',
    'adventure',
    0,
    1,
    'active',
    'Shimla Kufri Adventure Tour Package | TravHub',
    'Experience adventure in Shimla and Kufri. 4 days of exciting activities, sightseeing, and mountain fun. Perfect for families and groups.'
),
(
    'Shimla Heritage Walk & Culture Tour',
    'shimla-heritage-culture-tour',
    @shimla_id,
    'Discover the rich colonial heritage and cultural charm of Shimla. This 3-day tour focuses on historical landmarks, architecture, and local culture. Visit Viceregal Lodge, Christ Church, Gaiety Theatre, and explore the heritage buildings on Mall Road.',
    'Cultural 3-day tour exploring Shimla\'s colonial heritage and historical landmarks.',
    8500.00,
    7500.00,
    3,
    2,
    15,
    1,
    'assets/images/tours/shimla-heritage.jpg',
    '- 2 Nights hotel accommodation
- Daily breakfast
- Heritage walk with guide
- Museum entry tickets
- Transportation
- All taxes',
    '- Lunch and dinner
- Shopping expenses
- Camera fees
- Personal expenses
- Travel insurance',
    'easy',
    'cultural',
    0,
    0,
    'active',
    'Shimla Heritage & Culture Tour | TravHub',
    'Explore Shimla\'s colonial heritage. 3-day cultural tour covering historical landmarks, architecture, and local traditions.'
);

-- Update: Mark Shimla as popular destination
UPDATE destinations SET popular = 1 WHERE slug = 'shimla';

-- Show confirmation
SELECT 'Shimla destination and sample tours added successfully!' as message;
SELECT * FROM destinations WHERE slug = 'shimla';
