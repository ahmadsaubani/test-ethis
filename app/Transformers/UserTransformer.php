<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\UserWallet;
use App\Transformers\Log\LogActivityTransformer;
use App\Transformers\UserWallet\UserWalletTransformer;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public $type = 'user';
    
    protected $availableIncludes = [];

    public function transform(User $model)
    {
        return [
            "id"    => $model->id,
            "name"  => $model->name,
            "email" => $model->email
        ];
    }
}