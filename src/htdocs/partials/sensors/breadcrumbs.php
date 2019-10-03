<?php if(isset($breadcrumbs) && $breadcrumbs !== null): ?>
<?php
$bc = array_slice($breadcrumbs, 1, -1);
$bc = array_map(function($n) {
    return $n['description'];
}, $bc);
if (count($bc) > 0) {
    echo implode(' / ', $bc);
}
?>
<?php endif ?>