<?php
namespace AnhNhan\ModHub\Modules\User\Providers;

use AnhNhan\ModHub\Modules\User\UserApplication;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

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
     * @var \Doctrine\ORM\EntityRepository
     */
    private $userRepository;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    private $encoderFactory;

    const USER_ENTITY_TYPE_NAME = "AnhNhan\ModHub\Modules\User\Storage\User";

    public function setUserApplication(UserApplication $userApp)
    {
        $this->setEntityManager($userApp->getEntityManager());
        return $this;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->userRepository = $em->getRepository(self::USER_ENTITY_TYPE_NAME);
        return $this;
    }

    public function setUserRepository(EntityRepository $repo)
    {
        $this->userRepository = $repo;
        return $this;
    }

    public function setPasswordEncoderFactory(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
        return $this;
    }

    protected function retrieveUser($username, UsernamePasswordToken $token)
    {
        if (!$this->userRepository) {
            $this->setUserApplication(new UserApplication);
        }

        $user = $this->userRepository->findOneBy(array("username" => $username));

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
