<?php

namespace JM\MailReader\Adapters;

use JM\MailReader\Contracts\AdapterContract;

class CustomAdaptor implements AdapterContract
{

    public function getType()
    {
        return 'custom';
    }

}