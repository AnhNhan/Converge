<?php
namespace AnhNhan\Converge\Modules\People\Providers;

use AnhNhan\Converge\Modules\People\Query\PeopleQuery;
use AnhNhan\Converge\Modules\People\Storage\User;
use AnhNhan\Converge\Modules\People\PeopleApplication;

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
     * @var PeopleQuery
     */
    private $query;

    /**
     * @var EntityManager
     */
    private $entity_manager;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    private $encoderFactory;

    const USER_ENTITY_TYPE_NAME = "AnhNhan\Converge\Modules\People\Storage\User";

    public function setPeopleApplication(PeopleApplication $userApp)
    {
        $this->setEntityManager($userApp->getEntityManager());
        return $this;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->entity_manager = $em;
        $this->query = create_user_query($em);
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

        $canon_name = to_canonical($username);
        $_user  = $this->query->retrieveUsersForCanonicalNames([$canon_name], 1);
        $user = head($_user);

        if (!$user) {
            throw new UsernameNotFoundException("A user with the name '{$username}' could not be found!");
        }
        $this->entity_manager->detach($user);

        return $user;
    }

    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        if (!$this->encoderFactory->getEncoder($user)->isPasswordValid($user->getPassword(), $token->getCredentials(), $user->getSalt())) {
            throw new BadCredentialsException("Passwords do not match!");
        }
    }
}
