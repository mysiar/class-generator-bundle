<?php

namespace HelloWordPl\SimpleEntityGeneratorBundle\Tests\Lib;

use Doctrine\Common\Collections\ArrayCollection;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\ClassConstructorManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\ClassManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Items\InitPropertyManager;
use HelloWordPl\SimpleEntityGeneratorBundle\Lib\Renderer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Renderer Test
 *
 * @author Sławomir Kania <slawomir.kania1@gmail.com>
 */
class RendererTest extends KernelTestCase
{

    /**
     * @var string
     */
    private $postClassWithUpdatedConstructor = <<<EOT
<?php

namespace AppBundle\Entity;

/**
 *
 */
class Post implements \AppBundle\Entity\PostInterface
{

    /**
     * Post content
     *
     * @\Symfony\Component\Validator\Constraints\NotBlank()
     * @\JMS\Serializer\Annotation\Type("string")
     * @\JMS\Serializer\Annotation\SerializedName("content")
     * @var string
     */
    private \$content;

    /**
     * 'created_at' property
     *
     * @\JMS\Serializer\Annotation\Type("DateTime")
     * @\JMS\Serializer\Annotation\SerializedName("created_at")
     * @var DateTime
     */
    private \$createdAt;

    /**
     * 'updated_at' property
     *
     * @\JMS\Serializer\Annotation\Type("DateTime")
     * @\JMS\Serializer\Annotation\SerializedName("updated_at")
     * @var DateTime
     */
    private \$updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        \$this->collection = new \Doctrine\Common\Collections\ArrayCollection();
        \$this->collection2 = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * For property "content"
     *
     * @param string \$content
     * @return this
     */
    public function setContent(\$content)
    {
        \$this->content = \$content;
        return \$this;
    }

    /**
     * For property "content"
     *
     * @return string
     */
    public function getContent()
    {
        return \$this->content;
    }

    /**
     * For property "createdAt"
     *
     * @param DateTime \$createdAt
     * @return this
     */
    public function setCreatedAt(\DateTime \$createdAt)
    {
        \$this->createdAt = \$createdAt;
        return \$this;
    }

    /**
     * For property "createdAt"
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return \$this->createdAt;
    }

    /**
     * For property "updatedAt"
     *
     * @param DateTime \$updatedAt
     * @return this
     */
    public function setUpdatedAt(\DateTime \$updatedAt)
    {
        \$this->updatedAt = \$updatedAt;
        return \$this;
    }

    /**
     * For property "updatedAt"
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return \$this->updatedAt;
    }

}

EOT;

    /**
     * @var string
     */
    private $postClassWithNewPropertyExpected = <<<EOT
<?php

namespace AppBundle\Entity;

/**
 *
 */
class Post implements \AppBundle\Entity\PostInterface
{

    /**
     * Post content
     *
     * @\Symfony\Component\Validator\Constraints\NotBlank()
     * @\JMS\Serializer\Annotation\Type("string")
     * @\JMS\Serializer\Annotation\SerializedName("content")
     * @var string
     */
    private \$content;

    /**
     * 'created_at' property
     *
     * @\JMS\Serializer\Annotation\Type("DateTime")
     * @\JMS\Serializer\Annotation\SerializedName("created_at")
     * @var DateTime
     */
    private \$createdAt;

    /**
     * 'updated_at' property
     *
     * @\JMS\Serializer\Annotation\Type("DateTime")
     * @\JMS\Serializer\Annotation\SerializedName("updated_at")
     * @var DateTime
     */
    private \$updatedAt;

    /**
     * is post active
     *
     * @\Symfony\Component\Validator\Constraints\IsTrue()
     * @\JMS\Serializer\Annotation\Type("boolean")
     * @\JMS\Serializer\Annotation\SerializedName("active")
     * @var boolean
     */
    private \$active;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * For property "content"
     *
     * @param string \$content
     * @return this
     */
    public function setContent(\$content)
    {
        \$this->content = \$content;
        return \$this;
    }

    /**
     * For property "content"
     *
     * @return string
     */
    public function getContent()
    {
        return \$this->content;
    }

    /**
     * For property "createdAt"
     *
     * @param DateTime \$createdAt
     * @return this
     */
    public function setCreatedAt(\DateTime \$createdAt)
    {
        \$this->createdAt = \$createdAt;
        return \$this;
    }

    /**
     * For property "createdAt"
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return \$this->createdAt;
    }

    /**
     * For property "updatedAt"
     *
     * @param DateTime \$updatedAt
     * @return this
     */
    public function setUpdatedAt(\DateTime \$updatedAt)
    {
        \$this->updatedAt = \$updatedAt;
        return \$this;
    }

    /**
     * For property "updatedAt"
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return \$this->updatedAt;
    }

}

EOT;

    /**
     * @var string
     */
    private $postClassExpected = <<<EOT
<?php

namespace AppBundle\Entity;

/**
 *
 */
class Post implements \AppBundle\Entity\PostInterface
{

    /**
     * Post content
     *
     * @\Symfony\Component\Validator\Constraints\NotBlank()
     * @\JMS\Serializer\Annotation\Type("string")
     * @\JMS\Serializer\Annotation\SerializedName("content")
     * @var string
     */
    private \$content;

    /**
     * 'created_at' property
     *
     * @\JMS\Serializer\Annotation\Type("DateTime")
     * @\JMS\Serializer\Annotation\SerializedName("created_at")
     * @var DateTime
     */
    private \$createdAt;

    /**
     * 'updated_at' property
     *
     * @\JMS\Serializer\Annotation\Type("DateTime")
     * @\JMS\Serializer\Annotation\SerializedName("updated_at")
     * @var DateTime
     */
    private \$updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * For property "content"
     *
     * @param string \$content
     * @return this
     */
    public function setContent(\$content)
    {
        \$this->content = \$content;
        return \$this;
    }

    /**
     * For property "content"
     *
     * @return string
     */
    public function getContent()
    {
        return \$this->content;
    }

    /**
     * For property "createdAt"
     *
     * @param DateTime \$createdAt
     * @return this
     */
    public function setCreatedAt(\DateTime \$createdAt)
    {
        \$this->createdAt = \$createdAt;
        return \$this;
    }

    /**
     * For property "createdAt"
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return \$this->createdAt;
    }

    /**
     * For property "updatedAt"
     *
     * @param DateTime \$updatedAt
     * @return this
     */
    public function setUpdatedAt(\DateTime \$updatedAt)
    {
        \$this->updatedAt = \$updatedAt;
        return \$this;
    }

    /**
     * For property "updatedAt"
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return \$this->updatedAt;
    }

}

EOT;

    /**
     * @var string
     */
    private $userClassExpected = <<<EOT
<?php

namespace AppBundle\Entity;

/**
 * New User entity
 *
 * lorem ispum
 * second row
 * @\Doctrine\Common\Annotations\Entity()
 */
class User extends \AppBundle\Entity\Base implements \AppBundle\Entity\UserInterface
{

    /**
     * Username for login
     *
     * @\Symfony\Component\Validator\Constraints\NotBlank(message = "Login can not be empty")
     * @\Symfony\Component\Validator\Constraints\NotNull(message = "Login can not be null")
     * @\JMS\Serializer\Annotation\Type("string")
     * @\JMS\Serializer\Annotation\SerializedName("username")
     * @var string
     */
    private \$username;

    /**
     * User email
     *
     * @\Doctrine\Common\Annotations\Column()
     * lorem ispum
     * third row
     *
     * @\Symfony\Component\Validator\Constraints\NotBlank()
     * @\Symfony\Component\Validator\Constraints\Email(message = "Invalid email")
     * @\JMS\Serializer\Annotation\Type("string")
     * @\JMS\Serializer\Annotation\SerializedName("email")
     * @var string
     */
    private \$email;

    /**
     * Wether user is active or not
     *
     * @\Symfony\Component\Validator\Constraints\IsTrue()
     * @\JMS\Serializer\Annotation\Type("boolean")
     * @\JMS\Serializer\Annotation\SerializedName("active")
     * @var boolean
     */
    private \$active;

    /**
     * User posts
     *
     * @\JMS\Serializer\Annotation\Type("Doctrine\Common\Collections\ArrayCollection<AppBundle\Entity\Post>")
     * @\JMS\Serializer\Annotation\SerializedName("posts")
     * @var Doctrine\Common\Collections\ArrayCollection<AppBundle\Entity\Post>
     */
    private \$posts;

    /**
     * 'created_at' property
     *
     * @\JMS\Serializer\Annotation\Type("DateTime")
     * @\JMS\Serializer\Annotation\SerializedName("created_at")
     * @var DateTime
     */
    private \$createdAt;

    /**
     * 'updated_at' property
     *
     * @\JMS\Serializer\Annotation\Type("DateTime")
     * @\JMS\Serializer\Annotation\SerializedName("updated_at")
     * @var DateTime
     */
    private \$updatedAt;

    /**
     * 'last_post' property
     *
     * @\JMS\Serializer\Annotation\Type("AppBundle\Entity\Post")
     * @\JMS\Serializer\Annotation\SerializedName("lastPost")
     * @var AppBundle\Entity\Post
     */
    private \$lastPost;

    /**
     * Constructor.
     */
    public function __construct()
    {
        \$this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * For property "username"
     *
     * @param string \$username
     * @return this
     */
    public function setUsername(\$username)
    {
        \$this->username = \$username;
        return \$this;
    }

    /**
     * For property "username"
     *
     * @return string
     */
    public function getUsername()
    {
        return \$this->username;
    }

    /**
     * For property "email"
     *
     * @param string \$email
     * @return this
     */
    public function setEmail(\$email)
    {
        \$this->email = \$email;
        return \$this;
    }

    /**
     * For property "email"
     *
     * @return string
     */
    public function getEmail()
    {
        return \$this->email;
    }

    /**
     * For property "active"
     *
     * @return boolean
     */
    public function isActive()
    {
        return (bool) \$this->active;
    }

    /**
     * For property "active"
     *
     * @param boolean \$active
     * @return this
     */
    public function setActive(\$active)
    {
        \$this->active = \$active;
        return \$this;
    }

    /**
     * For property "active"
     *
     * @return boolean
     */
    public function getActive()
    {
        return \$this->active;
    }

    /**
     * For property "posts"
     *
     * @param Doctrine\Common\Collections\ArrayCollection<AppBundle\Entity\Post> \$posts
     * @return this
     */
    public function setPosts(\Doctrine\Common\Collections\ArrayCollection \$posts)
    {
        \$this->posts = \$posts;
        return \$this;
    }

    /**
     * For property "posts"
     *
     * @return Doctrine\Common\Collections\ArrayCollection<AppBundle\Entity\Post>
     */
    public function getPosts()
    {
        return \$this->posts;
    }

    /**
     * For property "createdAt"
     *
     * @param DateTime \$createdAt
     * @return this
     */
    public function setCreatedAt(\DateTime \$createdAt)
    {
        \$this->createdAt = \$createdAt;
        return \$this;
    }

    /**
     * For property "createdAt"
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return \$this->createdAt;
    }

    /**
     * For property "updatedAt"
     *
     * @param DateTime \$updatedAt
     * @return this
     */
    public function setUpdatedAt(\DateTime \$updatedAt)
    {
        \$this->updatedAt = \$updatedAt;
        return \$this;
    }

    /**
     * For property "updatedAt"
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return \$this->updatedAt;
    }

    /**
     * For property "lastPost"
     *
     * @param AppBundle\Entity\Post \$lastPost
     * @return this
     */
    public function setLastPost(\AppBundle\Entity\Post \$lastPost)
    {
        \$this->lastPost = \$lastPost;
        return \$this;
    }

    /**
     * For property "lastPost"
     *
     * @return AppBundle\Entity\Post
     */
    public function getLastPost()
    {
        return \$this->lastPost;
    }

}

EOT;

    /**
     * @var string
     */
    private $userInterfaceExpected = <<<EOT
<?php

namespace AppBundle\Entity;

/**
 * Interface for entity : \AppBundle\Entity\User
 */
interface UserInterface
{

    /**
     * For property "username"
     * @param string \$username
     * @return this
     */
    public function setUsername(\$username);

    /**
     * For property "username"
     * @return string
     */
    public function getUsername();

    /**
     * For property "email"
     * @param string \$email
     * @return this
     */
    public function setEmail(\$email);

    /**
     * For property "email"
     * @return string
     */
    public function getEmail();

    /**
     * For property "active"
     *
     * @return boolean
     */
    public function isActive();

    /**
     * For property "active"
     * @param boolean \$active
     * @return this
     */
    public function setActive(\$active);

    /**
     * For property "active"
     * @return boolean
     */
    public function getActive();

    /**
     * For property "posts"
     * @param Doctrine\Common\Collections\ArrayCollection<AppBundle\Entity\Post> \$posts
     * @return this
     */
    public function setPosts(\Doctrine\Common\Collections\ArrayCollection \$posts);

    /**
     * For property "posts"
     * @return Doctrine\Common\Collections\ArrayCollection<AppBundle\Entity\Post>
     */
    public function getPosts();

    /**
     * For property "createdAt"
     * @param DateTime \$createdAt
     * @return this
     */
    public function setCreatedAt(\DateTime \$createdAt);

    /**
     * For property "createdAt"
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * For property "updatedAt"
     * @param DateTime \$updatedAt
     * @return this
     */
    public function setUpdatedAt(\DateTime \$updatedAt);

    /**
     * For property "updatedAt"
     * @return DateTime
     */
    public function getUpdatedAt();

    /**
     * For property "lastPost"
     * @param AppBundle\Entity\Post \$lastPost
     * @return this
     */
    public function setLastPost(\AppBundle\Entity\Post \$lastPost);

    /**
     * For property "lastPost"
     * @return AppBundle\Entity\Post
     */
    public function getLastPost();

}

EOT;

    /**
     * @var string
     */
    private $postInterfaceExpected = <<<EOT
<?php

namespace AppBundle\Entity;

/**
 * Interface for entity : \AppBundle\Entity\Post
 */
interface PostInterface
{

    /**
     * For property "content"
     * @param string \$content
     * @return this
     */
    public function setContent(\$content);

    /**
     * For property "content"
     * @return string
     */
    public function getContent();

    /**
     * For property "createdAt"
     * @param DateTime \$createdAt
     * @return this
     */
    public function setCreatedAt(\DateTime \$createdAt);

    /**
     * For property "createdAt"
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * For property "updatedAt"
     * @param DateTime \$updatedAt
     * @return this
     */
    public function setUpdatedAt(\DateTime \$updatedAt);

    /**
     * For property "updatedAt"
     * @return DateTime
     */
    public function getUpdatedAt();

}

EOT;

    /**
     * @var string
     */
    private $userTestClassExpected = <<<EOT
<?php

namespace AppBundle\Tests\Entity;

/**
 * Test for \AppBundle\Entity\User
 */
class UserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Entity to test
     * @var \AppBundle\Entity\UserInterface
     */
    private \$object = null;

    public function setUp()
    {
        \$this->object = new \AppBundle\Entity\User();
    }

    public function testConstructor()
    {
        \$this->assertNotNull(\$this->object);
        \$this->assertInstanceof('\AppBundle\Entity\UserInterface', \$this->object);
        \$this->assertInstanceof('\AppBundle\Entity\User', \$this->object);
        \$this->assertInstanceof('\AppBundle\Entity\Base', \$this->object);
    }

    /**
     * @covers \AppBundle\Entity\User::setUsername
     */
    public function testSetUsername()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::getUsername
     */
    public function testGetUsername()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::setEmail
     */
    public function testSetEmail()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::getEmail
     */
    public function testGetEmail()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::isActive
     */
    public function testIsActive()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::setActive
     */
    public function testSetActive()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::getActive
     */
    public function testGetActive()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::setPosts
     */
    public function testSetPosts()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::getPosts
     */
    public function testGetPosts()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::setLastPost
     */
    public function testSetLastPost()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\User::getLastPost
     */
    public function testGetLastPost()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

}

EOT;

    /**
     * @var string
     */
    private $postTestClassExpected = <<<EOT
<?php

namespace AppBundle\Tests\Entity;

/**
 * Test for \AppBundle\Entity\Post
 */
class PostTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Entity to test
     * @var \AppBundle\Entity\PostInterface
     */
    private \$object = null;

    public function setUp()
    {
        \$this->object = new \AppBundle\Entity\Post();
    }

    public function testConstructor()
    {
        \$this->assertNotNull(\$this->object);
        \$this->assertInstanceof('\AppBundle\Entity\PostInterface', \$this->object);
        \$this->assertInstanceof('\AppBundle\Entity\Post', \$this->object);
    }

    /**
     * @covers \AppBundle\Entity\Post::setContent
     */
    public function testSetContent()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\Post::getContent
     */
    public function testGetContent()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\Post::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\Post::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\Post::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers \AppBundle\Entity\Post::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        \$this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

}

EOT;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var array
     */
    private $classManagers = [];

    /**
     * SET UP
     */
    public function setUp()
    {
        $this->renderer = new Renderer();
    }

    /**
     * @dataProvider dataForTestRender
     */
    public function testRender($item, $expectedOutput)
    {
        $result = $this->renderer->render($item);
        $this->assertEquals($expectedOutput, $result);
    }

    public function dataForTestRender()
    {
        $this->initDataFromYaml();
        return [
            [$this->classManagers[0], $this->userClassExpected],
            [$this->classManagers[1], $this->postClassExpected],
            [$this->classManagers[0]->getInterface(), $this->userInterfaceExpected],
            [$this->classManagers[1]->getInterface(), $this->postInterfaceExpected],
            [$this->classManagers[0]->getTestClass(), $this->userTestClassExpected],
            [$this->classManagers[1]->getTestClass(), $this->postTestClassExpected],
        ];
    }

    public function testRenderAndPutItemsToContent()
    {
        $itemsToRender = new ArrayCollection();
        $itemsToRender->add(Helper::prepareProperty("active", "boolean", "is post active", ["IsTrue()"]));
        $result = $this->renderer->renderAndPutItemsToContent($this->postClassExpected, $itemsToRender, 38);
        $this->assertEquals($this->postClassWithNewPropertyExpected, $result);
    }

    public function testRenderAndPutConstructorBodyToContent()
    {
        $constructorManager = new ClassConstructorManager(new ClassManager());
        $initProperties = new ArrayCollection();
        $initProperty = new InitPropertyManager();
        $initProperty->setProperty(Helper::prepareProperty("collection", "Doctrine\Common\Collections\ArrayCollection", "items collection", ["Valid(message = \"Collection has to be valid!\")"]));
        $initProperty2 = new InitPropertyManager();
        $initProperty2->setProperty(Helper::prepareProperty("collection2", "Doctrine\Common\Collections\ArrayCollection", "items collection 2", ["Valid(message = \"Collection has to be valid!\")"]));
        $initProperties->add($initProperty2);
        $initProperties->add($initProperty);
        $constructorManager->setInitProperties($initProperties);
        $result = $this->renderer->renderAndPutConstructorBodyToContent($this->postClassExpected, $constructorManager, 43);
        $this->assertEquals($this->postClassWithUpdatedConstructor, $result);
    }

    private function initDataFromYaml()
    {
        self::bootKernel();
        $structureGenerator = self::$kernel->getContainer()->get('seg.structure_generator');
        $this->classManagers = $structureGenerator->buildEntitiesClassStructure($structureGenerator->parseToArray(Helper::getStructureYaml()));
    }
}
