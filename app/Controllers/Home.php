<?php

namespace App\Controllers;

class Home extends BaseController
{
       public function site(): string
    {
        return view("site/home");
    }

    public function documentos() : string{
        return view('site/documentos');
    }

    public function tabelas() : string {
        return view('site/tabelas');
    }

    public function progressao() : string {
        return view('site/servicos/progressao');
    }

    public function afastamento() : string {
        return view('site/servicos/afastamento');
    }

    public function normativa() : string {
        return view('site/servicos/normativa');
    }

    public function rsc() : string {
        return view('site/servicos/rsc');
    }

    public function login() {
        return redirect()->to('login/site'); 
     }
    
}
