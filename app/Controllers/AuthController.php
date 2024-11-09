<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\IncomingRequest;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function doLogin()
    {
        $model = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            $session = session();
            $session->set('loggedIn', true);
            $session->set('role', $user['role']);
            return redirect()->to('/dashboard');
        } else {
            return redirect()->to('/login')->with('error', 'Invalid credentials');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
