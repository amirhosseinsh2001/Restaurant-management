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

    public function update(array $data, int $id)
    {
        $user = $this->findByField("id", $id);
        if (!$user) {
            return null;
        }
        $user->update($data);
        return $user->refresh();
    }

    public function verify(array $data): mixed
    {
        return $this->model->create($data);
    }
}
