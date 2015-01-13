<?php
namespace AnhNhan\Converge\Modules\Log\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table(indexes={
 * })
 */
class RequestPerformance extends EntityDefinition
{
    /**
     * @Id
     * @Column(type="string")
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="AnhNhan\Converge\Storage\Doctrine\UIDGenerator")
     */
    private $id;

    /**
     * @Column(type="float")
     */
    private $pageTime;

    /**
     * @Column(type="integer")
     */
    private $queryCount;

    /**
     * @Column(type="text")
     */
    private $queryDetails;

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
     * @OneToOne(targetEntity="Request")
     */
    private $request;

    public function __construct() {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public function request()
    {
        return $this->request;
    }

    public function pageTime()
    {
        return $this->pageTime;
    }

    public function queryCount()
    {
        return $this->queryCount;
    }

    public function queryDetails()
    {
        return json_decode($this->queryDetails);
    }

    public function setPageTime($time) {
        if (!is_float($time)) {
            throw new \InvalidArgumentException;
        }
        $this->pageTime = $time;
        return $this;
    }

    public function setSqlFromSqlProfiler(\Doctrine\DBAL\Logging\DebugStack $profiler)
    {
        $queries = $profiler->queries;
        $this->queryCount = count($queries);
        $this->queryDetails = json_encode($queries);
        return $this;
    }

    public function uid()
    {
        return $this->id;
    }

    public function getUIDType()
    {
        return 'REQT-PERF';
    }
}
