<?php
namespace AnhNhan\ModHub\Modules\User\Providers;

use AnhNhan\ModHub\Modules\User\UserApplication;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider as BaseUserAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

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
     * @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    private $encoder;

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

    public function setPasswordEncoder(PasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
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
        if (!$this->encoder->isPasswordValid($user->getPassword(), $token->getCredentials(), $user->getSalt())) {
            throw new BadCredentialsException("Passwords do not match!");
        }
    }
}
