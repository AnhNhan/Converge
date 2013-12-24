<?php
namespace AnhNhan\ModHub\Modules\User\Providers;

use AnhNhan\ModHub\Modules\User\UserApplication;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider as BaseUserAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class UserAuthenticationProvider extends BaseUserAuthenticationProvider
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $userRepository;

    const USER_ENTITY_TYPE_NAME = "AnhNhan\ModHub\Modules\User\Storage\User";

    public function setUserApplication(UserApplication $userApp)
    {
        $this->setEntityManager($userApp->getEntityManager());
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->userRepository = $em->getRepository(self::USER_ENTITY_TYPE_NAME);
    }

    public function setUserRepository(EntityRepository $repo)
    {
        $this->userRepository = $repo;
    }

    protected function retrieveUser($username, UsernamePasswordToken $token)
    {
        if (!$this->userRepository) {
            $this->setUserApplication(new UserApplication);
        }

        $user = $this->userRepository->findOneBy(array("username" => $username));

        return $user;
    }

    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        // TODO: Check password here
    }
}
