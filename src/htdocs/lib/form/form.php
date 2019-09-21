<?php
namespace AirQualityInfo\Lib\Form;

class Form {

    private $elements = array();

    private $formName;

    public function __construct($formName = null) {
        $this->formName = $formName;
    }

    public function addElement($name, $type, $label = '', $attributes = array(), $description = null) {
        $element = new FormElement($name, $type, $label, $attributes, $description);
        $this->elements[$name] = $element;
        return $element;
    }

    public function getElement($name) {
        return $this->elements[$name];
    }

    public function validate($data) {
        $ok = true;
        foreach ($this->elements as $name => $e) {
            $ok = $e->validate(isset($data[$name]) ? $data[$name] : null) && $ok;
        }
        return $ok;
    }

    public function render() {
        foreach ($this->elements as $e) {
            $e->render();
        }
        (new FormElement($this->formName, 'metadata', null))->render();
    }

    public function setDefaultValues($values) {
        foreach ($this->elements as $name => $e) {
            if (isset($values[$name])) {
                $e->setValue($values[$name]);
            }
        }
    }

    public function isSubmitted() {
        return $_SERVER['REQUEST_METHOD'] === 'POST' && ($this->formName === $_POST['_form_name']);
    }
}

?>