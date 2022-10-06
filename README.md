# Comfy Sylius Admin Bundle

[![Latest Stable Version](https://poser.pugx.org/oliverde8/comfy-sylius-admin-bundle/v)](//packagist.org/packages/oliverde8/comfy-sylius-admin-bundle)
[![Total Downloads](https://poser.pugx.org/oliverde8/comfy-sylius-admin-bundle/downloads)](//packagist.org/packages/oliverde8/comfy-sylius-admin-bundle)
[![Latest Unstable Version](https://poser.pugx.org/oliverde8/comfy-sylius-admin-bundle/v/unstable)](//packagist.org/packages/oliverde8/comfy-sylius-admin-bundle)
[![License](https://poser.pugx.org/oliverde8/comfy-sylius-admin-bundle/license)](//packagist.org/packages/oliverde8/comfy-sylius-admin-bundle)

This bundle adds the edition interface to stripe so that admins can configure their site using comfy bundle.

Check Comfy bundles documentation [here](https://github.com/oliverde8/comfyBundle)

## Requirements

- https://docs.sylius.com/en/1.11/cookbook/frontend/webpack.html

## Install

- Add the Sylius UI configuration in your application (`config/packages/comfy_sylius_admin.yml`)
```
imports:
    - { resource: "@oliverde8ComfySyliusAdminBundle/Resources/config/sylius_ui.yml" }
```

- Add the Routes in your application (`config/routes/comfy_sylius_admin.yml`)
```
comfy_bundle:
    resource: '@oliverde8ComfySyliusAdminBundle/Controller'
    type: annotation
    prefix: /admin
```

- Migration

Add the comfy configuration table using the migration feature.

- Add JS and CSS to Admin `assets/admin/entry.js`
```
import '../../bundles/oliverde8/ComfySyliusAdminBundle/Resources/private/entry';
```
