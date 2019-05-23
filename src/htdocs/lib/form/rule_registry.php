<?php
namespace AirQualityInfo\Lib\Form;

class RuleRegistry {

    private $rules = array();

    public function __construct() {
        $this->rules['required'] = new Rule(function($value, $options) {
            return $value != null && strlen($value) > 0;
        }, "%s is required");
        $this->rules['minLength'] = new Rule(function($value, $options) {
            return $value != null && strlen($value) >= $options;
        }, "%s should be at least %d characters long");
        $this->rules['sameAs'] = new Rule(function($value, $options) {
            return $value === $_POST[$options];
        }, "%s should be the same as %s");
    }

    public function getRule($type) {
        if (isset($this->rules[$type])) {
            return $this->rules[$type];
        } else {
            throw new \Exception("Invalid rule type: $type");
        }
    }
}
?>