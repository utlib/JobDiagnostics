<?php
echo head(
    array(
    'title' => __('Job Diagnostics'),
    )
);
require __DIR__ . "/../shared/nav.php";
echo flash();
?>

<button onclick="window.location.reload(true);" class="blue button">Refresh</button>

<h3><?php echo __("Short-Running Jobs"); ?></h3>

<p><?php echo __("Short-running jobs are used in more routine tasks such as batch edits. Run this test if these appear to be not working."); ?></p>

<p>
    <?php if (empty($latest_short_running_test)) : ?>
        <?php echo __("Latest result: %s", $short_running_result) ?>
    <?php else : ?>
        <?php echo __("Latest result (%s): %s", format_date($latest_short_running_test->started), $short_running_result) ?>
    <?php endif; ?>
</p>

<?php if ($short_running_allow_test) : ?>
    <form action="tests/add" method="POST" style="display:inline;">
        <input type="hidden" name="dispatch_type" value="short_running">
        <button class="green button"><?php echo __("Test"); ?></button>
    </form>
<?php endif; ?>

<?php if ($short_running_allow_history) : ?>
    <a href="tests/browse?dispatch_type=short_running" class="blue button"><?php echo __("History"); ?></a>

    <form action="tests/clear" method="POST" style="display:inline;">
        <input type="hidden" name="dispatch_type" value="short_running">
        <button class="red button" onclick="return confirm(<?php echo html_escape(js_escape(__("Clear all short-running test records?"))); ?>);"><?php echo __("Clear"); ?></button>
    </form>
<?php endif; ?>

<h3><?php echo __("Long-Running Jobs"); ?></h3>

<p><?php echo __("Long-running jobs are used in plugins with longer import tasks, such as the CSV importer. Run this test if jobs started by plugins appear to be not working."); ?></p>

<p>
    <?php if (empty($latest_long_running_test)) : ?>
        <?php echo __("Latest result: %s", $long_running_result) ?>
    <?php else : ?>
        <?php echo __("Latest result (%s): %s", format_date($latest_long_running_test->started), $long_running_result) ?>
    <?php endif; ?>
</p>

<?php if ($long_running_allow_test) : ?>
    <form action="tests/add" method="POST" style="display:inline;">
        <input type="hidden" name="dispatch_type" value="long_running">
        <button class="green button"><?php echo __("Test"); ?></button>
    </form>
<?php endif; ?>

<?php if ($long_running_allow_history) : ?>
    <a href="tests/browse?dispatch_type=long_running" class="blue button"><?php echo __("History"); ?></a>

    <form action="tests/clear" method="POST" style="display:inline;">
        <input type="hidden" name="dispatch_type" value="long_running">
        <button class="red button" onclick="return confirm(<?php echo html_escape(js_escape(__("Clear all long-running test records?"))); ?>);"><?php echo __("Clear"); ?></button>
    </form>
<?php endif; ?>

<?php echo foot();
