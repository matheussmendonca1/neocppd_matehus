<?php

namespace App\Controllers\Restrito\administracao;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\Menu;

class Dashboard extends Controller {        
    public function site() {	

		$data = array("header_page"=> "Painel de Controle");
		return  view('main/index', $data);	
	}

    public function teste() {
        $model = new Menu();
        echo  $model->getMenuUser(10);
    }
}

