<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menu';
    protected $allowedFields = ['idmenu', 'categoria', 'item', 'ordem'];

    public function getMenusUser($idusuario){

        $menus = $this->_getMenusByUsuario($idusuario);
    
        $MenusUsuario = [];
        foreach($menus as $menu){
            if(!isset($MenusUsuario[$menu['ordem']])){
                $MenusUsuario[$menu['ordem']] = [
                    'categoria' => $menu['categoria'],
                    'controllers' => [],
                    'icone' => $menu['menu_icone']
                ];
            }

            if($menu['mostrar_menu']){
                $MenusUsuario[$menu['ordem']]['controllers'][] = [
                    'name' => $menu['name'],
                    'titulo' => $menu['titulo'],
                    'descricao' => $menu['descricao'],
                    'icone' => $menu['controller_icone']
                ];
            }
        } 
        return $MenusUsuario;
    }

    public function _getMenusByUsuario($idusuario)
    {
        return $this->db->table('menu')
            ->select('menu.*, controller.*, privilegio.*, menu.icone as menu_icone, controller.icone as controller_icone') // Selecionando os campos que você deseja
            ->join('controller', 'controller.menu_idmenu = menu.idmenu')
            ->join('privilegio', 'privilegio.controller_idcontroller = controller.idcontroller')
            ->join('papel', 'papel.idpapel = privilegio.papel_idpapel')
            ->join('papel_usuario', 'papel_usuario.papel_idpapel = papel.idpapel')
            ->where('papel_usuario.usuario_idusuario', $idusuario) // Filtrando pelos IDs de papel (8, 9)
            ->orderBy('menu.ordem', 'ASC')
            ->get() // Executa a consulta
            ->getResultArray(); // Retorna o resultado como array
    }

    
    
}