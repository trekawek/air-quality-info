<div class="form-group">

<?php if ($this->label): ?>
<label for="<?php echo $this->name ?>Input"><?php echo $this->label ?>
</label>
<?php endif ?>

<input
    type="password"
    class="form-control"
    name="<?php echo $this->name ?>"
    id="<?php echo $this->name ?>Input"
    value="<?php echo $this->getEscapedValue() ?>"
    <?php echo $this->getAttributesString() ?>>

<?php if ($this->description): ?><span class="help-block"><?php echo $this->description ?></span><?php endif ?>

<?php if ($this->validationMessage): ?><div class="invalid-feedback"><?php echo $this->validationMessage ?></div><?php endif ?>

</div>