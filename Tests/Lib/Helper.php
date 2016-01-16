<?php

namespace HelloWordPl\SimpleEntityGeneratorBundle\Tests\Lib;

use Doctrine\Common\Collections\ArrayCollection;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\PropertyManager;

/**
 * @author Sławomir Kania <slawomir.kania1@gmail.com>
 */
class Helper
{

    /**
     * @param string $name
     * @param string $type
     * @param string $comment
     * @param array $constraintsParts
     * @return PropertyManager
     */
    public static function prepareProperty($name, $type, $comment = "", array $constraintsParts = [])
    {
        $propertyManager = new PropertyManager();
        $propertyManager->setName($name);
        $propertyManager->setType($type);
        $propertyManager->setComment($comment);

        $constraintCollection = new ArrayCollection();
        foreach ($constraintsParts as $constraintPart) {
            $constraintCollection->add($constraintPart);
        }

        $propertyManager->setConstraints($constraintCollection);

        return $propertyManager;
    }

    /**
     * @return string
     */
    public static function getStructureYaml()
    {
        return '
-
  namespace: \AppBundle\Entity\User
  comment: "New User entity"
  properties:
    -
      name: username
      type: string
      comment: "Username for login"
      constraints:
        - NotBlank(message = \'Login can not be empty\')
        - NotNull(message = \'Login can not be null\')
    -
      name: email
      type: string
      comment: "User email"
      constraints:
        - NotBlank()
        - Email(message = \'Invalid email\')
    -
      name: active
      type: boolean
      comment: "Wether user is active or not"
      constraints:
        - IsTrue()
    -
      name: posts
      type: Doctrine\Common\Collections\ArrayCollection<AppBundle\Entity\Post>
      comment: User posts
    -
      # default comment
      name: created_at
      type: DateTime
    -
      # default comment
      name: updated_at
      type: DateTime
    -
      # default comment
      name: last_post
      type: AppBundle\Entity\Post
-
  namespace: \AppBundle\Entity\Post
  # no comment
  properties:
    -
      name: content
      type: string
      comment: "Post content"
      constraints:
        - NotBlank()
    -
      # default comment
      name: created_at
      type: DateTime
    -
      # default comment
      name: updated_at
      type: DateTime';
        ;
    }
}
