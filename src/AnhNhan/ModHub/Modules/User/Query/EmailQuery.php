<?php
namespace AnhNhan\ModHub\Modules\User\Query;

use AnhNhan\ModHub\Storage\Query;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class EmailQuery extends Query
{
    const ENTITY_EMAIL = 'AnhNhan\ModHub\Modules\User\Storage\Email';

    /**
     * @return \AnhNhan\ModHub\Modules\User\Storage\Email
     */
    public function retrieveEmail($id)
    {
        return $this
            ->repository(self::ENTITY_EMAIL)
            ->find($id)
        ;
    }

    public function retrieveEmailsForIDs(array $ids, $limit = null, $offset = null)
    {
        $emailRepo = $this->repository(self::ENTITY_EMAIL);

        $emails = $emailRepo->findBy(array("id" => $ids), array("email" => "ASC"), $limit, $offset);
        return $emails;
    }

    public function retrieveEmails($limit = null, $offset = null)
    {
        $emailRepo = $this->repository(self::ENTITY_EMAIL);

        $emails = $emailRepo->findBy(array(), array("email" => "ASC"), $limit = null, $offset = null);
        return $emails;
    }

    public function retrieveEmailsForNames(array $names, $limit = null, $offset = null)
    {
        $emailRepo = $this->repository(self::ENTITY_EMAIL);

        $emails = $emailRepo->findBy(array("email" => $names), array("email" => "ASC"), $limit, $offset);
        return $emails;
    }
}
