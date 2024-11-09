<?php

namespace App\Controllers;
use App\Libraries\GroceryCrud;

class ExemploGrocery extends BaseController{

    public function index() : string{
        $crud = new GroceryCrud();
        $crud->setTable('menu');
        $crud->setSubject('menu');
        $output = $crud->render();
        return view('crud/main', (array)$output);    
    }

}

