# Cab Pricing System Setup

I've created a comprehensive cab pricing system for your tour booking platform. Here's what has been implemented:

## 🚗 Features Added

### 1. **Four Cab Types** (as per your pricing table)
- **Sedan** - Up to 4 passengers
- **Ertiga** - Up to 7 passengers  
- **Innova** - Up to 7 passengers
- **Tempo Traveller** - Up to 12 passengers

### 2. **Tour-Specific Pricing**
Your exact pricing table has been implemented:
- Day tour of Shimla: Sedan ₹2,730 | Ertiga ₹3,255 | Innova ₹3,780 | Tempo ₹4,830
- Full day tour Kufri, Fagu & Naldehra: Sedan ₹3,045 | Ertiga ₹3,780 | Innova ₹4,830 | Tempo ₹5,880
- And all other tours from your table...

### 3. **Admin Management Interface**
Complete backend interface to manage cab pricing at `/admin/cab_pricing.php`

### 4. **Smart Booking Form**
- Dynamic pricing based on selected tour
- Automatic cab filtering based on group size
- Real-time price calculation
- Live price summary

## 📁 Files Created/Updated

### New Files:
- `update_cab_pricing.php` - Database migration script
- `admin/cab_pricing.php` - Admin interface for pricing management  
- `booking_with_cabs.php` - Enhanced booking form with cab options
- `CAB_PRICING_README.md` - This documentation

### Updated Files:
- `includes/cab_options.php` - Enhanced with new cab types and tour-specific pricing functions

## 🛠️ Setup Instructions

### Step 1: Run Database Migration
```bash
# Navigate to your tour directory
cd /path/to/your/tour/directory

# Run the migration script via browser or CLI
# Browser: Visit http://yoursite.com/update_cab_pricing.php
# OR CLI: php update_cab_pricing.php
```

This will:
- Create `cab_types` table with your four cab types
- Create `tour_cab_pricing` table with exact pricing from your table
- Add cab columns to existing `bookings` table

### Step 2: Access Admin Panel
Visit `/admin/cab_pricing.php` to:
- Update base cab pricing
- Manage tour-specific pricing
- Add new tour pricing
- Delete obsolete pricing

### Step 3: Update Booking Links
Replace your existing booking form links with:
- `booking_with_cabs.php` - For the enhanced booking experience

## 💡 How It Works

### Dynamic Pricing Logic:
1. **Tour Selection**: When user selects a tour, JavaScript fetches tour-specific pricing
2. **Cab Display**: Shows all four cab types with correct pricing for that tour
3. **Capacity Filter**: Automatically disables cabs that can't accommodate group size
4. **Price Calculation**: Shows tour cost + cab charges = total amount

### Fallback System:
- If tour-specific pricing not found → Uses base cab pricing
- If database unavailable → Uses hardcoded default pricing

## 🎯 Usage Examples

### For Admin:
```php
// Get tour-specific pricing
$pricing = getTourSpecificPricing("Day tour of Shimla", $db);

// Get cab price for specific tour and type
$price = getCabPriceForTour("Day tour of Shimla", "sedan", $db);
```

### For Frontend:
```javascript
// Pricing automatically updates when tour changes
// Cabs automatically filter based on group size
// Total calculation happens in real-time
```

## 📊 Database Tables

### `cab_types`
```sql
- id, name, display_name, base_price, price_per_km
- max_passengers, description, features, status
```

### `tour_cab_pricing`
```sql
- id, tour_name, sedan_price, ertiga_price
- innova_price, tempo_traveller_price
```

### `bookings` (enhanced)
```sql
- existing columns + cab_type, cab_price, total_with_cab
```

## 🔧 Customization

### Adding New Tours:
1. Use admin interface at `/admin/cab_pricing.php`
2. Click "Add Tour Pricing"  
3. Enter tour name and pricing for each cab type

### Updating Base Pricing:
1. Access admin interface
2. Update "Base Cab Types & Default Pricing" section
3. Click "Update Base Pricing"

### Modifying Cab Types:
Edit the `cab_types` table directly or modify `update_cab_pricing.php` before running.

## 🚀 Next Steps

1. **Test the system**: Visit `booking_with_cabs.php` to test booking flow
2. **Run migration**: Execute `update_cab_pricing.php` once
3. **Configure admin**: Access `/admin/cab_pricing.php` to fine-tune pricing
4. **Update links**: Replace existing booking links with new enhanced form
5. **Delete migration**: Remove `update_cab_pricing.php` after successful setup

## 🎨 Features Highlights

- ✅ **Responsive Design** - Works on mobile and desktop
- ✅ **Real-time Updates** - Pricing updates as selections change  
- ✅ **Smart Filtering** - Only shows suitable cabs for group size
- ✅ **Admin Friendly** - Easy backend management
- ✅ **Fallback System** - Robust error handling
- ✅ **Your Exact Pricing** - Implements your provided pricing table

The system is now ready for use! All four cab types with your exact per-day pricing are configured and can be managed easily from the backend.