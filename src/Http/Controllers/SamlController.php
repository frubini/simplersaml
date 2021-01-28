<?php namespace SimplerSaml\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimplerSaml\Events\SamlLogin;
use SimplerSaml\Events\SamlLogout;
use SimplerSaml\Services\SamlAuth;

/**
 * Class SamlController
 *
 * @package SimplerSaml\Http\Controllers
 */
class SamlController extends Controller
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SamlAuth
     */
    protected $samlAuth;

    protected $event;

    /**
     * @param Config $config
     * @param EventDispatcher $event
     * @param SamlAuth $samlAuth
     */
    public function __construct(Config $config, EventDispatcher $event, SamlAuth $samlAuth)
    {
        $this->samlAuth = $samlAuth;
        $this->event = $event;
        $this->config = $config;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        $samlIdp = $this->config->get('simplersaml.idp');
        $this->samlAuth->requireAuth(array(
            'saml:idp' => $samlIdp,
        ));

        // The login succeeded and the execution continues
        $this->event->dispatch(new SamlLogin($this->samlAuth->user()));

        $userSessionRedirect = $this->config->get('simplersaml.userSessionRedirect');

        return redirect()->to($userSessionRedirect)->with('user', $this->samlAuth->user());
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $logoutRedirect = $this->config->get('simplersaml.logoutRedirect') ?: env('APP_URL');

        // Pass through the currently authenticated user to log out from laravel session
        if($request->user()) {
            $this->event->dispatch(new SamlLogout($request->user()));
        }

        if ($this->samlAuth->isAuthenticated()) {
            $returnUrl = url($logoutRedirect);
            $this->samlAuth->logout(
                (filter_var($returnUrl, FILTER_VALIDATE_URL) ? ['ReturnTo' => $returnUrl] : null)
            );
        }
        // If  not authenticated redirect directly
        return redirect()->to($logoutRedirect);
    }
}
