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
                    ), array('link_tag' => 'th scope="col"', 'list_tag' => '')
                );
                ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($job_diagnostics_tests as $test) : ?>
            <tr>
                <td><?php echo $test->id; ?></td>
                <td><?php echo __($test->dispatch_type); ?></td>
                <td><?php echo $test->started; ?></td>
                <td><?php echo $test->finished; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
