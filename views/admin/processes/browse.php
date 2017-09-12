<?php
echo head(
    array(
    'title' => __('Browsing Processes (%d total)', $total_results),
    )
);
require __DIR__ . "/../shared/nav.php";
echo flash();
?>

<?php echo pagination_links(); ?>
<button onclick="window.location.reload(true);" class="blue button"><?php echo __("Refresh"); ?></button>

<table id="processes">
    <thead>
        <tr>
            <?php
            echo browse_sort_links(
                array(
                __('ID') => 'id',
                __('PID') => null,
                __('Status') => 'status',
                __('User') => null,
                __('Started') => 'started',
                __('Stopped') => 'stopped',
                ), array('link_tag' => 'th scope="col"', 'list_tag' => '')
            );
            ?>
        </tr>
    </thead>
    <tbody>
        <?php $userTable = get_db()->getTable('User'); ?>
        <?php foreach ($processes as $process) : ?>
        <tr>
            <td><?php echo $process->id; ?></td>
            <td><?php echo $process->pid; ?></td>
            <td><?php echo __($process->status); ?></td>
            <td><?php echo ($user = $userTable->find($process->user_id)) ? html_escape($user->name) : '[Removed User]'; ?></td>
            <td><?php echo $process->started; ?></td>
            <td><?php echo $process->stopped; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
