<?php

namespace JM\MailReader;

class MailReader
{

    /**
     * @var array
     */
    private $settings = ['server' => '', 'username' => '', 'password' => ''];

    /**
     * @var array
     */
    private $messages = [];

    /**
     * @var string
     */
    private $mailbox = 'INBOX';

    /**
     * @var resource
     */
    private $conn;


    /**
     * When this class is destructed we
     * might want to close the connection.
     */
    public function __destruct()
    {
        $this->close();
    }


    /**
     * Open a connection to a mail server.
     *
     * @param array $credentials
     *
     * @return bool
     * @throws \Exception
     */
    public function connect($credentials = [])
    {

        if ($diff = array_diff(array_keys($this->settings), array_keys($credentials))) {
            throw new \Exception("Missing credentials, the following fields are missing ".implode('/', $diff));
        }

        $this->settings = array_merge($this->settings, $credentials);

        if (isset($this->settings['port']) === false) {
            $this->settings['port'] = 143;
        }

        $this->conn = @imap_open('{'.$this->settings['server'].':'.$this->settings['port'].'/notls}',
            $this->settings['username'], $this->settings['password']);

        if ($this->conn == false) {
            throw new \Exception("Could not connect or authorize to ".$this->settings['server'].':'.$this->settings['port']);
        }

        return ($this->conn != null);
    }


    /**
     * Close the connection to the mail server.
     */
    private function close()
    {
        if ($this->conn) {
            imap_close($this->conn);
        }
    }


    /**
     * @param string $mailbox
     *
     * @return bool
     */
    public function setMailbox($mailbox = 'INBOX')
    {

        if ($mailbox == 'INBOX') {
            $mailbox = "{".$this->settings['server']."}INBOX";
        } else {
            $mailbox = "{".$this->settings['server']."}INBOX.".imap_utf7_encode($mailbox);
        }

        $result = false;

        if ($this->conn) {
            $result = imap_reopen($this->conn, $mailbox);
            $this->mailbox = $mailbox;
        }

        return $result;
    }


    /**
     * Return the name of the current mailbox.
     *
     * @return string
     */
    public function getMailbox()
    {
        $mailbox = str_replace("{".$this->settings['server']."}", '', $this->mailbox);
        $mailbox = (substr($mailbox, 0, 6) == 'INBOX.') ? substr($mailbox, -6) : $mailbox;

        return $mailbox;
    }


    /**
     * Mark a given message as read.
     *
     * @param string $index
     *
     * @return bool
     */
    public function markMessageAsRead($index = '')
    {
        return imap_setflag_full($this->conn, $index, "\\Seen");
    }


    /**
     * Filter unread message sent to $to
     *
     * @param string $to
     *
     * @return array
     */
    public function filterUnReadMessagesTo($to = '')
    {

        $filteredResult = $this->filterTo($to);
        $filteredMessages = [];

        if (is_array($filteredResult) && count($filteredResult) > 0) {
            foreach ($filteredResult as $message) {
                $header = $message['header'];
                if ($header->Unseen == 'U') {
                    $filteredMessages[] = $message;
                }
            }
        }

        return $filteredMessages;
    }


    /**
     * Filter message sent to $to
     *
     * @param string $to
     *
     * @return array
     */
    public function filterTo($to = '')
    {
        $msg_cnt = imap_num_msg($this->conn);
        if ($msg_cnt > 0 && count($this->messages) == 0) {
            $this->readMailbox();
        }

        $filteredResult = imap_search($this->conn, 'TO "'.$to.'"');
        $filteredMessages = [];

        if (is_array($filteredResult) && count($filteredResult) > 0) {
            foreach ($filteredResult as $index) {
                $filteredMessages[] = $this->getMessage($index - 1);
            }
        }

        return $filteredMessages;
    }


    /**
     * Create a mailbox (folder)
     *
     * @param string $name
     *
     * @return bool
     */
    public function createMailbox($name = '')
    {

        if (empty($name)) {
            return false;
        }

        $name = imap_utf7_encode($name);

        return imap_createmailbox($this->conn, "{".$this->settings['server']."}INBOX.".$name);
    }


    /**
     * Remove a mailbox (folder)
     *
     * @param string $name
     *
     * @return bool
     */
    public function removeMailbox($name = '')
    {
        if (empty($name)) {
            return false;
        }

        $name = imap_utf7_encode($name);

        return imap_deletemailbox($this->conn, "{".$this->settings['server']."}INBOX.".$name);
    }


    /**
     * Check to see if a given mailbox (folder) exists on the server.
     *
     * @param string $name
     *
     * @return bool
     */
    public function mailboxExists($name = '')
    {
        if (empty($name)) {
            return false;
        }

        $name = imap_utf7_encode($name);
        $prefixedName = "{".$this->settings['server']."}INBOX.".$name;
        $mailboxes = imap_list($this->conn, "{".$this->settings['server']."}", "*");

        return in_array($prefixedName, $mailboxes);
    }


    /**
     * Return all mailboxes (folders) on the server.
     *
     * @return array
     */
    public function getMailboxes()
    {
        return imap_list($this->conn, "{".$this->settings['server']."}", "*");
    }


    /**
     * Return all mailboxes (folders) on the server. This function removes the
     * mailbox prefixes like {server}INBOX.<name here>
     *
     * @return array
     */
    public function getPrettyMailboxes()
    {
        $mailboxes = $this->getMailboxes();
        $result = [];
        if (is_array($mailboxes) && count($mailboxes) > 0) {
            $prefixedMailbox = "INBOX.";
            foreach ($mailboxes as $mailbox) {
                // Remove the server prefix
                $mailbox = str_replace('{'.$this->settings['server'].'}', '', $mailbox);

                // Remove the INBOX. prefix (if present)
                $mailbox = str_replace($prefixedMailbox, '', $mailbox);

                $result[] = $mailbox;
            }
        }

        return $result;
    }


    /**
     * Rename a mailbox (folder)
     *
     * @param string $from
     * @param string $to
     *
     * @return bool
     */
    public function renameMailbox($from = '', $to = '')
    {
        if (empty($from) || empty($to)) {
            return false;
        }

        $from = imap_utf7_encode($from);
        $to = imap_utf7_encode($to);

        return imap_renamemailbox($this->conn, "{".$this->settings['server']."}INBOX.".$from,
            "{".$this->settings['server']."}INBOX.".$to);
    }


    /**
     * Move a given message at $index to a given mailbox (folder).
     *
     * @param int    $index
     * @param string $to
     *
     * @return bool
     */
    public function moveMessage($index = -1, $to = '')
    {
        if (empty($to)) {
            return false;
        }

        if ($index >= 0) {
            $to = imap_utf7_encode($to);

            imap_mail_move($this->conn, $index, "INBOX.".$to);
            imap_expunge($this->conn);
        }

        return false;
    }


    /**
     * Delete a given email message. The param $index
     * is bases of the [index] field on an email array.
     *
     * @param int $index
     *
     * @return bool
     */
    public function deleteMessage($index = -1)
    {
        if ($index == -1) {
            return false;
        }

        $result = imap_delete($this->conn, $index);
        if ($result) {
            // Real delete emails marked as deleted
            imap_expunge($this->conn);
        }
        return $result;
    }


    /**
     * Return a message based on its index in the mailbox.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function getMessage($id = '')
    {
        $message = $this->messages[$id];
        if (is_array($message) === true) {
            // Get the message body but do not mark as read
            $message['body'] = imap_body($this->conn, $message['index'], FT_PEEK);
        }

        return $message;
    }


    /**
     * Retrieve a list of message in the mailbox.
     *
     * @return array
     */
    public function readMailbox()
    {
        $msg_cnt = imap_num_msg($this->conn);

        $messages = [];
        for ($i = 1; $i <= $msg_cnt; $i++) {
            $messages[] = [
                'index'     => $i,
                'header'    => imap_headerinfo($this->conn, $i),
                'structure' => imap_fetchstructure($this->conn, $i)
            ];
        }
        $this->messages = $messages;

        return $this->messages;
    }
}