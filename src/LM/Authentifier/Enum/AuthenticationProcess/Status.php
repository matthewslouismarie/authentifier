<?php

declare(strict_types=1);

namespace LM\Authentifier\Enum\AuthenticationProcess;

use LM\Common\Enum\AbstractEnum;

class Status extends AbstractEnum
{
    const ONGOING = "ONGOING";

    const SUCCEEDED = "SUCCEEDED";

    const FAILED = "FAILED";
}
