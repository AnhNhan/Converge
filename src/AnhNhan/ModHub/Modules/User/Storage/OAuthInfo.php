<?php
namespace AnhNhan\ModHub\Modules\User\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
 *
 */
class OAuthInfo extends EntityDefinition
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", unique=true)
     */
    private $uid;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="oauthKeys")
     */
    private $user;

    /**
     * @Column(type="string")
     */
    private $oauthProvider;

    /**
     * @Column(type="string")
     */
    private $oauthUID;

    /**
     * @Column(type="string")
     */
    private $accountURI;

    /**
     * @Column(type="string")
     */
    private $accountName;

    /**
     * @Column(type="string")
     */
    private $token;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $tokenExpires;

    /**
     * @Column(type="string")
     */
    private $tokenScope;

    /**
     * @Column(type="string")
     */
    private $tokenStatus;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $modifiedAt;

    public function getUIDType()
    {
        return "OKEY";
    }
}
