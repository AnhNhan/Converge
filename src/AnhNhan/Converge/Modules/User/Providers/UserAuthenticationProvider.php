<?php
namespace AnhNhan\Converge\Modules\User\Providers;

use AnhNhan\Converge\Modules\User\Query\UserQuery;
use AnhNhan\Converge\Modules\User\Storage\User;
use AnhNhan\Converge\Modules\User\UserApplication;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider as BaseUserAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class UserAuthenticationProvider extends BaseUserAuthenticationProvider
{
    /**
     * @var UserQuery
     */
    private $query;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    private $encoderFactory;

    const USER_ENTITY_TYPE_NAME = "AnhNhan\Converge\Modules\User\Storage\User";

    public function setUserApplication(UserApplication $userApp)
    {
        $this->setEntityManager($userApp->getEntityManager());
        return $this;
    }

    public function setEntityManager(EntityManager $em)
    {
        return $this->setQuery(new UserQuery($em));
    }

    public function setQuery(UserQuery $query)
    {
        $this->query = $query;
        return $this;
    }

    public function setPasswordEncoderFactory(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
        return $this;
    }

    protected function retrieveUser($username, UsernamePasswordToken $token)
    {
        if (!$this->query) {
            new \LogicException("Can't continue without an query object.");
        }

        $canon_name = User::to_canonical($username);
        $_user  = $this->query->retrieveUsersForCanonicalNames([$canon_name], 1);
        $user = idx($_user, 0);

        if ($user === null) {
            throw new UsernameNotFoundException("A user with the name '{$username}' could not be found!");
        }

        return $user;
    }

    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        if (!$this->encoderFactory->getEncoder($user)->isPasswordValid($user->getPassword(), $token->getCredentials(), $user->getSalt())) {
            throw new BadCredentialsException("Passwords do not match!");
        }
    }
}
