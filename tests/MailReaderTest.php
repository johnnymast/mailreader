<?php

namespace JM\MailReader\Tests;

use JM\MailReader\MailReader;

class MailReaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Exception
     */
    public function testConnectWillThrowOnError()
    {
        $reader = new MailReader();

        $reader->connect([
            'server'   => 'foobar.com',
            'username' => 'foo@bar.com',
            'password' => 'bar'
        ]);
    }

}
