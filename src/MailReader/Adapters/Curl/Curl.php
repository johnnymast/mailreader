<?php

namespace JM\MailReader\Adapters\Curl;

use JM\MailReader\Adapters\AdapterAbstract;
use JM\MailReader\Adapters\AdapterInterface;
use JM\MailReader\Credentials;

class Curl extends AdapterAbstract implements AdapterInterface
{
    /**
     * @param \JM\MailReader\Credentials $credentials
     * @return bool
     */
    public function connect(Credentials $credentials): bool
    {
        $this->credentials = $credentials;

        return $this->authenticate();
    }

    public function close(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function authenticate(): bool
    {
        // TODO: Implement authenticate() method.
        return false;
    }

    /**
     * @param string $mailbox
     * @return bool
     */
    public function setMailbox($mailbox = ''): bool
    {
        // TODO: Implement setMailbox() method.
    }

    /**
     * Return the name of the current mailbox.
     *
     * @return string
     */
    public function getMailbox(): string
    {
        return '';
    }

    /**
     * Return a list of available mailboxes.
     *
     * @return array
     */
    public function getMailboxes(): array
    {
        // TODO: Implement getMailboxes() method.
    }

    /**
     * Check if a mailbox exists.
     *
     * @param string $name
     * @return bool
     */
    public function mailboxExists($name = ''): bool
    {
        return false;
    }

    /**
     * Read the current mailbox
     *
     * @return array
     */
    public function readMailbox(): array
    {
        return [];
    }
}