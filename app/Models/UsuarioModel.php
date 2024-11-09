<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuario';
    protected $allowedFields = ['idusuario', 'servidor_idservidor', 'senha', 'nivel', 'autoriza_uso'];

    public function getUsuarioByEmail($email)
    {
        return $this->asObject()
                    ->select('usuario.*, servidor.email, servidor.*')
                    ->join('servidor', 'servidor.idservidor = usuario.servidor_idservidor')
                    ->where('servidor.email', $email)
                    ->first();
    }

    public function findOrCreateUser($email, $picture)
    {
        $servidor = $this->_hasServidor($email);
        if ($servidor && empty($servidor->foto)) {
            $this->_baixar_foto($servidor, $picture);
        }

        $user = $this->_hasUser($email);
        if ($user) {
            return $user;
        } elseif ($servidor) {
            $this->_createUser($servidor);
            return $this->_hasUser($email);
        }
        return null;
    }

    private function _hasUser($email)
    {
        return $this->asObject()->join("servidor", "servidor.idservidor = usuario.servidor_idservidor")
                    ->where("email", $email)
                    ->first();
    }

    private function _hasServidor($email)
    {
        $db = \Config\Database::connect();
        return $db->table("servidor")->where("email", $email)->get()->getRowObject();
    }

    private function _baixar_foto($servidor, $picture)
    {
        $pathFoto = "assets/uploads/user/u_$servidor->idservidor.jpg";
        copy($picture, $pathFoto);
        $db = \Config\Database::connect();
        $db->table("servidor")->where("idservidor", $servidor->idservidor)->update(['foto' => "u_$servidor->idservidor.jpg"]);
    }

    private function _createUser($servidor)
    {
        $dados = [
            "servidor_idservidor" => $servidor->idservidor,
            "nivel" => "Particular",
        ];
        $this->insert($dados);
        $idUser = $this->insertID();
        $this->_criarPapeis($idUser, $servidor->idservidor);
    }

    private function _criarPapeis($idUsuario, $idservidor)
    {
        $db = \Config\Database::connect();
        $papelUsuario = [["papel_idpapel" => 2, "usuario_idusuario" => $idUsuario]];

        if ($this->_isResponsavel($idservidor)) {
            $papelUsuario[] = ["papel_idpapel" => 3, "usuario_idusuario" => $idUsuario];
        }
        $db->table("papel_usuario")->insertBatch($papelUsuario);
    }

    private function _isResponsavel($idservidor)
    {
        $db = \Config\Database::connect();
        return $db->table("laboratorio")->where("responsavel_idservidor", $idservidor)->countAllResults() > 0;
    }

    public function getPapeis($idUsuario)
    {
        return $this->db->table("papel_usuario")
                        ->select("idpapel, papel, is_admin")
                        ->join("papel", "papel.idpapel = papel_usuario.papel_idpapel")
                        ->where("usuario_idusuario", $idUsuario)
                        ->get()
                        ->getResult();
    }
}