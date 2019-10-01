<?php if (!empty($this->value)): ?>
<div class="form-group">

<?php if ($this->label): ?>
<label for="<?php echo $this->name ?>Input"><?php echo __($this->label) ?>
</label>
<?php endif ?>

<?php if (isset($this->options['prepend'])): ?>
<div class="input-group mb-4">
    <div class="input-group-append">
        <span class="input-group-text"><?php echo $this->options['prepend'] ?></span>
    </div>
<?php endif ?>

    <div>
    <img
        name="<?php echo $this->name ?>"
        id="<?php echo $this->name ?>Input"
        src="<?php echo l('attachment', 'get', null, array('name' => $this->value)) ?>"
        <?php echo $this->getAttributesString() ?>>
    </div>

<?php if ($this->description): ?><span class="help-block"><?php echo __($this->description) ?></span><?php endif ?>

<?php if ($this->validationMessage): ?><div class="alert alert-danger" role="alert"><?php echo $this->validationMessage ?></div><?php endif ?>

<?php if (isset($this->options['prepend'])): ?>
</div>
<?php endif ?>

</div>
<?php endif ?>