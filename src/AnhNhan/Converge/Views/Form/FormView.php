<?php
namespace AnhNhan\Converge\Views\Form;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\AbstractView;

/**
 * @method mixed getAction()
 * @method mixed getMethod()
 * @method mixed getEncoding()
 * @method mixed setAction($action)
 * @method mixed setMethod($method)
 * @method mixed setEncoding($encoding)
 * @method mixed getTitle()
 * @method mixed setTitle($title)
 * @method mixed getId()
 * @method mixed setId($id)
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class FormView extends AbstractView
{
    private $action   = '';
    private $method   = '';
    private $encoding = '';

    const METHOD_POST = 'POST';
    const METHOD_GET  = 'GET';
    const METHOD_PUT  = 'PUT';

    const ENC_MULTI   = 'multipart/form-data';

    private $is_double_column = true;
    private $title = '';

    private $id = '';

    public function __call($name, $arguments)
    {
        $matches = array();
        $match = preg_match(
            '/^(?P<type>g|s)et(?P<name>.*?)$/',
            $name,
            $matches
        );

        if (!$match) {
            throw new \Exception('Bad Getter/Setter call: \'$name\'');
        }

        $type = strtolower($matches['type']);
        $name = strtolower($matches['name']);
        if ($type == 'g') {
            if (!isset($this->$name)) {
                throw new \Exception("Bad getter call: {$name}");
            }
            return $this->$name;
        } elseif ($type == 's') {
            if (!isset($this->$name)) {
                throw new \Exception("Bad setter call: {$name}");
            }
            $this->$name = array_shift($arguments);
            return $this;
        } else {
            throw new \Exception('Method does not exist!');
        }
    }

    /**
     * Convenience method for setting the right encoding for file uploads.
     *
     * Also disables GET method
     *
     * @return \AnhNhan\Converge\Views\Form\FormView
     */
    public function enableFileUpload()
    {
        $this->encoding = self::ENC_MULTI;
        if (strtoupper($this->method) == self::METHOD_GET) {
            $this->method = self::METHOD_POST;
        }
        return $this;
    }

    public function setDualColumnMode($dual_column = true)
    {
        $this->is_double_column = $dual_column;
        return $this;
    }

    public function render()
    {
        $formTag = Converge\ht('form')
        ->addClass('form')
        ->setId($this->id ?: null);

        if ($this->is_double_column) {
            $formTag->addClass("form-dual-column");
        } else {
            $formTag->addClass("form-single-column");
        }

        if ($this->action) {
            $formTag->addOption('action', $this->action);
        }

        if ($this->encoding) {
            $formTag->addOption('encoding', $this->encoding);
        }

        $method = self::METHOD_POST;
        if ($this->method) {
            $method = $this->method;
        }
        $formTag->addOption('method', $method);

        if ($this->title) {
            $formTag->append(Converge\ht("h2", $this->title));
        }

        foreach ($this->retrieveChilds()->getMarkupData() as $child) {
            $formTag->append($child);
        }

        // TODO: Add CSRF protection
        $formTag->append(id(new Controls\HiddenControl())
            ->setName('__form__')
            ->setValue(1));

        return $formTag;
    }
}
