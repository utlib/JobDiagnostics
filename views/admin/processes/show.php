<?php
echo head(
    array(
        'title' => __('Process %d', $process->id),
    )
);
require __DIR__ . "/../shared/nav.php";
echo flash();
?>

<section class="seven columns alpha">
    <div class="element-set">
        <h2><?php echo __('Overview'); ?></h2>
        <div class="element">
            <div class="field two columns alpha">
                <label><?php echo __('Status'); ?></label>
            </div>
            <div class="element-text five columns omega">
                <p><?php echo __($process->status); ?></p>
            </div>
        </div>
        <?php if ($process->pid) : ?>
        <div class="element">
            <div class="field two columns alpha">
                <label><?php echo __('PID'); ?></label>
            </div>
            <div class="element-text five columns omega">
                <p><?php echo __($process->pid); ?></p>
            </div>
        </div>
        <?php endif; ?>
        <div class="element">
            <div class="field two columns alpha">
                <label><?php echo __('Run By'); ?></label>
            </div>
            <div class="element-text five columns omega">
                <p><?php echo ($user = get_db()->getTable('User')->find($process->user_id)) ? html_escape($user->name) : '[Removed User]'; ?></p>
            </div>
        </div>
        <div class="element">
            <div class="field two columns alpha">
                <label><?php echo __('Started at'); ?></label>
            </div>
            <div class="element-text five columns omega">
                <p><?php echo $process->started; ?></p>
            </div>
        </div>
        <div class="element">
            <div class="field two columns alpha">
                <label><?php echo __('Finished at'); ?></label>
            </div>
            <div class="element-text five columns omega">
                <p><?php echo $process->stopped ? $process->stopped : '&nbsp;'; ?></p>
            </div>
        </div>
    </div>

    <div class="element-set">
        <h2><?php echo __('Data'); ?></h2>
        <div class="element">
            <div class="element-text seven columns alpha" id="data-args" style="font-family: monospace; word-wrap: break-word;"><?php echo html_escape($process->args); ?></div>
        </div>
    </div>
</section>

<section class="three columns omega">
    <div class="panel">
        <a onclick="window.location.reload(true);" class="big blue button"><?php echo html_escape(__("Refresh")) ?></a>
        <a href="<?php echo admin_url(array('action' => 'browse'), 'job_diagnostics_processes'); ?>" class="big blue button"><?php echo html_escape(__("Return to Listings")) ?></a>
    </div>
</section>

<?php echo foot();
