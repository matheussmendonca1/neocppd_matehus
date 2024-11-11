<?php namespace App\Controllers\Restrito\avaliacao;

use App\Controllers\BaseController;
use App\Libraries\GroceryCrud;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


class Requerimentos extends BaseController {

    public function index() {    

        $crud = new GroceryCrud();
        $crud->setTable("requerimento");
        $crud->setSubject("Requerimento");  
        $crud->columns(["servidor_idservidor","carreira_idcarreira","data", "status"]);
        $crud->displayAs("carreira_idcarreira", "Classe/Nível");
        $crud->displayAs("servidor_idservidor", "Servidor");
        
        $crud->unsetEdit();
        $crud->unsetDelete();
        $crud->unsetClone();
        $crud->unsetAdd();

        /*
        if( in_array($crud->getState(),["list", "success","ajax_list"]) ){
            $crud->set_relation("servidor_idservidor", "servidor", "nome");
            $crud->set_relation("carreira_idcarreira", "carreira", "{classe}/{nivel}");
        }

        $crud->callback_column("status", array($this, "_column_status"));
*/
        $form = $crud->render();
        $form->header_page = "Requerimento de Progressão";	
        return  view('crud/index', (array)$form);
        
    }

    public function _column_status($value, $row){
        $status = $this->carreira->getStatus($row->carreira_idcarreira);
        if ($status === 'AGUARDANDO RECEBIMENTO PELO RH'){            
            return 
            '<a href="'.site_url("restrito/avaliacao/requerimentos/abrir_processo/$row->idRequerimento").'" class="btn btn-danger">Rejeitar Requerimento</a>' .
            '<a href="'.site_url("restrito/avaliacao/requerimentos/receber_requerimento/$row->idRequerimento").'" class="btn btn-success">Receber Requerimento</a>';            
        }else if($status === "AGUARDANDO ABERTURA DE PROCESSO"){
            return '<a href="'.site_url("restrito/avaliacao/requerimentos/abrir_processo/$row->idRequerimento").'" class="btn btn-success">Abrir Processo</a>';
        }else if($status === "AVALIAÇÃO INICIADA"){

        }
        return $status;
    }

    public function receber_requerimento($id){
        $this->db->where("idRequerimento", $id);
        $this->db->update("requerimento", array("status"=>"AGUARDANDO ABERTURA DE PROCESSO"));
        redirect(site_url("restrito/avaliacao/requerimentos"));
    }

    public function abrir_processo($id){
        redirect(site_url("restrito/avaliacao/processo/abrir/$id/add"));
    }
}
