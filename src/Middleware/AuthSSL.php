<?php

namespace Gabrielmoura\Laravel-AuthSSL\Middleware;

use App\Auth\AuthSSL as AuthSSLDB;
use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Se não logado verificar se tem SSL_Client assinado logar-lo.
 * Class AuthSSL
 * @package App\Http\Middleware
 */
class AuthSSL
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * AuthSSL constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {

        $this->request = $request;
    }

    /**
     * Se não logado e certificado existe ver metodo.
     * Se metodo for simple buscar email pelo certificado.
     * Se metodo for outro buscar metodo pelo BD.
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return mixed|void
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::check()) {
            return $next($request);
        } else {
            if ($this->certExist()) {
                return $this->ifExist($request, $next);
            } else {
                return $next($request);
            }
        }
    }

    /**
     * @param $request
     * @param $next
     */
    private function ifExist($request, $next)
    {
        if (config('authssl.method') == 'simple') {
            if ($this->getUserFromCert()) {
                return $next($request);
            } else {
                return abort(401, 'Permissão negada.');
            }
        } elseif (config('authssl.method') == 'db') {
            if ($this->getUserFromDB()) {
                return $next($request);
            } else {
                return abort(401, 'Permissão negada.');
            }
        } else {
            if ($this->getUserFromCache()) {
                return $next($request);
            } else {
                return abort(401, 'Permissão negada.');
            }

        }
    }

    /**
     * @return bool
     */
    private function certExist()
    {
        if ($this->request->server('SSL_CLIENT_M_SERIAL')
            || $this->request->server('SSL_CLIENT_V_END')
            || $this->request->server('SSL_CLIENT_VERIFY') === 'SUCCESS'
            || $this->request->server('SSL_CLIENT_I_DN')
        ) {
            if ($this->request->server('SSL_CLIENT_V_REMAIN') <= 0) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function getUserFromCert()
    {
        $user_id = User::where('email', $this->request->server('SSL_CLIENT_S_DN_Email'))->first()->id;
        if (Auth::loginUsingId($user_id)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function getUserFromDB()
    {
        $user_id = AuthSSLDB::where('serial', $this->request->server('SSL_CLIENT_M_SERIAL'))->first()->user_id;
        if (Auth::loginUsingId($user_id)) {
            return true;
        } else {
            return false;
        }
    }

    private function getUserFromCache()
    {
        Cache::set('');
    }
}