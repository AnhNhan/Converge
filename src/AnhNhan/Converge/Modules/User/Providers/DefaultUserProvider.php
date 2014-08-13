<?php
namespace AnhNhan\Converge\Modules\User\Providers;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class DefaultUserProvider implements UserProviderInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    const ENTITY_USER = 'AnhNhan\Converge\Modules\User\Storage';

    public function __construct($appOrEm)
    {
        if ($appOrEm instanceof BaseApplication) {
            $this->em = $appOrEm->getEntityManager();
        } else {
            $this->em = $appOrEm;
        }
    }

    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository(self::ENTITY_USER)->findOneBy(array("username" => $username));
        if (!$user) {
            $e = new UsernameNotFoundException;
            $e->setUsername($username);
            throw $e;
        }
        return $user;
    }

    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException("Not supported: " . get_class($user));
        }
    }

    public function supportsClass($class)
    {
        return $class == self::ENTITY_USER;
    }
}
