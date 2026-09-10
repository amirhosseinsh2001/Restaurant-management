<?php

namespace App\Repositories\Auth;

use App\Exceptions\AuthException;
use App\interfaces\Auth\AuthInterface;
use App\Models\User;
use App\Repositories\Core\CoreRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthRepository extends CoreRepository implements AuthInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $data
     * @param string $field
     * @param string $value
     * @return void
     */
    public function register(array $data, string $field, string $value): void
    {
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function verify(array $data): mixed
    {
        return User::create($data);
    }
}
