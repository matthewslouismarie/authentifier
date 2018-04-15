<?php

namespace LM\Authentifier\Enum\Persistence;

use LM\Common\Enum\AbstractEnum;

class Operation extends AbstractEnum
{
    const CREATE = "CREATE";

    const DELETE = "DELETE";

    const UPDATE = "UPDATE";
}
