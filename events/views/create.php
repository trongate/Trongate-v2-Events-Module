<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Event Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        
        echo form_label('Event Name');
        echo form_input('event_name', $event_name, ["placeholder" => "Enter Event Name"]);
        
        echo form_label('Event Location');
        echo form_input('event_location', $event_location, ["placeholder" => "Enter Event Location"]);
        
        echo form_label('Event Start (Date & Time)');
        echo form_datetime_local('event_start', $event_start);

        echo '<div class="text-center">';
        echo anchor($cancel_url, 'Cancel', ['class' => 'button alt']);
        echo form_submit('submit', 'Submit');
        echo form_close();
        echo '</div>';
        ?>
    </div>
</div>
