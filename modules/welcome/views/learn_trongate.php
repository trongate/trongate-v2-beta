<section class="resources">
<?php
$counter = 0;
foreach($resources as $resource) {
    $counter++;
    $subhead_class = ($counter === 1) ? 'mt-0' : 'mt-1';
    echo '<h3 class="'.$subhead_class.'">'.anchor(BASE_URL.$resource['url'], $resource['title']).'</h3>';
    echo '<p class="text-left sm">'.$resource['description'].'</p>';

    if ($counter < count($resources)) {
        echo '<hr class="mb-0">';
    }
}
?>
</section>
