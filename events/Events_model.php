<?php
/**
 * Events Model - Handles data operations for event records
 * 
 * Demonstrates proper datetime-local handling with conversion between
 * ISO 8601 format (YYYY-MM-DDTHH:MM) and MySQL DATETIME format (YYYY-MM-DD HH:MM:SS).
 */
class Events_model extends Model {
    
    /**
     * Fetch paginated event records from database
     * 
     * Retrieves events with proper limit and offset for pagination.
     * This is the primary method for listing events in manage view.
     * 
     * @param int $limit Maximum number of records to return
     * @param int $offset Number of records to skip (for pagination)
     * @return array Array of event record objects
     */
    public function fetch_records(int $limit, int $offset): array {
        return $this->db->get('id', 'events', $limit, $offset);
    }
    
    /**
     * Get form-ready data based on current context
     * 
     * Determines whether to return existing record data (for editing)
     * or POST data/default values (for new forms or validation errors).
     * This is the main method called by controller's create() method.
     * 
     * @param int $update_id Record ID to edit, or 0 for new records
     * @return array Form data ready for view display
     * @example get_form_data(5) returns event #5 data for editing
     * @example get_form_data(0) returns POST data or defaults for new event
     */
    public function get_form_data(int $update_id = 0): array {
        if ($update_id > 0 && REQUEST_TYPE === 'GET') {
            return $this->get_data_for_edit($update_id);
        } else {
            return $this->get_data_from_post_or_defaults();
        }
    }

    /**
     * Get existing record data for editing
     * 
     * Fetches a single record from database and prepares it for form display.
     * Key conversion: MySQL DATETIME (YYYY-MM-DD HH:MM:SS) → ISO 8601 (YYYY-MM-DDTHH:MM)
     * 
     * @param int $update_id The record ID to fetch
     * @return array Record data with datetime converted for form_datetime_local()
     * @throws No explicit throws, but returns empty array if record not found
     */
    public function get_data_for_edit(int $update_id): array {
        $record = $this->db->get_where($update_id, 'events');
        
        if (empty($record)) {
            return [];
        }
        
        $data = (array) $record;
        
        // Convert MySQL DATETIME to ISO 8601 for datetime-local input
        if (isset($data['event_start']) && $data['event_start'] !== null) {
            $data['event_start'] = $this->convert_mysql_to_iso($data['event_start']);
        }
        
        return $data;
    }
    
    /**
     * Get form data from POST or use defaults
     * 
     * Used for new forms or when redisplaying form after validation errors.
     * Returns empty strings as defaults for a clean new form.
     * 
     * @return array Form data with proper types for view
     */
    private function get_data_from_post_or_defaults(): array {
        return [
            'event_name' => post('event_name', true) ?? '',
            'event_location' => post('event_location', true) ?? '',
            'event_start' => post('event_start', true) ?? ''
        ];
    }
    
    /**
     * Prepare POST data for database storage
     * 
     * Converts form submission data to database-ready format.
     * Key conversion: ISO 8601 (YYYY-MM-DDTHH:MM) → MySQL DATETIME (YYYY-MM-DD HH:MM:SS)
     * 
     * @return array Database-ready data with proper types
     */
    public function get_post_data_for_database(): array {
        $event_start_iso = post('event_start', true);
        
        return [
            'event_name' => post('event_name', true),
            'event_location' => post('event_location', true),
            'event_start' => $this->convert_iso_to_mysql($event_start_iso)
        ];
    }
    
    /**
     * Convert ISO 8601 datetime-local format to MySQL DATETIME format
     * 
     * Converts: 2025-12-27T14:30 → 2025-12-27 14:30:00
     * 
     * @param string $iso_datetime ISO 8601 format (YYYY-MM-DDTHH:MM)
     * @return string MySQL DATETIME format (YYYY-MM-DD HH:MM:SS)
     */
    public function convert_iso_to_mysql(string $iso_datetime): string {
        if (empty($iso_datetime)) {
            return '';
        }
        
        // Replace 'T' with space and append seconds
        return str_replace('T', ' ', $iso_datetime) . ':00';
    }
    
    /**
     * Convert MySQL DATETIME format to ISO 8601 datetime-local format
     * 
     * Converts: 2025-12-27 14:30:00 → 2025-12-27T14:30
     * 
     * @param string $mysql_datetime MySQL DATETIME format (YYYY-MM-DD HH:MM:SS)
     * @return string ISO 8601 format (YYYY-MM-DDTHH:MM)
     */
    public function convert_mysql_to_iso(string $mysql_datetime): string {
        if (empty($mysql_datetime)) {
            return '';
        }
        
        // Take first 16 characters (removes seconds) and replace space with 'T'
        return str_replace(' ', 'T', substr($mysql_datetime, 0, 16));
    }
    
    /**
     * Prepare raw database data for display in views
     * 
     * Adds formatted versions of fields while preserving raw data.
     * This is where you add display-friendly versions of data.
     * 
     * @param array $data Raw data from database
     * @return array Enhanced data with formatted fields
     * @example Converts event_start='2025-12-27 14:30:00' to 'December 27, 2025 at 2:30 PM'
     */
    public function prepare_for_display(array $data): array {
        // Format event start datetime for display if present
        if (isset($data['event_start']) && $data['event_start'] !== null && $data['event_start'] !== '') {
            try {
                $datetime = new DateTime($data['event_start']);
                // Full format: "December 27, 2025 at 2:30 PM"
                $data['event_start_formatted'] = $datetime->format('F j, Y \a\t g:i A');
                // Short format: "Dec 27, 2025 - 2:30 PM"
                $data['event_start_short'] = $datetime->format('M j, Y - g:i A');
                // Date only: "December 27, 2025"
                $data['event_date'] = $datetime->format('F j, Y');
                // Time only: "2:30 PM"
                $data['event_time'] = $datetime->format('g:i A');
            } catch (Exception $e) {
                $data['event_start_formatted'] = 'Invalid Date/Time';
                $data['event_start_short'] = 'N/A';
                $data['event_date'] = 'N/A';
                $data['event_time'] = 'N/A';
            }
        } else {
            $data['event_start_formatted'] = 'Not scheduled';
            $data['event_start_short'] = 'N/A';
            $data['event_date'] = 'N/A';
            $data['event_time'] = 'N/A';
        }
        
        return $data;
    }
    
    /**
     * Prepare multiple records for display in list views
     * 
     * Processes an array of records through prepare_for_display().
     * Maintains object structure for consistency with Trongate patterns.
     * 
     * @param array $rows Array of record objects from database
     * @return array Array of objects with formatted display fields
     */
    public function prepare_records_for_display(array $rows): array {
        $prepared = [];
        foreach ($rows as $row) {
            $row_array = (array) $row;
            $prepared[] = (object) $this->prepare_for_display($row_array);
        }
        return $prepared;
    }
}
