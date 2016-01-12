<?php

namespace HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items;

/**
 * Getter Method Manager
 *
 * @author Sławomir Kania <slawomir.kania1@gmail.com>
 */
class MethodGetterManager extends MethodManager
{

    /**
     * Return prepared method name from property eg. getNameOrSurname
     *
     * @return string
     */
    public function getPreparedName()
    {
        return sprintf("get%s", parent::getPreparedName());
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
            ." * <comment>\n"
            ." *\n"
            ." * @return <property_type>\n"
            ." */\n"
            ."public function <method_name>()\n"
            ."{\n"
            ."    return \$this-><property_name>;\n"
            ."}\n";
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
            self::TAG_PROPERTY_TYPE,
            self::TAG_METHOD_NAME,
            self::TAG_PROPERTY_NAME,
        ];
    }
}