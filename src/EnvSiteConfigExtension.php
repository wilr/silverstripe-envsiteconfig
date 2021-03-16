<?php

namespace Wilr\EnvSiteConfig;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Environment;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

class EnvSiteConfigExtension extends DataExtension
{
    private static $allowlist;

    private static $db = [
        'Env' => 'Text'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $allowed = Config::inst()->get(__CLASS__, 'allowlist');

        if ($allowed) {
            $env = [];
            foreach ($allowed as $envvar) {
                $key = urlencode($envvar);
                $env[] = TextField::create(
                    'SetEnv['. $key . ']', $envvar, $envvar
                )->setAttribute('placeholder', $this->getRealEnv($envvar));
            }

            $fields->addFieldsToTab('Root.Env', $env);
        }
    }


    public function onBeforeWrite()
    {
        $this->owner->Env = json_encode($this->SetEnv);
    }

    public function appendCustomEnv()
    {
        if (!$this->owner->Env) {
            return;
        }

        $vars = json_decode($this->owner->Env);

        foreach ($vars as $k => $v) {
            Environment::setEnv($k, $v);
        }
    }

    /**
     * Get value of environment variable
     *
     * @param string $name
     * @return mixed Value of the environment variable, or false if not set
     */
    public function getRealEnv($name)
    {
        switch (true) {
            case  is_array($_ENV) && array_key_exists($name, $_ENV):
                return $_ENV[$name];
            case  is_array($_SERVER) && array_key_exists($name, $_SERVER):
                return $_SERVER[$name];
            default:
                return getenv($name);
        }
    }
}
