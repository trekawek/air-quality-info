<?php
namespace AirQualityInfo\Lib\Form;

class Rule {

    private $validateFunction;

    private $message;

    public function __construct($validateFunction, $message) {
        $this->validateFunction = $validateFunction;
        $this->message = $message;
    }

    public function validate($value, $options = null) {
        return $this->validateFunction($value, $options);
    }

    public function getMessage() {
        return $message;
    }
}
?>