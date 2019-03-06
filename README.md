README
================

![#ffc734](https://placehold.it/15/ffc734/000000?text=+) This extension only receives security fixes.
------------

Requirements
------------

The supported Magento version is 1.9.x

Features
----------------

- ajax navigation using history pushState/popState
- price slider with submit button
- SEO URLs (http://www.example.com/men/shirts/filter/fit/regular,sharp/sleeve_length/long-sleeve.html)
- multiple filters for the same attribute
- supports Magento Configurable Swatches
- possibility to add "nofollow" on layered navigation pages

All the above features can be enabled/disabled from backend: "System -> Configuration -> Catalin SEO -> Catalog Layered Navigation"

Install via Modman
----------------

You can install this module using [Colin Mollenhour's](https://github.com/colinmollenhour) [Modman tool](https://github.com/colinmollenhour/modman).

```bash
$ modman init
$ modman clone https://github.com/caciobanu/improved-magento-layered-navigation.git
```

Install via Composer
----------------

You can install this module with [Composer](https://getcomposer.org/) in combination with a Magento Composer installer (e.g. [Bragento Composer Installer](https://github.com/bragento/bragento-composer-installer)).

Make sure you have required the [Firegento packages](https://packages.firegento.com/) in your composer.json's `repositories` node

```json
"repositories": [
    {
      "type": "composer",
      "url": "https://packages.firegento.com"
    }
]
```
Afterwards you can install this module by simply requiring it.

```bash
$ composer require caciobanu/improved-magento-layered-navigation
```

Contribution
------------

To contribute please issue pull requests to the `develop` branch _only_. New releases will be merged to feature branches. Bugfixes are hotfix patched to both `master` and `develop`.
