<?php
/**
 * Created by PhpStorm.
 * User: jmast
 * Date: 28-Nov-17
 * Time: 20:34
 */

namespace JM\MailReader\Adapters;

use JM\MailReader\Credentials;

abstract class AdapterAbstract
{
    /**
     * @var \JM\MailReader\Credentials
     */
    protected $credentials = null;

    /**
     * @var string
     */
    protected $mailbox = 'INBOX';

    /**
     * @return bool
     */
    abstract public function close(): bool;

    /**
     * @param \JM\MailReader\Credentials $credentials
     * @return bool
     */
    abstract public function connect(Credentials $credentials): bool;

    /**
     * @return bool
     */
    abstract protected function authenticate(): bool;
}