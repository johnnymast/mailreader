<?php

namespace JM\MailReader;

use JM\MailReader\Adapters\{
    AdapterInterface, Curl\Curl, Core\Core
};

/**
 * Class MailReader
 *
 * @package JM\MailReader
 */
class MailReader
{
    /**
     * @var AdapterInterface
     */
    protected $adapter = null;

    /**
     * @var \JM\MailReader\Credentials
     */
    protected $credentials = null;

    /**
     * @var bool
     */
    protected $authenticated = false;

    /**
     * MailReader constructor.
     *
     * @param \JM\MailReader\Credentials $credentials
     */
    public function __construct(Credentials $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * When this class is destructed we
     * might want to close the connection.
     */
    public function __destruct()
    {
        $this->adapter->close();
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        if (! $this->adapter) {
            $class = Curl::class;

            if (extension_loaded('ext-imap')) {
                $class = Core::class;
            }
            $class = Core::class; // TODO
            $this->adapter = new $class;
        }

        return $this->adapter;
    }

    /**
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function connect(): bool
    {
        if ($this->credentials->validate()) {
            if ($this->getAdapter()->connect($this->credentials)) {
                $this->authenticated = true;
            } else {
                $this->authenticated = false;
            }
        }

        return $this->authenticated;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->adapter, $name)) {
            return call_user_func_array([$this->adapter, $name], $arguments);
        }
    }
}