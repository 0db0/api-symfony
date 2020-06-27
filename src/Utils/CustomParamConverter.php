<?php

namespace App\Utils;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration)
    {
        $reflectionController = new \ReflectionMethod($request->attributes->get('_controller'));

        $parameters = $reflectionController->getParameters();

        foreach ($parameters as $parameter) {
            if (! $name = $configuration->getName()) {
                $name = $parameter->getName();
            }

            if (! $class = $configuration->getClass()) {
                $type = $parameter->getType();
                $class = $type->getName();

                if (strpos($class, 'App\Entity\\') === 0 || strpos($class, 'App\Dto\\') === 0) {
                    $reflectionClass = new \ReflectionClass($class);
                    $properties = $reflectionClass->getProperties();

                    if ($arguments = $this->getArguments($properties, $request)) {
                        $objectDto = $reflectionClass->newInstanceArgs($arguments);

                        $request->attributes->set($name, $objectDto);
                    }
                }
            }
        }
        return true;
    }

    private function getArguments(array $properties, Request $request): ?array
    {
        foreach ($properties as $property) {
            if ($argument = $request->request->get($property->getName())) {
                $arguments[] = $argument;
            }
        }

        return $arguments ?? null;
    }

    public function supports(ParamConverter $configuration)
    {
        return true;
//        return false;
    }
}