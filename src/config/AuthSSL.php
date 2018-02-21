<?php

namespace App\Auth;

use Illuminate\Database\Eloquent\Model;

/**
 * Associa certificado dos clientes aos usuários
 */
class AuthSSL extends Model
{
    //use SSLTrait;
    protected $table = 'auth_ssl';
    protected $connection = 'mysql2';

}
