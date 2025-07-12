<?php

namespace App\Shared\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException as SymfonyAuthenticationException;

class AuthenticationException extends SymfonyAuthenticationException
{
    public function __construct(string $message = 'Invalid credentials')
    {
        parent::__construct($message);
    }
}
