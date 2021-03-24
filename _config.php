<?php

use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;

if (Security::database_is_ready()) {
    try {
        $config = SiteConfig::current_site_config();

        if ($config) {
            $config->appendCustomEnv();
        }
    } catch (Exception $e) {
        //
    }
}
