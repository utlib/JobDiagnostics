<?php
echo head(
    array(
    'title' => __('Showing Process %d', $process->id),
    )
);
require __DIR__ . "/../shared/nav.php";
echo flash();
?>

<p>I am the show page for process #<?php echo $process->id; ?></p>
