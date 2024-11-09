<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuario';
    protected $allowedFields = ['servidor_idservidor', 'senha', 'nivel', 'autoriza_uso'];

    public function getUsuarioByEmail($email)
    {
        return $this->select('usuario.*, servidor.email')
                    ->join('servidor', 'servidor.idservidor = usuario.servidor_idservidor')
                    ->where('servidor.email', $email)
                    ->first(); 
    }

    
}
