<?php

namespace JM\MailReader;

class Email
{
    protected $index;

    protected $header;

    protected $structure;

    /**
     * Email constructor.
     *
     * @param $index
     * @param $header
     * @param $structure
     */
    public function __construct($index, $header, $structure)
    {
        $this->index = $index;
        $this->header = $header;
        $this->structure = $structure;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index): void
    {
        $this->index = $index;
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param mixed $header
     */
    public function setHeader($header): void
    {
        $this->header = $header;
    }

    /**
     * @return mixed
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param mixed $structure
     */
    public function setStructure($structure): void
    {
        $this->structure = $structure;
    }
}