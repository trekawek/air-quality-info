<div class="form-group">

<?php if ($this->label): ?>
<label for="<?php echo $this->name ?>Input"><?php echo __($this->label) ?>
</label>
<?php endif ?>

<select
    class="form-control"
    name="<?php echo $this->name ?>"
    id="<?php echo $this->name ?>Input"
    <?php echo $this->getAttributesString() ?>>
<?php foreach($this->options as $k => $v): ?>
    <option value="<?php echo $k ?>" <?php echo $k === $this->value ? 'selected' : '' ?>><?php echo $v ?></option>
<?php endforeach ?>
</select>

<?php if ($this->description): ?><span class="help-block"><?php echo __($this->description) ?></span><?php endif ?>

<?php if ($this->validationMessage): ?><div class="alert alert-danger" role="alert"><?php echo $this->validationMessage ?></div><?php endif ?>

</div>