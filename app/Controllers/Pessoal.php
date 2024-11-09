<?php

namespace App\Controllers;

class Pessoal extends BaseController{

    public function index(): string {
        $title['title'] = 'Ola';
        return view('templates/header', $title);
    }
}