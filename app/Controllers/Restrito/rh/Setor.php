<?php namespace App\Controllers\Restrito\rh;

use App\Controllers\BaseController;
use App\Libraries\GroceryCrud;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Setor extends BaseController{

    public function index($id = null) {        
        $crud = new GroceryCrud();
        if(is_numeric($id)){
            $crud->where("setor.setor_idsetor", $id);
            //$crud->field_type("setor_idsetor", "hidden", $id);
        }else{
            //$crud->field_type("setor_idsetor", "hidden");
            $crud->where("setor.setor_idsetor", null);
        }
        $crud->setTable("setor");
        $crud->setSubject("Setor");
        $crud->columns(["nome", "servidor_idservidor"]);
        $crud->requiredFields(["nome", "servidor_idservidor"]);
        $crud->displayAs("servidor_idservidor", "Chefe");
        $crud->setRelation("servidor_idservidor", "servidor", "nome");
        $crud->unsetRead();
        $crud->unsetClone();
        //$crud->setActionButton("Criar setor interno",null,site_url('restrito/rh/setor/index/'), "fas fa-house-user",null,null);

        $crud->setActionButton('Criar setor interno', 'fas fa-house-user', function ($id) {
            return 'index/' . $id;
        }, true);

        $form = $crud->render();
        $form->header_page = "Cadastro de Setores Administrativos";
        return  view('crud/index', (array)$form);
    }

   

}
