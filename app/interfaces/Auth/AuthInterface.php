<?php

namespace App\interfaces\Auth;

interface AuthInterface
{
    public function register(array $data, string $field, string $value);
    public function verify(array $data);
}
