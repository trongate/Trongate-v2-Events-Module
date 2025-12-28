# Events Scheduling Module - Quick Installation Guide

## What You're Getting

A complete Trongate v2 CRUD module for scheduling events with:
- Native HTML5 datetime-local picker (zero JavaScript!)
- Bidirectional datetime conversion (ISO 8601 ↔ MySQL DATETIME)
- Full create, read, update, delete operations
- Pagination with configurable records per page
- Beautiful datetime formatting
- Clean, well-documented code

## Installation Steps

### 1. Import the Database Table
```sql
-- Run events.sql in your MySQL database
-- This creates the 'events' table with DATETIME column type
```

### 2. Copy the Module
```bash
# Copy the 'events' folder into your Trongate modules directory
your-project/
  modules/
    events/          ← Copy this entire folder here
      Events.php
      Events_model.php
      views/
```

### 3. Access the Module
```
https://your-domain.com/events
```

## File Structure Delivered

```
events/
├── Events.php              # Controller with full CRUD operations
├── Events_model.php        # Model with datetime conversion methods
└── views/
    ├── create.php          # Create/Edit form
    ├── manage.php          # List view with pagination
    ├── show.php            # Detail view
    ├── delete_conf.php     # Delete confirmation
    └── not_found.php       # 404 page

events.sql                   # Database table schema
README.md                    # Full documentation
```

## Database Schema

```sql
CREATE TABLE `events` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `event_name` VARCHAR(100),
  `event_location` VARCHAR(100),
  `event_start` DATETIME,    ← Stores date/time in YYYY-MM-DD HH:MM:SS
  PRIMARY KEY (`id`)
);
```

## Key Features

✅ **Native HTML5 DateTime-Local Input** - Uses `form_datetime_local()` helper
✅ **ISO 8601 Format** - Form submits as YYYY-MM-DDTHH:MM
✅ **MySQL DATETIME Type** - Database stores as YYYY-MM-DD HH:MM:SS
✅ **Automatic Conversion** - Model handles bidirectional conversion
✅ **Beautiful Display** - Formats as "December 27, 2025 at 2:30 PM"
✅ **Full Validation** - Including datetime-local validation
✅ **Pagination** - 10, 20, 50, or 100 records per page
✅ **Security** - CSRF protection, admin authentication
✅ **Form Repopulation** - Shows entered data on validation errors

## URL Routes

- `/events` or `/events/manage` - List all events
- `/events/create` - Create new event
- `/events/show/{id}` - View event details
- `/events/create/{id}` - Edit existing event
- `/events/delete_conf/{id}` - Delete confirmation

## Code Highlights

### The DateTime-Local Input Field
```php
echo form_datetime_local('event_start', $event_start);
// Renders: <input type="datetime-local" name="event_start">
// Always submits in YYYY-MM-DDTHH:MM format
```

### DateTime-Local Validation
```php
$this->validation->set_rules('event_start', 'event start', 'required|valid_datetime_local');
// Validates ISO 8601 format (YYYY-MM-DDTHH:MM)
```

### DateTime Conversion (Form → Database)
```php
// Form submits: "2025-12-27T14:30"
$event_start_iso = post('event_start', true);

// Convert to MySQL format: "2025-12-27 14:30:00"
$data['event_start'] = $this->model->convert_iso_to_mysql($event_start_iso);

// Save to database
$this->db->insert($data, 'events');
```

### DateTime Conversion (Database → Form)
```php
// Database has: "2025-12-27 14:30:00"
$record = $this->db->get_where($update_id, 'events');

// Convert to ISO format: "2025-12-27T14:30"
$data['event_start'] = $this->model->convert_mysql_to_iso($record->event_start);

// Pass to form
$this->view('event_form', $data);
```

### DateTime Display Formatting
```php
// Model method formats for display
$datetime = new DateTime($data['event_start']);
$data['event_start_formatted'] = $datetime->format('F j, Y \a\t g:i A');
// Result: "December 27, 2025 at 2:30 PM"
```

## The DateTime Conversion Pattern

This is the key pattern you need to understand:

**ISO 8601 (from HTML5 form):** `2025-12-27T14:30`
- Format: YYYY-MM-DDTHH:MM
- Note the "T" separator
- No seconds

**MySQL DATETIME (in database):** `2025-12-27 14:30:00`
- Format: YYYY-MM-DD HH:MM:SS
- Space separator
- Includes seconds

**Conversion Methods (in Model):**
```php
// ISO → MySQL
public function convert_iso_to_mysql(string $iso): string {
    return str_replace('T', ' ', $iso) . ':00';
}

// MySQL → ISO
public function convert_mysql_to_iso(string $mysql): string {
    return str_replace(' ', 'T', substr($mysql, 0, 16));
}
```

## Important Module Structure Note

**Trongate v2 eliminates the `controllers/` and `models/` subdirectories!**

✅ **Correct structure:**
```
events/
  Events.php           ← Controller in module root
  Events_model.php     ← Model in module root
  views/
```

❌ **Old v1 structure (DO NOT USE):**
```
events/
  controllers/
    Events.php
  models/
    Events_model.php
  views/
```

## Troubleshooting

**Module not showing?**
- Ensure the `events` folder is in `modules/` directory
- Verify Events.php and Events_model.php are in events/ root (NOT in subdirectories!)
- Check folder permissions (755 for directories, 644 for files)
- Verify you're logged into the admin panel

**DateTime picker not appearing?**
- HTML5 datetime-local inputs work in all modern browsers
- Very old browsers fall back to text input
- Users can type YYYY-MM-DDTHH:MM format manually

**Validation errors?**
- Check that all required fields are filled
- Event start must be in YYYY-MM-DDTHH:MM format
- Ensure datetime conversion methods are being called

**Database datetime errors?**
- Verify event_start column is DATETIME type
- Confirm conversion from ISO to MySQL format is happening
- Check that seconds (:00) are being appended

## Need Help?

- Full documentation in README.md
- Visit [trongate.io/documentation](https://trongate.io/documentation)
- All code follows Trongate v2 best practices
- See also: [Friends Birthday Tracker](https://github.com/trongate/Trongate-v2-Friends-Module) module

Enjoy scheduling events with Trongate! 📅⏰
