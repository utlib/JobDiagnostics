<nav id="section-nav" class="navigation vertical">
<?php
echo nav(
    array(
    array(
        'label' => __('Diagnostics'),
        'uri' => url('job-diagnostics/tests'),
    ),
    array(
        'label' => __('Processes'),
        'uri' => url('job-diagnostics/processes'),
    ),
    ), 'admin_navigation_settings'
);
?>
</nav>
