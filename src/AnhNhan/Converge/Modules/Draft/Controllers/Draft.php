<?php
namespace AnhNhan\Converge\Modules\Draft\Controllers;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Draft\Storage\DraftObject;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\JsonPayload;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class Draft extends DraftController
{
    public function requiredUserRoles($request)
    {
        return [
            'ROLE_USER',
        ];
    }

    public function handle()
    {
        $request = $this->request;
        $request_method = $request->getMethod();
        $query = $this->buildQuery();
        $em = $this->app->getEntityManager();

        $user_uid = $request->request->get('user-id');
        $object_uid = $request->request->get('object-id');

        $draft_object = $query->retrieveDraftObject($user_uid, $object_uid);

        switch ($request_method)
        {
            case 'GET':
                // Just doing checks
                // fallthrough
            case 'PUT':
                if (!$draft_object)
                {
                    return (new ResponseHtml404)->setText('Draft object not found');
                }

                if ($request_method == 'GET')
                {
                    break;
                }
                // fallthrough
            case 'POST':
                if (!$request->request->has('contents'))
                {
                    throw new \Excpetion('Requiring contents parameter.');
                }

                if (!$draft_object)
                {
                    $draft_object = new DraftObject;
                }
                $draft_object->user_uid = $user_uid;
                $draft_object->object_uid = $object_uid;
                $draft_object->contents = $request->request->get('contents');
                $draft_object->updateModifiedAt();
                $em->persist($draft_object);
                $em->flush();
                break;
            case 'DELETE':
                if (!$draft_object)
                {
                    return (new ResponseHtml404)->setText('Draft object not found');
                }

                $em->remove($draft_object);
                $em->flush();
                $payload = new JsonPayload();
                return $payload;
                break;
            default:
                throw new \Exception('Unsupported method');
        }

        $payload = new JsonPayload();
        $payload->setPayloadContents([
            'user_uid' => $user_uid,
            'object_uid' => $object_uid,
            'contents' => $draft_object->contents,
            'created_at' => $draft_object->createdAt->getTimestamp(),
            'modified_at' => $draft_object->modifiedAt->getTimestamp(),
        ]);
        return $payload;
    }
}
