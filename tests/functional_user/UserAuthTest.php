<?php
use Codeception\Util\Stub;

use AnhNhan\ModHub\Modules\User\Storage\Role;
use AnhNhan\ModHub\Modules\User\Storage\User;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserAuthTest extends \Codeception\TestCase\Test
{
    const ENTITY_ROLE = 'AnhNhan\ModHub\Modules\User\Storage\Role';
    const ENTITY_USER = 'AnhNhan\ModHub\Modules\User\Storage\User';

   /**
    * @var \UserTestGuy
    */
    protected $userTestGuy;

   /**
    * @var \Symfony\Component\Security\Core\SecurityContext
    */
    private $securityContext;

   /**
    * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
    */
    private $encoderFactory;

    private $providerKey;

    private $username  = "foo";
    private $dispname  = "FooBar";
    private $password  = "bar";
    private $salt;
    private $userRoles = array(
        "ROLE_FOO",
        "ROLE_BAR",
    );

    protected function _before()
    {
        $this->salt = \Filesystem::readRandomCharacters(22);

        $container = \AnhNhan\ModHub\Web\Core::loadBootstrappedSfDIContainer();
        $this->securityContext = $container->get("security.context.default");
        $this->encoderFactory  = $container->get("security.encoder.factory");
        $this->providerKey     = $container->getParameter("security.provider_key");

        $roles = array();
        foreach ($this->userRoles as $roleName) {
            $role = Role::initializeWithName($roleName)
                ->setLabel($roleName . "_LABEL")
            ;
            $this->userTestGuy->persistEntity($role);
            $roles[] = $role;
        }

        $password = $this->encoderFactory
            ->getEncoder(self::ENTITY_USER)
            ->encodePassword($this->password, $this->salt);
        $user = new User($this->username, $this->dispname, $password, $this->salt);
        foreach ($roles as $role) {
            $user->addRole($role);
        }
        $this->userTestGuy->persistEntity($user);
    }

    protected function _after()
    {
    }

    // tests
    public function testSuccessfulLogin()
    {
        $token = new UsernamePasswordToken($this->username, $this->password, $this->providerKey);
        $this->securityContext->setToken($token);

        $this->assertTrue($this->securityContext->isGranted("ROLE_FOO"));
        $this->assertFalse($this->securityContext->isGranted("ROLE_DERP"));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testExceptionOnWrongCredentials()
    {
        $token = new UsernamePasswordToken($this->username, "some password", $this->providerKey);
        $this->securityContext->setToken($token);

        $this->assertTrue($this->securityContext->isGranted("ROLE_FOO"));

        $this->fail();
    }

}
