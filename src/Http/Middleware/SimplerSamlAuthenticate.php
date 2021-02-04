<?php

namespace SimplerSaml\Http\Middleware;

use Closure;
use SimplerSaml\Services\SamlAuth;

class SimplerSamlAuthenticate
{
    /**
     * @var SamlAuth
     */
    protected $sa;

    public function __construct(SamlAuth $sa)
    {
        $this->sa = $sa;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$this->sa->isAuthenticated()) {
            return redirect()->route('saml.login');
        }
        return $next($request);
    }
}
