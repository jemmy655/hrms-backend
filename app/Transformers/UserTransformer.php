<?php
/**
 * Created by PhpStorm.
 * User: oshomo.oforomeh
 * Date: 10/07/2016
 * Time: 1:36 PM
 */

namespace App\Transformers;

use App\User;
use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'      => (int) $user->id,
            'name'   => $user->name,
            'email'    => $user->email,
            'links'   => [
                [
                    'rel' => 'self',
                    'uri' => route('api.v1.users.show', ['user' => $user->id])
                ]
            ],
        ];
    }
}