<?php namespace App\Controllers\Restrito\administracao;

use App\Controllers\BaseController;
use App\Libraries\GroceryCrud;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Campi extends BaseController
{
	public function index()
	{
	    $crud = new GroceryCrud();
	    $crud->setTable('campi');
		$crud->setSubject("Campi");		
	    $output = $crud->render();
		$output->header_page = "Cadastros de Campis";	
		return  view('crud/index', (array)$output);
	}
}