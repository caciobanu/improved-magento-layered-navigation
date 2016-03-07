README
================

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

Contribution
------------

To contribute please issue pull requests to the `develop` branch _only_. New releases will be merged to feature branches. Bugfixes are hotfix patched to both `master` and `develop`.
