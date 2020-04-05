<?php

namespace App\Utils;

use App\Dto\CreatePostDto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomParamConverter implements ParamConverterInterface
{
    /** @var CreatePostDtoAssembler  */
    private $dtoAssembler;

    public function __construct(CreatePostDtoAssembler $dtoAssembler)
    {
        $this->dtoAssembler = $dtoAssembler;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $reflectionController = new \ReflectionMethod($request->attributes->get('_controller'));

        if (! $name = $configuration->getName()) {
            $name = $reflectionController->getParameters()[0]->getName();
        }

        if (! $class = $configuration->getClass()) {
            $class = (string) $reflectionController->getParameters()[0]->getType();
        }

        $reflectionClass = new \ReflectionClass($class);
        $arguments = $request->request->all();
        $objectDto = $reflectionClass->newInstanceArgs($arguments);

        $request->attributes->set($name, $objectDto);

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
//        if ($configuration->getClass() == null) {
//            return false;
//        }

        return true;
    }
}