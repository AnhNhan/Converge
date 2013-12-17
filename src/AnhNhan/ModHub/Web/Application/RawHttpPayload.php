<?php
namespace AnhNhan\ModHub\Web\Application;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class RawHttpPayload extends HttpPayload
{
    protected function renderHttpBody()
    {
        return $this->getPayloadContents();
    }
}
