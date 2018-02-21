<?php

namespace Gabrielmoura\Laravel-AuthSSL;

use App\Auth\AuthSSL;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class Service
{
    use Dispatchable, InteractsWithQueue, Queueable;
    private $ssl;

    public function __construct(SSL $ssl)
    {
        $this->ssl = $ssl;
    }

    /**
     * Usado para inserir certificado já existente e assinado
     * @param $user_id
     * @param $raw
     * @param array $data [serial,client_issuer_dn,name,email]
     * @return mixed
     */
    protected function insert($user_id, array $data, $raw)
    {
        return AuthSSL::insert([
            'serial' => $data['serial'],
            'client_issuer_dn' => $data['client_issuer_dn'],
            'name' => $data['name'],
            'email' => $data['email'],
            'raw' => $raw,
            'user_id' => $user_id
        ]);
    }

    public function assign($csr, $user)
    {
        $ssl = $this->ssl->certAssign($csr, 0, '365');
        $info = $this->ssl->certInfo($csr);
        $array = $data = [
            'name' => $info->name,
            'serial' => '000',//$this->request->server('SSL_CLIENT_M_SERIAL'),
            'email' => $info->email,
            'client_issuer_dn' => '00000'//$this->request->server('SSL_CLIENT_I_DN')
        ];
        $raw = $this->ssl->exportStringCsr($ssl);
        $this->insert($user->id, $array, $raw);
        return $raw;
    }

    public function revoke()
    {
    }

    public function download()
    {
    }

    public function create()
    {
    }
}