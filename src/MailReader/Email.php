<?php

namespace JM\MailReader;

/**
 * Class Email
 *
 * @package JM\MailReader
 */
class Email
{
    /**
     * @var array
     */
    private $fields = [];

    /**
     * Email constructor.
     *
     * @param array $fields
     */
    public function __construct($fields = [])
    {

        $this->fields = [
            'subject' => '',
            'from' => '',
            'to' => '',
            'date' => '',
            'message_id' => '',
            'size' => 0,
            'msgno' => 0,
            'recent' => 0,
            'flagged' => 0,
            'answered' => 0,
            'deleted' => 0,
            'seen' => 0,
            'draft' => 0,
            'udate' => 0,
            'body' => '',
        ];


        if (is_object($fields)) {
            $fields = (array)$fields;
        }

        if (is_array($fields) == true) {
            $this->fields = array_merge($this->fields, $fields);
        }
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    function __call($name, $arguments)
    {
        if (isset($this->fields[$name]) == true) {
            return $this->fields[$name];
        }
        return null;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->fields[$name]) == true) {
            return $this->fields[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return null
     */
    public function __set($name, $value)
    {
        if (isset($this->fields[$name]) == true) {
            $this->fields[$name] = $value;
        }
        return null;
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return $this->fields;
    }
}