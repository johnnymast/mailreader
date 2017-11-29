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

    public function setMailbox($mailbox = ''): bool
    {
        // TODO: Implement setMailbox() method.
    }

    public function getMailbox(): string
    {
        // TODO: Implement getMailbox() method.
    }

    public function getMailboxes(): array
    {
        // TODO: Implement getMailboxes() method.
    }

    public function getPrettyMailboxes(): array
    {
        // TODO: Implement getPrettyMailboxes() method.
    }

    public function mailboxExists($name = ''): bool
    {
        // TODO: Implement mailboxExists() method.
    }

    public function subscribeToMailbox($name = ''): bool
    {
        // TODO: Implement subscribeToMailbox() method.
    }

    public function createMailbox($name = ''): bool
    {
        // TODO: Implement createMailbox() method.
    }

    public function readMailbox(): array
    {
        // TODO: Implement readMailbox() method.
    }

    public function renameMailbox($from = '', $to = ''): bool
    {
        // TODO: Implement renameMailbox() method.
    }

    public function deleteMailbox($name = ''): bool
    {
        // TODO: Implement deleteMailbox() method.
    }

    public function getMessages($mailbox = ''): array
    {
        // TODO: Implement getMessages() method.
    }

    public function getMessage($uid = '', $mailbox = '')
    {
        // TODO: Implement getMessage() method.
    }

    public function filterTo($to = '', $mailbox = ''): array
    {
        // TODO: Implement filterTo() method.
    }

    public function markMessageAsRead($uid = '', $mailbox = ''): bool
    {
        // TODO: Implement markMessageAsRead() method.
    }

    public function markMessageAsUnRead($uid = '', $mailbox = ''): bool
    {
        // TODO: Implement markMessageAsUnRead() method.
    }

    public function moveMessageFromCurrentMailBox($uid = -1, $to = ''): bool
    {
        // TODO: Implement moveMessageFromCurrentMailBox() method.
    }

    public function deleteMessage($uid = -1, $mailbox = ''): bool
    {
        // TODO: Implement deleteMessage() method.
    }
}