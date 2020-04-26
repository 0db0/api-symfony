<?php

namespace App\Utils;

class AbstractConverter
{
    public function toArray()
    {
        $rf = new \ReflectionClass($this);
        $properties = $rf->getProperties();

        $array = [];

        foreach ($properties as $property) {
            $name = $property->getName();
            $methodName = sprintf('get%s', ucfirst($name));

            $array[$name] = $this->getPropertyValue($rf, $methodName);
        }
//dd($array);
        return $array;
    }

    public function setFromArray(array $array)
    {
        $rf = new \ReflectionClass($this);
        $properties = $rf->getProperties();

        if ($this->isNotValidArray($array, $properties)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Array size is invalid. Expected %d element(s), but %d given',
                        count($properties),
                        count($array)
                ));
        }

        foreach ($array as $key => $value) {
            $key = $this->getCamelCaseKeyName($key);
            array_map(function ($property) use ($key, $value) {
                if ($property->getName() === $key) {
                    $property->setAccessible(true);
                    $property->setValue($this, $value);
                }
            }, $properties);
        }
        dd($this);

        return $this;
    }

    private function getPropertyValue(\ReflectionClass $rf, $method)
    {
        return call_user_func($rf->getMethod($method)->getClosure($this));
    }

    private function isNotValidArray(array $array, array $properties): bool
    {
        return count($array) !== $properties;
    }

    private function getCamelCaseKeyName(string $key): string
    {
        if (!strpos($key, '_')) {
            return $key;
        }

        $replacement = ucfirst(substr($key, strpos($key, '_') + 1));
        if (strpos($replacement, '_')) {
            $replacement = $this->getCamelCaseKeyName($replacement);
        }
        $camelCaseKeyName = preg_replace('/_[a-zA-z]+/', $replacement, $key);

        return $camelCaseKeyName;
    }
}