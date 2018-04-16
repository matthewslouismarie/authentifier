<?php

namespace LM\Authentifier\Factory;

use Firehed\U2F\Registration;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\IAuthenticationCallback;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Enum\Scalar;
use LM\Common\Model\IntegerObject;
use LM\Common\Model\ArrayObject;
use LM\Common\Model\StringObject;

class AuthenticationProcessFactory
{
    public function createProcess(
        array $challenges,
        IAuthenticationCallback $callback = null,
        ?string $username = null): AuthenticationProcess
    {
        $dataArray = [
            'used_u2f_key_public_keys' => new ArrayObject([], Scalar::_STR),
            'challenges' => new ArrayObject($challenges, Scalar::_STR),
            'max_n_failed_attempts' => new IntegerObject(3),
            'n_failed_attempts' => new IntegerObject(0),
            'callback' => $callback,
            'status' => new Status(Status::ONGOING),
            'u2f_registrations' => new ArrayObject([], Registration::class),
        ];
        if (null !== $username) {
            $dataArray['username'] = new StringObject($username);
        }
        $typedMap = new TypedMap($dataArray);

        return new AuthenticationProcess($typedMap);
    }
}
