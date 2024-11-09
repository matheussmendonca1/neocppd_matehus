<?php

namespace App\Controllers\Restrito;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Google\Client as Google_Client;
use Google\Service\PeopleService;

use App\Models\UsuarioModel;

class Login extends Controller{

    public $client;
    public $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->client = new Google_Client();
        $this->client->setClientId('188279924105-e5qtf8nfo9r95h20d5vbpg3dibeokjef.apps.googleusercontent.com');
        $this->client->setClientSecret('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');        
        $this->client->setRedirectUri(base_url('/restrito/login/google'));
        $this->client->setScopes(["email", PeopleService::USERINFO_PROFILE]);
        $this->session = session();
    }

    public function site(): string {   
        $dados["authUrl"] = $this->client->createAuthUrl();
        return view("site/form_login", $dados);
    }

    public function google() {
        if (!isset($_GET['code'])) {
            return redirect()->to('restrito/login/site');
        }

        try {
            $code = $_GET['code'];
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($token);
            $payload = $this->client->verifyIdToken();  
            $email = $payload['email'];
            
            $servidor = $this->_hasServidor($email);
            if(!empty($servidor) && empty($servidor->foto)){
                $this->_baixar_foto($servidor, $payload['picture']);
            }
          
            //verifica se existe o usuário criado
            if (!empty($user = $this->_hasUser($email))) {
                //faz o login com esse usuario                
                $this->_create_session($user);
            } else if (!empty($servidor)) {
                //existe um servidor com esse email, então cria um usuario com privilegios basicos                
                $this->_createUser($servidor);
                if (!empty($user2 = $this->_hasUser($email))) {
                    //faz o login com esse usuario                
                    $this->_create_session($user2);
                }
            } else {
                //não servidor não conhecido na base de dados
                $this->session->setFlashdata('erro', 'Entre em contato com o administrador do catálogo. Servidor novo, ainda não cadastrado!');
                return redirect()->to('restrito/login/site');
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    private function _baixar_foto($servidor, $picture){
        //copia a foto do usuário
        $pathPoto = "assets/uploads/user/u_$servidor->idservidor.jpg";
        copy($picture, $pathPoto);        
        $this->db->where("idservidor", $servidor->idservidor);
        $this->db->update("servidor", array('foto' => "u_$servidor->idservidor.jpg"));
    }

    private function _createUser($servidor) {        
        $dados = array(
            "servidor_idservidor" => $servidor->idservidor,
            "nivel" => "Particular",      
        );        
        $this->db->insert("usuario", $dados);
        $idUser =  $this->db->insert_id() ;
                               
        $this->_criarPapeis($idUser, $servidor->idservidor);
    }
    
    private function _criarPapeis($idUsuario, $idservidor ){
        $pesquisador = array("papel_idpapel" => 2, "usuario_idusuario" => $idUsuario);
        if ($this->_isResponsavel($idservidor)){
            $laboratorio = array("papel_idpapel" => 3, "usuario_idusuario" => $idUsuario);
            $this->db->insert_batch("papel_usuario", array($pesquisador, $laboratorio)); 
        }else{
            $this->db->insert("papel_usuario", $pesquisador); 
        }                       
    }

    private function _isResponsavel($idservidor){
        $this->db->where("responsavel_idservidor", $idservidor);        
        return $this->db->get("laboratorio")->num_rows() > 0;
    }
    
    private function _hasUser($email) {
        $this->db->join("servidor", "servidor.idservidor = usuario.servidor_idservidor");      
        $this->db->where("email", $email);
        return $this->db->get("usuario")->row();
    }

    private function _hasServidor($email) {
        $this->db->where("email", $email);
        return $this->db->get("servidor")->row();
    }

    public function logoff() {
        $this->session->unset_userdata("user");
        redirect(site_url(''));
    }

    private function _create_session($user) {
        $user->papeis = $this->_getPapeis($user);
        $usuario = array(
            'nome' => $user->nome,
            'email' => $user->email,
            'foto' => $user->foto,
            'idcampi' => $user->campi_idcampi,
            'idservidor' => $user->idservidor,
            'nivel' => $user->nivel,
            'idusuario' => $user->idusuario,
            'papeis' => $user->papeis,
            'tem_licenca_uso' => $user->autoriza_uso,
            'logged' => TRUE
        );
        $this->session->set_userdata("user", $usuario);
        return redirect()->to('restrito/administracao/dashboard');
    }

   
    
    private function _getPapeis($user){
        $this->db->select("idpapel, papel, is_admin");
        $this->db->join("papel_usuario", "papel_usuario.usuario_idusuario = usuario.idusuario");
        $this->db->join("papel", "papel.idpapel = papel_usuario.papel_idpapel");
        $this->db->where("idusuario", $user->idusuario);
        $query = $this->db->get("usuario");
        return $query->result();
    }    

    
    public function verificar() {
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->getUsuarioByEmail($email);
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $this->_create_session($user);
        } else {
            $this->session->setFlashdata('erro', 'Usuário ou senha inválidos');
            return redirect()->to('restrito/login/site');
        }
    }

}
