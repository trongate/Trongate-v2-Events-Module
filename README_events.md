# Trongate v2 Events Scheduling Module

A complete **events** module for **Trongate v2** that demonstrates a full-featured **CRUD** (Create, Read, Update, Delete) application for scheduling events with precise date and time tracking.

This repository provides a ready-to-use example of building an event scheduling system using the Trongate PHP framework (version 2). It includes pagination, form validation, secure admin access, **native HTML5 datetime-local input handling**, proper datetime conversion patterns, and clean separation of concerns.

## Features

- ✅ Paginated event listing with selectable records per page (10, 20, 50, 100)
- ✅ Create new event records with precise date and time scheduling
- ✅ View detailed event information with formatted datetime display
- ✅ Update existing event records (with form repopulation on validation errors)
- ✅ Safe delete with confirmation page
- ✅ **Native HTML5 datetime-local picker** for combined date/time selection (zero JavaScript required)
- ✅ **Proper datetime handling** using ISO 8601 format (YYYY-MM-DDTHH:MM)
- ✅ **Bidirectional datetime conversion** between ISO 8601 and MySQL DATETIME formats
- ✅ **MySQL DATETIME column type** for database storage
- ✅ Beautiful datetime formatting for display (e.g., "December 27, 2025 at 2:30 PM")
- ✅ Form validation including datetime-local validation
- ✅ CSRF protection on all forms
- ✅ Admin security checks on all actions
- ✅ Responsive back navigation and flash messages
- ✅ Clean, well-commented code following Trongate v2 best practices

## Database Table

The `events.sql` file creates an `events` table with the following columns:
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `event_name` (VARCHAR 100)
- `event_location` (VARCHAR 100)
- `event_start` (DATETIME) - stores date and time in YYYY-MM-DD HH:MM:SS format

## Prerequisites

- Trongate v2 framework (latest version recommended)
- PHP 8.0+
- MySQL/MariaDB database
- Web server with URL rewriting enabled

Visit the official site: [trongate.io](https://trongate.io)

## Installation

1. **Install Trongate v2** (if not already done):
   - Download or clone the official framework from GitHub: [https://github.com/trongate/trongate-framework](https://github.com/trongate/trongate-framework)
   - For full documentation and guides, visit: [trongate.io/documentation](https://trongate.io/documentation)

2. **Add the module**:
   - Copy the `events` folder into your project's `modules` directory:
     ```
     modules/
       events/
         Events.php
         Events_model.php
         views/
           create.php
           manage.php
           show.php
           delete_conf.php
           not_found.php
     ```

3. **Create the database table**:
   - Import `events.sql` into your database (e.g., via phpMyAdmin or command line).

4. **Access the module**:
   - Log in to your Trongate admin panel.
   - Visit: `https://your-domain.com/events` or `https://your-domain.com/events/manage`

## URL Routes

- List events: `/events` or `/events/manage` (with pagination: `/events/manage/{page}`)
- Create event: `/events/create`
- View event: `/events/show/{id}`
- Edit event: `/events/create/{id}`
- Delete confirmation: `/events/delete_conf/{id}`
- Set records per page: `/events/set_per_page/{option_index}`

## Module Structure

```
events/
├── Events.php              # Main controller with CRUD operations
├── Events_model.php        # Data layer with datetime conversion methods
└── views/
    ├── create.php          # Create/Edit form
    ├── manage.php          # Paginated list view
    ├── show.php            # Detail view
    ├── delete_conf.php     # Delete confirmation
    └── not_found.php       # 404 error page
```

## Key Features Explained

### Native HTML5 DateTime-Local Input

This module uses the **native HTML5 datetime-local picker** via Trongate's `form_datetime_local()` helper:

```php
echo form_datetime_local('event_start', $event_start);
```

**Benefits:**
- ✅ Zero JavaScript required
- ✅ Works on all modern browsers (Chrome, Firefox, Safari, Edge)
- ✅ Native mobile keyboards and pickers
- ✅ Accessible by default
- ✅ Always submits in ISO 8601 format (YYYY-MM-DDTHH:MM)
- ✅ Browser displays in user's locale format automatically

### DateTime Conversion Pattern

The module demonstrates proper datetime conversion between HTML5 and MySQL:

**From Form to Database:**
```php
// Form submits: 2025-12-27T14:30
// Convert to MySQL: 2025-12-27 14:30:00
$data['event_start'] = str_replace('T', ' ', $event_start_iso) . ':00';
```

**From Database to Form:**
```php
// Database has: 2025-12-27 14:30:00
// Convert to ISO: 2025-12-27T14:30
$data['event_start'] = str_replace(' ', 'T', substr($mysql_datetime, 0, 16));
```

### DateTime Storage

Datetimes are stored in MySQL's `DATETIME` type, which uses `YYYY-MM-DD HH:MM:SS` format. The module handles all necessary conversions automatically.

### DateTime Display Formatting

The model includes a `prepare_for_display()` method that formats datetimes for human-readable display:

```php
// Database: 2025-12-27 14:30:00
// Display: December 27, 2025 at 2:30 PM
// Short: Dec 27, 2025 - 2:30 PM
```

### Validation Rules

The module demonstrates proper validation including:
- Required fields
- String length limits
- **DateTime-local format validation** using `valid_datetime_local` rule

```php
$this->validation->set_rules('event_start', 'event start', 'required|valid_datetime_local');
```

## Development Patterns Demonstrated

### 1. The Three-Method Form Pattern
- `create()` - Display form
- `submit()` - Process submission with datetime conversion
- `show()` - Display success/result

### 2. Create/Update Pattern with DateTime Conversion
- Single form for both creating and editing
- Automatic datetime conversion when loading from database
- Automatic datetime conversion when saving to database
- Proper segment type-casting: `segment(3, 'int')`

### 3. POST-Redirect-GET Pattern
- Prevents duplicate submissions on refresh
- Uses `set_flashdata()` for success messages
- Clean URL after form submission

### 4. Data Conversion (Model Methods)
- `convert_iso_to_mysql()` - Converts YYYY-MM-DDTHH:MM to YYYY-MM-DD HH:MM:SS
- `convert_mysql_to_iso()` - Converts YYYY-MM-DD HH:MM:SS to YYYY-MM-DDTHH:MM
- `prepare_for_display()` - Formats datetime for human-readable output
- Clear separation between database format and display format

### 5. Pagination Implementation
- Session-based per-page selection
- Proper offset calculation
- Clean pagination helper integration

## Code Examples

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

### Storing DateTime-Local Data
```php
// Get from form (ISO 8601 format)
$event_start_iso = post('event_start', true); // "2025-12-27T14:30"

// Convert to MySQL format
$data['event_start'] = $this->model->convert_iso_to_mysql($event_start_iso);
// Result: "2025-12-27 14:30:00"

// Save to database
$this->db->insert($data, 'events');
```

### Loading DateTime-Local Data for Editing
```php
// Get from database (MySQL DATETIME format)
$record = $this->db->get_where($update_id, 'events');
// $record->event_start = "2025-12-27 14:30:00"

// Convert to ISO 8601 for form
$data['event_start'] = $this->model->convert_mysql_to_iso($record->event_start);
// Result: "2025-12-27T14:30"

// Pass to view
$this->view('event_form', $data);
```

### DateTime Display Formatting
```php
// Model method formats for display
$datetime = new DateTime($data['event_start']); // "2025-12-27 14:30:00"
$data['event_start_formatted'] = $datetime->format('F j, Y \a\t g:i A');
// Result: "December 27, 2025 at 2:30 PM"
```

## Important DateTime Concepts

### ISO 8601 Format (YYYY-MM-DDTHH:MM)
- This is what HTML5 datetime-local inputs use
- Example: `2025-12-27T14:30`
- Note the "T" separator between date and time
- No seconds included (handled during conversion)

### MySQL DATETIME Format (YYYY-MM-DD HH:MM:SS)
- This is what MySQL DATETIME columns use
- Example: `2025-12-27 14:30:00`
- Note the space separator and included seconds
- Standard SQL format

### The Conversion Pattern
```php
// ISO 8601 → MySQL DATETIME
str_replace('T', ' ', $iso_datetime) . ':00'

// MySQL DATETIME → ISO 8601
str_replace(' ', 'T', substr($mysql_datetime, 0, 16))
```

## Customization

### Changing DateTime Display Format

Edit the `prepare_for_display()` method in `Events_model.php`:

```php
// Current format: "December 27, 2025 at 2:30 PM"
$data['event_start_formatted'] = $datetime->format('F j, Y \a\t g:i A');

// 24-hour format: "27/12/2025 14:30"
$data['event_start_formatted'] = $datetime->format('d/m/Y H:i');

// Short format: "Dec 27, 2:30 PM"
$data['event_start_formatted'] = $datetime->format('M j, g:i A');
```

### Adding Duration Tracking

Add an `event_end` column to track when events finish:

```sql
ALTER TABLE events ADD COLUMN event_end DATETIME AFTER event_start;
```

Then add another `form_datetime_local()` field in your form and handle the conversion the same way.

### Making Event Start Optional

Change the validation rule in `Events.php`:

```php
// From:
$this->validation->set_rules('event_start', 'event start', 'required|valid_datetime_local');

// To:
$this->validation->set_rules('event_start', 'event start', 'valid_datetime_local');
```

## Troubleshooting

**Module not showing?**
- Ensure the `events` folder is in `modules/` directory
- Verify Events.php and Events_model.php are in the events/ root (not in subdirectories)
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

## Browser Compatibility

The native HTML5 datetime-local input is supported by:
- ✅ Chrome (all versions)
- ✅ Firefox (all versions)
- ✅ Safari 14.1+
- ✅ Edge (all versions)
- ✅ Mobile browsers (iOS Safari, Android Chrome)

**Note:** Very old browsers (IE 11 and earlier) will render datetime-local inputs as text fields. Users can still type datetimes manually in YYYY-MM-DDTHH:MM format, and validation will ensure correctness.

## Security Features

- ✅ CSRF token validation on all forms
- ✅ Admin authentication checks on all methods
- ✅ SQL injection prevention via prepared statements
- ✅ XSS prevention via `out()` function in views
- ✅ DateTime format validation
- ✅ Delete confirmation to prevent accidental deletion

## Contributing

Issues, suggestions, and pull requests are welcome! Feel free to fork and improve this example module.

## License

Released under the same open-source license as the Trongate framework (MIT-style - permissive and free to use).

## Learn More

- [Trongate Framework](https://trongate.io)
- [Trongate Documentation](https://trongate.io/documentation)
- [Date and Time Handling in Trongate v2](https://trongate.io/documentation/trongate_php_framework/working-with-dates-and-times)
- [Related Module: Friends Birthday Tracker](https://github.com/trongate/Trongate-v2-Friends-Module) - Demonstrates `form_date()` usage

Happy scheduling with Trongate! 📅⏰
