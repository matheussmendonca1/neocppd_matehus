<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Google\Client as Google_Client;
use Google\Service\PeopleService;
use App\Models\UsuarioModel;

class Login extends Controller
{
    private $client;
    private $session;
    private $usuarioModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $ClientId = env('ClientId');
        $ClientSecret = env('ClientSecret');       
        $this->client = new Google_Client();
        $this->client->setClientId($ClientId);
        $this->client->setClientSecret($ClientSecret );
        $this->client->setRedirectUri(base_url('/login/google'));
        $this->client->setScopes(["email", PeopleService::USERINFO_PROFILE]);
        $this->session = session();
        $this->usuarioModel = new UsuarioModel();
    }

    public function site(): string
    {
        $dados["authUrl"] = $this->client->createAuthUrl();
        return view("site/form_login", $dados);
    }

    public function google()
    {  
        if (!isset($_GET['code'])) {
            return redirect()->to('login/site');
        }

        try {
            $code = $_GET['code'];
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($token);
            $payload = $this->client->verifyIdToken();
            $email = $payload['email'];

            // Busca ou cria o usuário e realiza login
            $user = $this->usuarioModel->findOrCreateUser($email, $payload['picture']);
           
            if ($user) {
                $this->_create_session($user);
                return redirect()->to('restrito/administracao/dashboard');
            } else {
                $this->session->setFlashdata('erro', 'Entre em contato com o administrador do catálogo. Servidor novo, ainda não cadastrado!');
                return redirect()->to('login/site');
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }  
    }

    private function _create_session($user)
    {
        $user->papeis = $this->usuarioModel->getPapeis($user->idusuario);
        $idsPapeis = array_map(function($obj) {
            return $obj->idpapel;  
        },  $user->papeis); 

        $usuario = [
            'nome' => $user->nome,
            'email' => $user->email,
            'foto' => $user->foto,
            'idcampi' => $user->campi_idcampi,
            'idservidor' => $user->idservidor,
            'nivel' => $user->nivel,
            'idusuario' => $user->idusuario,
            'papeis' => $user->papeis,
            'idsPapeis' => $idsPapeis, 
            'tem_licenca_uso' => $user->autoriza_uso,
            'logged' => TRUE,
        ];
        $this->session->set("user", $usuario); 
    }

    public function logoff()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }

    public function verificar()
    {
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');
        $usuario = $this->usuarioModel->getUsuarioByEmail($email);
        
        if ($usuario && password_verify($senha, $usuario->senha)) {
            $this->_create_session($usuario);
            return redirect()->to('restrito/administracao/dashboard');
        } else {
            $this->session->setFlashdata('erro', 'Usuário ou senha inválidos');
            return redirect()->to('login/site');
        }
    }
}