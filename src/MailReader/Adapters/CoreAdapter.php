<?php
namespace JM\MailReader\Adapters;

use JM\MailReader\Contracts\AdapterContract;

class CoreAdapter implements AdapterContract
{

    public function getType()
    {
        return 'core';
    }

}