<?php
namespace AnhNhan\ModHub\Web\Application;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class JsonPayload extends HttpPayload
{
    private $status = "ok";

    public function setStatus($status = "ok") {
        $this->status = $status;
        return $this;
    }

    protected function renderHttpBody()
    {
        return json_encode(array(
            "payloads" => $this->getPayloadContents(),
            "status"       => $this->status,
        ));
    }

    protected function getDefaultContentType()
    {
        return "application/json";
    }
}
