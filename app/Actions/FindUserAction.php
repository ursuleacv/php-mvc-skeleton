<?php

declare(strict_types=1);

namespace App\Actions;

class FindUserAction
{
    public function __construct()
    {
        //
    }

    /**
     * @param $email
     */
    public function byEmail($email)
    {
        return [
            'id' => random_int(1, 100),
            'email' => $email,
        ];
    }
}
