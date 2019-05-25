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
    <input
        type="text"
        class="form-control"
        name="<?php echo $this->name ?>"
        id="<?php echo $this->name ?>Input"
        value="<?php echo $this->getEscapedValue() ?>"
        <?php echo $this->getAttributesString() ?>>

<?php if ($this->description): ?><span class="help-block"><?php echo __($this->description) ?></span><?php endif ?>

<?php if ($this->validationMessage): ?><div class="alert alert-danger" role="alert"><?php echo $this->validationMessage ?></div><?php endif ?>

<?php if (isset($this->options['prepend'])): ?>
</div>
<?php endif ?>

</div>