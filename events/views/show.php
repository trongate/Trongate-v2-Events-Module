<h1><?= $headline ?></h1>
<?= flashdata() ?>
<div class="card">
    <div class="card-heading">
        Event Details
    </div>
    <div class="card-body">
        <div class="text-right mb-3">
            <?= anchor($back_url, 'Back', array('class' => 'button alt')) ?>
            <?= anchor(BASE_URL.'events/create/'.$update_id, 'Edit', array('class' => 'button')) ?>
            <?= anchor('events/delete_conf/'.$update_id, 'Delete',  array('class' => 'button danger')) ?>
        </div>
        
        <div class="detail-grid">
            <div class="detail-row">
                <div class="detail-label">Event Name</div>
                <div class="detail-value"><?= out($event_name) ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Event Location</div>
                <div class="detail-value"><?= out($event_location) ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Event Start</div>
                <div class="detail-value"><?= out($event_start_formatted) ?></div>
            </div>
        </div>
    </div>
</div>
