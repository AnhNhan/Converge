<?php
namespace AnhNhan\ModHub\Web\Application;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class HttpPayload extends AbstractPayload
{
    private $httpCode = 200;
    private $httpHeaders = array();

    private $sendHttpHeaders = true;

    final public function setHttpCode($code)
    {
        $this->httpCode = $code;
        return $this;
    }

    final public function getHttpCode()
    {
        return $this->httpCode;
    }

    final public function getHttpCodeText()
    {
        $labels = array(
            200 => "OK",
            304 => "Not modified",
            404 => "Not found",
            500 => "Internal Server Error",
        );

        return idx($labels, $this->getHttpCode());
    }

    final public function setHttpHeader($name, $value = null)
    {
        $this->httpHeaders[$name] = $value;
        return $this;
    }

    final public function getHttpHeaders()
    {
        $headers = array_replace(
            array(
                0 => "HTTP/1.1 " . $this->getHttpCode() . " " . $this->getHttpCodeText(),
                "Content-Type" => $this->getDefaultContentType(),
            ),
            $this->httpHeaders
        ) + $this->httpHeaders;
        return $headers;
    }

    protected function getDefaultContentType()
    {
        return "text/text";
    }

    /**
     * If true, `header()` will be used (bad for testing), else it will be
     * printed out along the body
     */
    final public function sendHttpHeaders($send = true)
    {
        $this->sendHttpHeaders = $send;
        return $this;
    }

    final public function render()
    {
        ob_start();
        $httpBody = $this->renderHttpBody();
        $this->setHttpHeader("Content-Length", strlen($httpBody));

        if ($this->sendHttpHeaders) {
            foreach ($this->getHttpHeaders() as $name => $value) {
                if ($name === 0) {
                    header($value);
                } else {
                    if ($value) {
                        header("$name: $value");
                    } else {
                        header("$name");
                    }
                }
            }
        } else {
            foreach ($this->getHttpHeaders() as $name => $value) {
                if ($name === 0) {
                    echo "$value\n";
                } else {
                    if ($value) {
                        echo "$name: $value\n";
                    } else {
                        echo "$name\n";
                    }
                }
            }
            echo "\n";
        }

        echo $httpBody;
        return ob_get_clean();
    }

    abstract protected function renderHttpBody();
}
