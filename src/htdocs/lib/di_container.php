<?php
namespace AirQualityInfo\Lib;

class DiContainer {

    private $bindings = array();

    private $lazyBindings = array();

    public function setBinding($name, $value) {
        $this->bindings[$name] = $value;
    }

    public function setLazyBinding($name, $provider) {
        $this->lazyBindings[$name] = $provider;
    }

    public function addBindings($bindings) {
        foreach ($bindings as $n => $v) {
            $this->bindings[$n] = $v;
        }
    }

    public function injectClass($className) {
        if (!class_exists($className, true)) {
            throw new \Exception("Invalid class: $className");
        }
        $class = new \ReflectionClass($className);
        $constructor = $class->getConstructor();
        $constructorArgs = array();
        if ($constructor !== null) {
            foreach($constructor->getParameters() as $param) {
                $constructorArgs[] = $this->getParamValue($param);
            }
        }

        $object = new $className(...$constructorArgs);
        
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if ($method->getNumberOfParameters() === 1 && preg_match('/^set[A-Z]/', $methodName)) {
                $param = $method->getParameters()[0];
                $value = $this->getParamValue($param);
                $object->$methodName($value);
            }
        }
        return $object;
    }

    public function getBinding($name) {
        if (!isset($this->bindings[$name])) {
            if (isset($this->lazyBindings[$name])) {
                $this->bindings[$name] = $this->lazyBindings[$name]();
            } else {
                throw new \Exception("Can't find binding for $name");
            }
        }
        return $this->bindings[$name];
    }

    function getParamValue($param) {
        $paramName = $param->getName();
        if (!isset($this->bindings[$paramName])) {
            if (isset($this->lazyBindings[$paramName])) {
                $value = $this->lazyBindings[$paramName]();
                $this->bindings[$paramName] = $value;
            } else if ($param->hasType()) {
                $value = $this->injectClass($param->getType()->__toString());
                $this->bindings[$paramName] = $value;
            } else {
                //throw new \Exception("Can't find type for the $paramName in $className");
            }
        }
        return $this->bindings[$paramName];
    }
}

?>