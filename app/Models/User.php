<?php

namespace App\Models;

use App\Models\Elegant;
use Respect\Validation\Validator as v;

class User extends Elegant
{
    protected $table = 'users';

    public function photo()
    {
        return $this->hasOne('App\Models\Photos');
    }

    public function rules()
    {
        return  [
            'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'name' => v::notEmpty()->alpha(),
            'password' => v::alnum()->noWhitespace()->length(4, 16),
        ];
    }

    public function add($request)
    {
        $data = (object)$request->getParams();
        $user = new User;
        $user->name = trim($data->name);
        $user->email = trim($data->email);
        $user->password = password_hash(trim($data->password), PASSWORD_DEFAULT);
        $user->created_at = new \DateTime('now');
        $user->updated_at = null;
        $user->save();

        $_SESSION['user'] = $user->id;
    }
}