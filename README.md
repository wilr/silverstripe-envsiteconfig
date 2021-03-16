# silverstripe-envsiteconfig

View, set &amp; override env variables using SiteConfig.

This is not a recommended module for every website, but in some cases (i.e CWP) developers are unable to view or modify
environment variables easily – a pain when it comes to modules which require certain ENV variables to be defined
(such as `silverstripe-algolia`).

Given the alternatives (hard-coding API keys) this module provides a half way solution, allowing website developers to
edit and view environment variables via the built in `SiteConfig` settings tab.

## Installation

```
composer require wilr/silverstripe-envsiteconfig
```

## Usage

Due to the senstive nature of exposing environment variables this module does not naively expose everything in `ENV`
such as database usernames and passwords. Instead, projects should individually declare what environment variables can
be modified such as `ALGOLIA_ADMIN_API_KEY`

```
Wilr\EnvSiteConfig\EnvSiteConfigExtension:
  allowlist:
    - ALGOLIA_ADMIN_API_KEY
    - ALGOLIA_SEARCH_API_KEY
    - ALGOLIA_SEARCH_APP_ID
```
