<?php if(isset($breadcrumbs) && $breadcrumbs !== null): ?>
<ol class="breadcrumb">
<?php foreach($breadcrumbs as $node): ?>
    <?php $lastItem = (end($breadcrumbs) === $node) ?>
    <li class="breadcrumb-item <?php echo $lastItem ? 'active' : '' ?>">
        <?php if (!$lastItem || $lastItemLink): ?>
        <a href="<?php echo l('device_hierarchy', 'index', null, array('node_id' => $node['id'])) ?>">
        <?php endif ?>
            <?php if ($node['parent_id'] === null): ?>
            <?php echo __('Root') ?>
            <?php else: ?>
            <?php echo $node['description'] ?>
            <?php endif ?>
        <?php if (!$lastItem || $lastItemLink): ?>
        </a>
        <?php endif ?>
    </li>
<?php endforeach ?>
</ol>
<?php endif ?>