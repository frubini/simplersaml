<?php namespace SimplerSaml\Services;

use SimpleSAML\Auth\Simple;

class SamlAuth
{
    protected $config;

    protected $sa;

    public function __construct($config, Simple $sa)
    {
        $this->config = $config;
        $this->sa = $sa;
    }

    /**
     * Returns what makes up a user for the application.
     *
     * Should be the only thing needed to override.
     *
     * @return \SimplerSaml\Contracts\User
     */
    public function user()
    {
        $attributes = $this->getAttributes();
        $model = $this->config->get('simplersaml.model', 'SimplerSaml\User');
        /** @var \SimplerSaml\User $user */
        $user = new $model;
        $user->setRaw($attributes)->map($attributes);
        return $user;
    }

    public function getAttribute($key, array $options = [])
    {
        $this->requireAuth($options);

        $attributes = $this->getAttributes();
        if (!isset($attributes[$key][0])) {
            throw new Exception('Attribute not in SAML response');
        }

        return $attributes[$key][0];
    }

    public function requireAuth(array $options = [])
    {
        $this->sa->requireAuth($options);
    }

    public function getAttributes()
    {
        return $this->sa->getAttributes();
    }

    public function isAuthenticated()
    {
        return $this->sa->isAuthenticated();
    }

    public function isGuest()
    {
        return ! $this->sa->isAuthenticated();
    }

    public function logout()
    {
        $this->sa->logout();
    }
}
