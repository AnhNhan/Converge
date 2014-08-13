<?php
namespace Providers;

use Codeception\Util\Stub;
use AnhNhan\Converge\Modules\User\Providers\UserAuthenticationProvider;
use AnhNhan\Converge\Modules\User\Storage\User;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserAuthenticationProviderTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    /**
     * @var UserAuthenticationProvider
     */
    private $authProvider;

    /**
     * @var UserChecker
     */
    private $userChecker;

    /**
     * @var EntityRepository
     */
    private $repository;

    private $token;

    const PROVIDER_KEY = "providerKey";

    protected function _before()
    {
        $this->repository   = $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);
        $this->userChecker  = $this->getMock('Symfony\Component\Security\Core\User\UserChecker');
        $this->authProvider = new UserAuthenticationProvider($this->userChecker, self::PROVIDER_KEY, false);
        $this->token        = new UsernamePasswordToken("foo", "bar", self::PROVIDER_KEY);

        $this->authProvider->setUserRepository($this->repository);
    }

    protected function _after()
    {
        $this->authProvider = null;
        $this->userChecker  = null;
        $this->repository   = null;
        $this->token        = null;
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testAuthenticationFailsForNonExistingUser()
    {
        $this->repository
            ->expects($this->once())
            ->method("findOneBy")
            ->will($this->returnValue(null));

        $this->authProvider->authenticate($this->token);

        $this->fail();
    }

    public function testASuccessfulLoginAttempt()
    {
        $user = $this->getMock('AnhNhan\Converge\Modules\User\Storage\User', array(), array(), '', false);
        // Assuming plain text password
        $user->expects($this->once())->method("getPassword")->will($this->returnValue("bar"));
        $user->expects($this->once())->method("getRoles")->will($this->returnValue(array("ROLE_USER")));

        $this->repository
            ->expects($this->once())
            ->method("findOneBy")
            ->with(array("username" => "foo"))
            ->will($this->returnValue($user));

        $passwordEncoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder');
        $passwordEncoder->expects($this->once())
                        ->method("isPasswordValid")
                        ->with("bar", "bar", null)
                        ->will($this->returnValue(true));
        $encoderFactory = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactory', array(), array(), '', false);
        $encoderFactory->expects($this->once())
                       ->method("getEncoder")
                       ->with($user)
                       ->will($this->returnValue($passwordEncoder));
        $this->authProvider->setPasswordEncoderFactory($encoderFactory);

        $this->assertEquals(false, $this->token->isAuthenticated());
        $newToken = $this->authProvider->authenticate($this->token);

        $this->assertEquals(true, $newToken->isAuthenticated());
        $this->assertSame($user, $newToken->getUser());
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testLoginFailsWithWrongPassword()
    {
        $user = $this->getMock('AnhNhan\Converge\Modules\User\Storage\User', array(), array(), '', false);
        // Assuming plain text password
        $user->expects($this->once())->method("getPassword")->will($this->returnValue("blurp"));

        $this->repository
            ->expects($this->once())
            ->method("findOneBy")
            ->with(array("username" => "foo"))
            ->will($this->returnValue($user));

        $passwordEncoder = $this->getMock('Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder');
        $passwordEncoder->expects($this->once())
                        ->method("isPasswordValid")
                        ->with("blurp", "bar", null) // Wrong password
                        ->will($this->returnValue(false));
        $encoderFactory = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactory', array(), array(), '', false);
        $encoderFactory->expects($this->once())
                       ->method("getEncoder")
                       ->with($user)
                       ->will($this->returnValue($passwordEncoder));
        $this->authProvider->setPasswordEncoderFactory($encoderFactory);

        $this->assertEquals(false, $this->token->isAuthenticated());
        $newToken = $this->authProvider->authenticate($this->token);

        $this->fail();
    }

}
