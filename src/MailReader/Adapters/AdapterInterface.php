<?php

namespace JM\MailReader\Adapters;

use JM\MailReader\Email;

interface AdapterInterface
{
    /**
     * Set the current mailbox
     *
     * @param string $mailbox
     * @return bool
     */
    public function setMailbox($mailbox = ''): bool;

    /**
     * Return the name of the current mailbox.
     *
     * @return string
     */
    public function getMailbox(): string;

    /**
     * Return a list of available mailboxes.
     *
     * @return array
     */
    public function getMailboxes(): array;

    /**
     * Return all mailboxes (folders) on the server. This function removes the
     * mailbox prefixes like {server}INBOX.<name here>
     *
     * @return array
     */
    public function getPrettyMailboxes(): array;

    /**
     * Check if a mailbox exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function mailboxExists($name = ''): bool;

    /**
     * Subscribe to a mailbox.
     *
     * @param string $name
     *
     * @return bool
     */
    public function subscribeToMailbox($name = ''): bool;

    /**
     * Create a mailbox (folder)
     *
     * @param string $name
     *
     * @return bool
     */
    public function createMailbox($name = ''): bool;

    /**
     * Read the current mailbox
     *
     * @return array
     */
    public function readMailbox(): array;

    /**
     * Rename a mailbox (folder)
     *
     * @param string $from
     *
     * @param string $to
     *
     * @return bool
     */
    public function renameMailbox($from = '', $to = ''): bool;

    /**
     * Delete a mailbox (folder)
     *
     * @param string $name
     *
     * @return bool
     */
    public function deleteMailbox($name = ''): bool;

    /**
     * @param string $mailbox
     *
     * @return array
     */
    public function getMessages($mailbox = ''): array;

    /**
     * @param string $uid
     *
     * @param string $mailbox
     *
     * @return mixed
     */
    public function getMessage($uid = '', $mailbox = '');

    /**
     * Filter message sent to $to
     *
     * @param string $to
     *
     * @param string $mailbox
     *
     * @return array
     */
    public function filterTo($to = '', $mailbox = ''): array;


    /**
     * @param string $uid
     *
     * @param string $mailbox
     *
     * @return bool
     */
    public function markMessageAsRead($uid = '', $mailbox = ''): bool;

    /**
     * @param string $uid
     *
     * @param string $mailbox
     *
     * @return bool
     */
    public function markMessageAsUnRead($uid = '', $mailbox = ''): bool;

    /**
     * Move a given message at $index to a given mailbox.
     *
     * @param int $uid
     *
     * @param string $to
     *
     * @return bool
     */
    public function moveMessageFromCurrentMailBox($uid = -1, $to = ''): bool;

    /**
     * Delete a given email message. The param $uid
     * is bases of the uid of the email.
     *
     * @param int $uid
     *
     * @param string $mailbox
     *
     * @return bool
     */
    public function deleteMessage($uid = -1, $mailbox = ''): bool;
}