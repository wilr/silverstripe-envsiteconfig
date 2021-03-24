<?php

namespace Wilr\EnvSiteConfig;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Environment;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

class EnvSiteConfigExtension extends DataExtension
{
    /**
     * @config
     *
     * @var string[]
     */
    private static $allowlist = [];

    /**
     * @config
     *
     */
    private static $allowlist_descriptions = [];

    private static $db = [
        'Env' => 'Text'
    ];

    /**
     * @var array
     */
    private $overridden_envvars = [];

    public function updateCMSFields(FieldList $fields)
    {
        $allowed = Config::inst()->get(__CLASS__, 'allowlist');
        $descriptions = Config::inst()->get(__CLASS__, 'allowlist_descriptions');

        if ($allowed) {
            $env = [];
            foreach ($allowed as $envvar) {
                $desc = (isset($descriptions[$envvar])) ? $descriptions[$envvar] : '';

                $env[] = TextField::create(
                   $this->owner->generateEnvVarKey($envvar),
                   $envvar,
                   $this->owner->getCustomEnvVar($envvar)
                )
                    ->setAttribute('placeholder', $this->owner->getOriginalEnvVar($envvar))
                    ->setDescription($desc);
            }

            $fields->addFieldsToTab('Root.Environment', $env);
        }
    }

    public function onBeforeWrite()
    {
        $vars = [];

        $allowed = Config::inst()->get(__CLASS__, 'allowlist');
        $write = false;

        if ($allowed) {
            foreach ($allowed as $envvar) {
                $key = $this->owner->generateEnvVarKey($envvar);

                if (isset($this->owner->$key)) {
                    $write = true;

                    $vars[$envvar] = $this->owner->$key;
                }
            }
        } else {
            $write = true;
        }

        if ($write) {
            $this->owner->Env = json_encode($vars);
        }
    }

    public function generateEnvVarKey($envvar)
    {
        return 'SetEnv['. $envvar .']';
    }

    public function setCustomEnvVar($envvar, $value)
    {
        $key = $this->generateEnvVarKey($envvar);

        $this->owner->$key = $value;
    }

    public function appendCustomEnv()
    {
        if (!$this->owner->Env) {
            return;
        }

        $vars = json_decode($this->owner->Env, true);

        if ($vars) {
            foreach ($vars as $k => $v) {
                $current = Environment::getEnv($k);

                if ($current != $v) {
                    $this->overridden_envvars[$k] = Environment::getEnv($k);

                    Environment::setEnv($k, $v);
                }
            }
        }
    }

    /**
     * @param string
     *
     * @return mixed
     */
    public function getOriginalEnvVar(string $envvar)
    {
        if (isset($this->overridden_envvars[$envvar])) {
            return $this->overridden_envvars[$envvar];
        }

        return Environment::getEnv($envvar);
    }

    /**
     * @param string
     *
     * @return mixed
     */
    public function getCustomEnvVar(string $envvar)
    {
        $vars = json_decode($this->owner->Env, true);

        if (isset($vars[$envvar])) {
            if (isset($this->overridden_envvars[$envvar])) {
                return $vars[$envvar];
            }
        }

        return '';
    }
}
