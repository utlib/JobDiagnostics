<?php
echo head(
    array(
    'title' => __('Job Diagnostics'),
    )
);
require __DIR__ . "/../shared/nav.php";
echo flash();
?>

<h2><?php echo __("Test in Progress..."); ?></h2>

<progress id="test-progress" value="0" max="<?php echo JobDiagnostics_TestsController::TIMEOUT_LIMIT; ?>" data-record-id="<?php echo $job_diagnostics_test->id; ?>" data-poll-interval="<?php echo JobDiagnostics_TestsController::POLL_INTERVAL; ?>" data-timeout="<?php echo JobDiagnostics_TestsController::TIMEOUT_LIMIT; ?>" data-poll-path="<?php echo admin_url("/job-diagnostics/tests/wait-ajax/id/{$job_diagnostics_test->id}"); ?>"></progress>

<?php echo foot();
