<?php

namespace SimplerSaml\Services;

use SimpleSAML\Auth\Simple;

class SamlAuth
{
    protected $config;

    protected $authSimple;

    public function __construct($config, Simple $authSimple)
    {
        $this->config = $config;
        $this->authSimple = $authSimple;
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

    /**
     * @param $key
     * @param array $options
     * @return mixed
     */
    public function getAttribute($key, array $options = [])
    {
        $this->requireAuth($options);

        $attributes = $this->getAttributes();
        if (!isset($attributes[$key][0])) {
            throw new Exception('Attribute not in SAML response');
        }

        return $attributes[$key][0];
    }

    /**
     * @param array $options
     */
    public function requireAuth(array $options = [])
    {
        $this->authSimple->requireAuth($options);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->authSimple->getAttributes();
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->authSimple->isAuthenticated();
    }

    /**
     * @return bool
     */
    public function isGuest()
    {
        return ! $this->authSimple->isAuthenticated();
    }

    /**
     * @param null $params
     */
    public function logout($params = null)
    {
        $this->authSimple->logout($params);
    }
}
