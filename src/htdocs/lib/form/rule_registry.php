<?php
namespace AirQualityInfo\Lib\Form;

class RuleRegistry {

    private $rules = array();

    public function __construct() {
        $this->ruleRegistry['required'] = new Rule(function($value, $options) {
            return $value != null && strlen($value) > 0;
        }, "%s is required");
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