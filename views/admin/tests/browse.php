<?php
echo head(
    array(
    'title' => __('Browsing Test History (%d total)', $total_results),
    )
);
require __DIR__ . "/../shared/nav.php";
echo flash();
?>

<?php echo pagination_links(); ?>
<button onclick="window.location.reload(true);" class="blue button"><?php echo __("Refresh"); ?></button>

<?php if (empty($job_diagnostics_tests)) : ?>
    <?php echo __("No test records."); ?>
<?php else : ?>
    <table id="tests">
        <thead>
            <tr>
                <?php
                echo browse_sort_links(
                    array(
                        __('ID') => 'id',
                        __('Dispatch Type') => null,
                        __('Started') => 'started',
                        __('Finished') => 'finished',
                        __('Error') => null,
                    ), array('link_tag' => 'th scope="col"', 'list_tag' => '')
                );
                ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($job_diagnostics_tests as $test) : ?>
            <tr>
                <td><?php echo $test->id; ?></td>
                <td><?php
                    switch ($test->dispatch_type) {
                        case JobDiagnostics_TestsController::SHORT_DISPATCH:
                            echo __("Short-Running Job");
                            break;
                        case JobDiagnostics_TestsController::LONG_DISPATCH:
                            echo __("Long-Running Job");
                            break;
                        default:
                            echo html_escape($test->dispatch_type);
                            break;
                    }
                ?></td>
                <td><?php echo $test->started; ?></td>
                <td><?php echo $test->finished; ?></td>
                <td><?php echo html_escape(($test->error) ? $test->error : __('[OK]')); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php echo foot();
