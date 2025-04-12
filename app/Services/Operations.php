<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
class Operations{
    // checa se o id pode ser decriptografado
    public static function decryptId($value){
        try {
            $value = Crypt::decrypt($value);
        } catch (DecryptException $e) {
            return redirect()->route('home');
        }
        return $value;
    }
}