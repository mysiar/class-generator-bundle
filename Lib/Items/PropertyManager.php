<?php

namespace HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use Exception;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Interfaces\MultilineCommentableInterface;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Interfaces\RenderableInterface;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\PropertyManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Tools;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Traits\MultilineCommentTrait;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\TypeParser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PropertyManager
 *
 * @author Sławomir Kania <slawomir.kania1@gmail.com>
 */
class PropertyManager implements RenderableInterface, MultilineCommentableInterface
{

    use MultilineCommentTrait;

    /**
     * Property predefined types
     *
     * @var string
     */
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_ARRAY_COLLECTION = 'Doctrine\Common\Collections\ArrayCollection';

    /**
     * Comment of property
     *
     * @Type("string")
     */
    private $comment = '';

    /**
     * Collection of Constraints
     *
     * @Type("Doctrine\Common\Collections\ArrayCollection<string>")
     */
    private $constraints = null;

    /**
     * Property name
     *
     * @Type("string")
     * @Assert\NotBlank(message="Property name can not be empty!")
     */
    private $name = '';

    /**
     * Property type
     *
     * @Type("string")
     * @Assert\NotBlank(message="Property type can not be empty!")
     */
    private $type = '';

    /**
     * Property serialized name, default is property name in snake format eg. for property $lastPost => last_post
     *
     * @Type("string")
     * @Assert\Type("string")
     */
    private $serializedName = '';

    /**
     * @Type("boolean")
     * @Assert\Type("boolean")
     */
    private $optional = false;

    /**
     * Constr.
     */
    public function __construct()
    {
        $this->constraints = new ArrayCollection();
    }

    /**
     * @Assert\IsTrue(message = "Invalid property name!")
     * @return boolean
     */
    public function isValidName()
    {
        if (false == Tools::isValidPropertyNameString($this->getName())) {
            return false;
        }

        return true;
    }

    /**
     * @Assert\IsTrue(message = "Invalid property type!")
     * @return boolean
     */
    public function isValidType()
    {
        if (false == Tools::isValidPropertyTypeString($this->getType())) {
            return false;
        }

        return true;
    }

    /**
     * @Assert\IsTrue(message = "Property has invalid validation constraint! Insert only constraint class with parameters eg. NotBlank() or Email(message = 'Invalid email')")
     */
    public function hasAllCallableConstraintIfHasAny()
    {
        if (false == $this->hasConstraints()) {
            return true;
        }

        foreach ($this->getConstraintAnnotationCollection() as $constraintAnnotation) {
            if (false == Tools::isCallableConstraintAnnotation($constraintAnnotation)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check is property boolean type
     *
     * @return boolean
     */
    public function isTypeBoolean()
    {
        return PropertyManager::TYPE_BOOLEAN == $this->getType();
    }

    /**
     * Check is property ArrayCollection type
     *
     * @return boolean
     */
    public function isTypeArrayCollection()
    {
        return PropertyManager::TYPE_ARRAY_COLLECTION == $this->getTypeName();
    }

    /**
     * Return property comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set property comment
     *
     * @param string $comment
     * @return PropertyManager
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Return constraints parts array
     *
     * @return ArrayCollection
     */
    public function getConstraints()
    {
        if (false == ($this->constraints instanceof ArrayCollection)) {
            return new ArrayCollection();
        }

        return $this->constraints;
    }

    /**
     * Check that property has constraints
     *
     * @return boolean
     */
    public function hasConstraints()
    {
        return (false == $this->getConstraints()->isEmpty());
    }

    /**
     * Return constraints names array
     *
     * @return ArrayCollection
     */
    public function getConstraintAnnotationCollection()
    {
        $constraintsFull = new ArrayCollection();
        foreach ($this->getConstraints() as $constraintPart) {
            $constraintsFull->add(sprintf("@\Symfony\Component\Validator\Constraints\%s", $constraintPart));
        }

        return $constraintsFull;
    }

    /**
     * Set constraints names array
     *
     * @param ArrayCollection $constraints
     * @return PropertyManager
     */
    public function setConstraints(ArrayCollection $constraints)
    {
        $this->constraints = $constraints;
        return $this;
    }

    /**
     * Return property name string
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return prepared property name eg. name_and_surname => nameAndSurname
     *
     * @param string $propertyName
     * @return string
     */
    public function getPreparedName()
    {
        return Inflector::camelize($this->getName());
    }

    /**
     * Set property name
     *
     * @param string $name
     * @return PropertyManager
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Return property type string
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return only type name without params eg. Doctrine\Common\Collections\ArrayCollection<AppBundle\Entity\Post> result Doctrine\Common\Collections\ArrayCollection
     *
     * @return string
     * @throws Exception
     */
    public function getTypeName()
    {
        $typeParser = new TypeParser();
        $result = $typeParser->parse($this->getType());

        if (false == array_key_exists("name", $result)) {
            throw new Exception("Invalid type parsing result");
        }

        return $result["name"];
    }

    /**
     * Set property type
     *
     * @param string $type
     * @return PropertyManager
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Return custom serialized name
     *
     * @return string
     */
    public function getSerializedName()
    {
        return $this->serializedName;
    }

    /**
     * Set custom serialized name form property
     *
     * @param string $serializedName
     * @return PropertyManager
     */
    public function setSerializedName($serializedName)
    {
        $this->serializedName = $serializedName;
        return $this;
    }

    /**
     * Check that property has custom serialized name
     *
     * @return boolean
     */
    public function hasSerializedName()
    {
        return false == empty($this->serializedName);
    }

    /**
     * @return boolean
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * @param boolean $optional
     */
    public function setOptional($optional)
    {
        $this->optional = $optional;
    }

    /**
     * Return common element template
     *
     * @return string
     */
    public function getTemplate()
    {
        return ""
            ."/**\n"
            ." * <comment><multiline_comment>\n"
            ." *\n"
            ."<constraints>"
            ."<jms_part>"
            ." * @var <type>\n"
            ." */\n"
            ."private $<name>;";
    }

    /**
     * Return set of tags used in template
     *
     * @return array
     */
    public function getTemplateTags()
    {
        return [
            self::TAG_COMMENT,
            self::TAG_CONSTRAINTS,
            self::TAG_JMS_PART,
            self::TAG_TYPE,
            self::TAG_NAME,
            self::TAG_MULTILINE_COMMENT,
        ];
    }
}
