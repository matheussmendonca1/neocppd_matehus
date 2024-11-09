<?php 

namespace App\Libraries;

use App\Models\MenuModel;
use App\Models\MotoboyModel;

class Menu{

    public function getProfileUser(){
        //obtém o email do usuario logado
        $session = session();
        $usuario = $session->get("user");   
        return view('template/admin/rowUserProfile', $usuario);
    }

    public static function getUser(){
        $session = session();
        return $session->get("user"); 
    }

    public function getMenuUser(){

        $model = new MenuModel();
        $idUsuario = session()->get('user')['idusuario'];
        $menu["menus"] = $model->getMenusUser($idUsuario);
        return view('template/admin/itemMenu', $menu);
        
        
    }

    public function menus($id, $userType){
        $links = array(
            "Empresa" => array("nome" => "Meus Dados", "icone" => "far fa-building", "url" => "restrito/empresa/perfil/index/edit/"),
            "Motoboy" => array("nome" => "Meus Dados", "icone" => "fas fa-motorcycle", "url" => "restrito/motoboy/perfil/index/edit/"),
            "Usuario" => array("nome" => "Alterar Senha", "icone" => "fas fa-user", "url" => "restrito/usuario/index"),
            "Entrega" => array("nome" => "Entrega", "icone" => "fas fa-box-open", "url" => "restrito/entrega/index"),
            "Encomenda" => array("nome" => "Solicitar Entrega", "icone" => "fas fa-box-open", "url" => "restrito/empresa/encomenda/index"),
            "Encomenda_Entrega" => array("nome" => "Encomendas disponíveis", "icone" => "fas fa-box", "url" => "restrito/motoboy/encomendas/disponiveis"),
            "Minhas_Entregas" => array("nome" => "Minhas Entregas", "icone" => "fas fa-truck", "url" => "restrito/motoboy/entregas/minhas"),
            "Alterar_Foto" => array("nome" => "Alterar Foto", "icone" => "fas fa-camera", "url" => "restrito/empresa/perfil/index/edit/")
        );
        $MenuUsuario = array(
            "Docente" => array($links["Encomenda_Entrega"],$links["Minhas_Entregas"],  $links["Motoboy"], $links["Usuario"]),
            "Empresa" => array($links["Encomenda"], $links["Empresa"], $links["Usuario"]),
            "Administrador" => $links,
        );
        return $MenuUsuario[$userType];
    }
/*
    public static function isCompleteProfile(){
        $session = session();
        $usuario = $session->get("usuario"); 
        if($usuario["tipo"]=="Motoboy"){
			$model = new MotoboyModel();
        }else if($usuario["tipo"]=="Empresa"){
            $model = new EmpresaModel();
        }

        if($model){
            $perfil = $model->where("usuario_idusuario", $usuario["idusuario"])->first();
			if($perfil){
                return true;
            }
        }
        return false;
    }
    */
}