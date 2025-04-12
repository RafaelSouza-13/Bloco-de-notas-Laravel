<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(){
        return view('login');
    }

    public function loginSubmit(Request $request){
        $request->validate(
            // roles
            [
                'text_username' => 'required|email',
                'text_password' => 'required|min:6|max:16',
            ],
            // erro messages
            [
                'text_username.required' => "Campo username é obrigatório",
                'text_username.email' => "username deve ser um email válido",
                'text_password.required' => "Campo senha é obrigatório",
                'text_password.min' => "A senha deve ter pelo menos :min caracteres",
                'text_password.max' => "A senha deve ter no maximo :max caracteres",
            ]
        );
        $username = $request->input('text_username');
        $password = $request->input('text_password');
        // teste database
        // try{
        //     DB::connection()->getPdo();
        // }catch(\PDOException $e){
        //     echo "Cone";
        // }
        // check user exist
        $user = User::where('username', $username) 
                    ->where('deleted_at', NULL)
                    ->first();
        if(!$user){
            return redirect()->back()->withInput()->with('loginError', 'Username ou password incorreto');
        }

        //check password is correct
        echo '<pre>';
        if(!password_verify($password, $user->password)){
            return redirect()->back()->withInput()->with('loginError', 'Username ou password incorreto');
        }
        // update last login
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        // login user
        session([
            'user' =>[
                'id' => $user->id,
                'username' => $user->username
            ]
        ]);
        return redirect('/');
    }

    public function logout(){
        session()->forget('user');
        return redirect()->to('/login');
    }
}
