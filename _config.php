<?php

use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;

if (Security::database_is_ready()) {
    $config = SiteConfig::current_site_config();

    if ($config) {
        $config->appendCustomEnv();
    }
}
