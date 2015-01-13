<?php
namespace AnhNhan\Converge\Modules\Log\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table(indexes={
 * })
 */
class Request extends EntityDefinition
{
    /**
     * @Id
     * @Column(type="string")
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="AnhNhan\Converge\Storage\Doctrine\UIDGenerator")
     */
    private $id;

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

    /**
     * @OneToOne(targetEntity="RequestPerformance", inversedBy="request")
     */
    private $perf;

    /**
     * @OneToOne(targetEntity="RequestDetail", inversedBy="request")
     */
    private $detail;

    // Some properties for caching

    /**
     * @Column(type="string")
     * // Q: Is a 128/256 char limit reasonable?
     */
    private $useragent;

    public function __construct() {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public function uid()
    {
        return $this->id;
    }

    public function performance()
    {
        return $this->perf;
    }

    public function getUIDType()
    {
        return 'REQT';
    }
}
