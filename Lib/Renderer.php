<?php

namespace HelloWordPl\SimpleEntityGeneratorBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Exceptions\RendererException;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Exceptions\UnrecognizedItemToRenderException;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Interfaces\RenderableInterface;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Interfaces\SetterMethodInterface;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\ClassConstructorManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\ClassManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\InitPropertyManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\InterfaceManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\MethodGetterBooleanInterfaceManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\MethodGetterBooleanManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\MethodGetterInterfaceManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\MethodGetterManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\MethodManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\PropertyManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\TestClassManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\TestMethodManager;

/**
 * Factory for rendering items
 *
 * @author Sławomir Kania <slawomir.kania1@gmail.com>
 */
class Renderer
{

    /**
     * No indention
     *
     * @var integer
     */
    const INDENT_NO_INDENT = 0;

    /**
     * One indention
     *
     * @var integer
     */
    const INDENT_4_SPACES = 4;

    /**
     * Render renderable item
     *
     * @param RenderableInterface $item
     * @return string
     * @throws RendererException
     * @throws UnrecognizedItemToRenderException
     */
    public function render(RenderableInterface $item)
    {
        switch (true) {
            case $item instanceof ClassManager:
                return $this->renderClass($item);
            case $item instanceof ClassConstructorManager:
                return $this->renderClassConstructor($item);
            case $item instanceof InitPropertyManager:
                return $this->renderInitProperty($item);
            case $item instanceof InterfaceManager:
                return $this->renderInterface($item);
            case $item instanceof PropertyManager:
                return $this->renderProperty($item);
            case $item instanceof MethodManager:
                return $this->renderMethod($item);
            case $item instanceof TestClassManager:
                return $this->renderTestClass($item);
            case $item instanceof TestMethodManager:
                return $this->renderTestMethod($item);
            default:
                throw $this->getExceptionUnrecognizedItem($item);
        }
    }

    /**
     * @param string $content
     * @param ClassConstructorManager $constructor
     * @param int $position
     * @return string
     */
    public function renderAndPutConstructorBodyToContent($content, ClassConstructorManager $constructor, $position = 0)
    {
        $source = Tools::explodeTemplateStringToArray($content);
        foreach ($constructor->getInitProperties() as $initProperty) {
            $this->putElementIntoSource($source, $position, $this->addIndentation($this->render($initProperty), self::INDENT_4_SPACES));
        }

        return Tools::implodeArrayToTemplate($source);
    }

    /**
     * @param string $content
     * @param ArrayCollection $itemsToRender
     * @param int $position
     * @return string
     */
    public function renderAndPutItemsToContent($content, ArrayCollection $itemsToRender, $position = 0)
    {
        $itemsRendered = [];
        foreach ($itemsToRender as $itemToRender) {
            $itemsRendered[] = $this->render($itemToRender);
        }

        return $this->updateSourceWithElements($content, $position, $itemsRendered);
    }

    /**
     * @param InitPropertyManager $initProperty
     * @return string
     * @throws RendererException
     */
    protected function renderInitProperty(InitPropertyManager $initProperty)
    {
        $template = $initProperty->getTemplate();
        $tags = $initProperty->getTemplateTags();

        $args[RenderableInterface::TAG_PROPERTY_NAME] = $initProperty->getProperty()->getPreparedName();
        $args[RenderableInterface::TAG_PROPERTY_TYPE] = sprintf('\%s', $initProperty->getProperty()->getTypeName());
        return $this->addIndentation($this->replace($tags, $args, $template), self::INDENT_4_SPACES);
    }

    /**
     * @param ClassConstructorManager $classConstructor
     * @return string
     * @throws RendererException
     */
    protected function renderClassConstructor(ClassConstructorManager $classConstructor)
    {
        $template = $classConstructor->getTemplate();
        $tags = $classConstructor->getTemplateTags();

        $initProperties = [];
        foreach ($classConstructor->getInitProperties() as $initProperty) {
            $initProperties[] = $this->render($initProperty);
        }

        $args[RenderableInterface::TAG_INIT_PROPERTIES] = Tools::implodeArrayToTemplate($initProperties);
        return $this->addIndentation($this->replace($tags, $args, $template), self::INDENT_4_SPACES);
    }

    /**
     * @param MethodManager $method
     * @return string
     * @throws RendererException
     * @throws UnrecognizedItemToRenderException
     */
    protected function renderMethod(MethodManager $method)
    {
        $template = $method->getTemplate();
        $tags = $method->getTemplateTags();

        $property = $method->getProperty();
        $propertyName = $property->getPreparedName();
        $methodName = $method->getPreparedName();
        $comment = sprintf('For property "%s"', $propertyName);

        $args = [];
        switch (true) {
            case $method instanceof MethodGetterManager:
                $args[RenderableInterface::TAG_COMMENT] = $comment;
                $args[RenderableInterface::TAG_PROPERTY_TYPE] = $property->getType();
                $args[RenderableInterface::TAG_METHOD_NAME] = $methodName;
                $args[RenderableInterface::TAG_PROPERTY_NAME] = $propertyName;
                break;
            case $method instanceof MethodGetterInterfaceManager:
                $args[RenderableInterface::TAG_COMMENT] = $comment;
                $args[RenderableInterface::TAG_PROPERTY_TYPE] = $property->getType();
                $args[RenderableInterface::TAG_METHOD_NAME] = $methodName;
                break;
            case $method instanceof MethodGetterBooleanManager:
                $args[RenderableInterface::TAG_COMMENT] = $comment;
                $args[RenderableInterface::TAG_PROPERTY_TYPE] = $property->getType();
                $args[RenderableInterface::TAG_METHOD_NAME] = $methodName;
                $args[RenderableInterface::TAG_PROPERTY_NAME] = $propertyName;
                break;
            case $method instanceof MethodGetterBooleanInterfaceManager:
                $args[RenderableInterface::TAG_COMMENT] = $comment;
                $args[RenderableInterface::TAG_PROPERTY_TYPE] = $property->getType();
                $args[RenderableInterface::TAG_METHOD_NAME] = $methodName;
                break;
            case $method instanceof SetterMethodInterface:
                $typeHintitng = '';
                if ($method->canAddTypeHinting()) {
                    $typeHintitng = sprintf('\%s ', $property->getTypeName());
                }

                $args[RenderableInterface::TAG_COMMENT] = $comment;
                $args[RenderableInterface::TAG_PROPERTY_TYPE] = $property->getType();
                $args[RenderableInterface::TAG_TYPE_HINTING] = $typeHintitng;
                $args[RenderableInterface::TAG_METHOD_NAME] = $methodName;
                $args[RenderableInterface::TAG_PROPERTY_NAME] = $propertyName;
                break;
            default:
                throw $this->getExceptionUnrecognizedItem($method);
        }

        return $this->addIndentation($this->replace($tags, $args, $template), self::INDENT_4_SPACES);
    }

    /**
     * @param ClassManager $class
     * @return string
     * @throws RendererException
     */
    protected function renderClass(ClassManager $class)
    {
        $template = $class->getTemplate();
        $tags = $class->getTemplateTags();

        $properties = [];
        $methods = [];
        $interfacePart = '';
        foreach ($class->getProperties() as $property) {
            $properties[] = $this->render($property);
        }
        foreach ($class->getMethods() as $method) {
            $methods[] = $this->render($method);
        }
        if ($class->hasInterface()) {
            $interfacePart = sprintf("implements %s", $class->getInterface()->getNamespace());
        }

        $args[RenderableInterface::TAG_NAMESPACE] = $class->getNamespaceWithoutNameAndBackslashPrefix();
        $args[RenderableInterface::TAG_COMMENT] = $class->getComment();
        $args[RenderableInterface::TAG_NAME] = $class->getName();
        $args[RenderableInterface::TAG_INTERFACE] = $interfacePart;
        $args[RenderableInterface::TAG_CONSTRUCTOR] = $this->render($class->getConstructor());
        $args[RenderableInterface::TAG_PROPERTIES] = Tools::implodeArrayToTemplate($properties);
        $args[RenderableInterface::TAG_METHODS] = Tools::implodeArrayToTemplate($methods);

        return $this->replace($tags, $args, $template);
    }

    /**
     * @param InterfaceManager $interface
     * @return string
     * @throws RendererException
     */
    protected function renderInterface(InterfaceManager $interface)
    {
        $template = $interface->getTemplate();
        $tags = $interface->getTemplateTags();

        $methods = [];
        foreach ($interface->getMethods() as $method) {
            $methods[] = $this->render($method);
        }

        $args[RenderableInterface::TAG_NAMESPACE] = $interface->getNamespaceWithoutNameAndBackslashPrefix();
        $args[RenderableInterface::TAG_COMMENT] = $interface->getComment();
        $args[RenderableInterface::TAG_NAME] = $interface->getName();
        $args[RenderableInterface::TAG_METHODS] = Tools::implodeArrayToTemplate($methods);

        return $this->replace($tags, $args, $template);
    }

    /**
     * @param PropertyManager $property
     * @return string
     * @throws RendererException
     */
    protected function renderProperty(PropertyManager $property)
    {
        $template = $property->getTemplate();
        $tags = $property->getTemplateTags();

        $validatorsPart = '';
        $validators = $property->getValidators();
        if ($validators->count() > 0) {
            $validatorsStringArray = [];
            foreach ($validators as $validator) {
                $validatorsStringArray[] = sprintf(" * @\Symfony\Component\Validator\Constraints\%s", $validator);
            }

            $validatorsPart = Tools::implodeArrayToTemplate($validatorsStringArray)."\n";
        }

        $comment = $property->getComment();
        if (empty($comment)) {
            $comment = "'".$property->getName()."' property";
        }

        $args[RenderableInterface::TAG_COMMENT] = $comment;
        $args[RenderableInterface::TAG_VALIDATORS] = $validatorsPart;
        $args[RenderableInterface::TAG_TYPE] = $property->getType();
        $args[RenderableInterface::TAG_NAME] = $property->getPreparedName();

        return $this->addIndentation($this->replace($tags, $args, $template), self::INDENT_4_SPACES);
    }

    /**
     * @param TestClassManager $testClass
     * @return string
     * @throws RendererException
     */
    protected function renderTestClass(TestClassManager $testClass)
    {
        $template = $testClass->getTemplate();
        $tags = $testClass->getTemplateTags();

        $methods = [];
        foreach ($testClass->getMethods() as $method) {
            $methods[] = $this->render($method);
        }

        $class = $testClass->getClassManager();
        $args[RenderableInterface::TAG_NAMESPACE] = $testClass->getNamespaceWithoutNameAndBackslashPrefix();
        $args[RenderableInterface::TAG_COMMENT] = $testClass->getComment();
        $args[RenderableInterface::TAG_NAME] = $testClass->getName();
        $args[RenderableInterface::TAG_INTERFACE] = $class->getInterface()->getNamespace();
        $args[RenderableInterface::TAG_CLASS] = $class->getNamespace();
        $args[RenderableInterface::TAG_METHODS] = Tools::implodeArrayToTemplate($methods);

        return $this->replace($tags, $args, $template);
    }

    /**
     * @param TestMethodManager $testMethod
     * @return string
     * @throws RendererException
     */
    protected function renderTestMethod(TestMethodManager $testMethod)
    {
        $template = $testMethod->getTemplate();
        $tags = $testMethod->getTemplateTags();

        $args[RenderableInterface::TAG_CLASS] = $testMethod->getMethod()->getClassManager()->getNamespace();
        $args[RenderableInterface::TAG_METHOD_NAME] = $testMethod->getMethod()->getPreparedName();
        $args[RenderableInterface::TAG_TEST_METHOD_NAME] = $testMethod->getPreparedName();

        return $this->addIndentation($this->replace($tags, $args, $template), self::INDENT_4_SPACES);
    }

    /**
     * Replace tags in template witch arguments
     *
     * @param array $tags
     * @param array $args
     * @param string $template
     * @return string
     * @throws RendererException
     */
    protected function replace($tags, $args, $template)
    {
        if ($tags !== array_keys($args)) {
            throw new RendererException("Tags and keys are not identical!");
        }

        return str_replace($tags, array_values($args), $template);
    }

    /**
     * Add Indentation to template
     *
     * @param string $template
     * @param integer $spaces
     */
    protected function addIndentation($template, $spaces = self::INDENT_NO_INDENT)
    {
        $parts = Tools::explodeTemplateStringToArray($template);
        array_walk(
            $parts,
            function (&$value) use ($spaces) {
                $value = str_pad($value, strlen($value) + (int) $spaces, " ", STR_PAD_LEFT);
            }
        );

        return Tools::implodeArrayToTemplate($parts);
    }

    /**
     * @param mixed $item
     * @return UnrecognizedItemToRenderException
     */
    protected function getExceptionUnrecognizedItem($item)
    {
        return new UnrecognizedItemToRenderException(sprintf("Unrecognized item: %s", get_class($item)));
    }

    /**
     * Update source file with rendered elements
     *
     * @param string $content
     * @param integer $startPosition
     * @param array $renderedElements
     * @return string
     */
    protected function updateSourceWithElements($content, $startPosition = 0, array $renderedElements = [])
    {
        $source = Tools::explodeTemplateStringToArray($content);
        foreach ($renderedElements as $renderedElement) {
            $this->putElementIntoSource($source, $startPosition, $renderedElement);
        }

        return Tools::implodeArrayToTemplate($source);
    }

    /**
     * @param array $source
     * @param integer $offset
     * @param string $element
     */
    protected function putElementIntoSource(&$source, $offset, $element)
    {
        array_splice($source, $offset, 0, [$element]);
    }
}