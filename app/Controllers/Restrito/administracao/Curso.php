<?php

use App\Controllers\BaseController;
use App\Libraries\GroceryCrud;

defined('BASEPATH') OR exit('No direct script access allowed');

class Curso extends BaseController {

    public function __construct() {
        parent::__construct();
        $usuario = $this->session->userdata("user");
        if (!isset($usuario)) {
            redirect(site_url('restrito/login'));
        }
    }

    public function index() {
        $crud = new GroceryCrud();
        $crud->setTable("curso");
        $crud->setSubject("Cursos");
        $crud->columns("nome", "tipo");
        $crud->unsetClone();
        $form = $crud->render();
        //envio de dados para template
        $data = [
            'breadcrumb1' => 'Cadastros',
            'breadcrumb2' => 'Curso',
            'titulo' => 'Cadastro de Curso',
            'form' => $form
        ];

        // Carregando a view do template restrito
        // echo view('template/restrito', $data);

        // Carregando a view do CRUD
        // echo view('crud/index', $data);
    }

}
