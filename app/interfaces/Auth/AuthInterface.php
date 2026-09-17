<?php

namespace App\interfaces\Auth;

interface AuthInterface
{
    public function update(array $data, int $id);
    public function verify(array $data);
}
