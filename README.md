# Comfy Sylius Admin Bundle

[![Latest Stable Version](https://poser.pugx.org/oliverde8/comfy-sylius-admin-bundle/v)](//packagist.org/packages/oliverde8/comfy-sylius-admin-bundle)
[![Total Downloads](https://poser.pugx.org/oliverde8/comfy-sylius-admin-bundle/downloads)](//packagist.org/packages/oliverde8/comfy-sylius-admin-bundle)
[![Latest Unstable Version](https://poser.pugx.org/oliverde8/comfy-sylius-admin-bundle/v/unstable)](//packagist.org/packages/oliverde8/comfy-sylius-admin-bundle)
[![License](https://poser.pugx.org/oliverde8/comfy-sylius-admin-bundle/license)](//packagist.org/packages/oliverde8/comfy-sylius-admin-bundle)

This bundle adds the edition interface to the Sylius admin so that admins can configure
their site using the [Comfy bundle](https://github.com/oliverde8/comfyBundle).

Check the Comfy bundle documentation [here](https://github.com/oliverde8/comfyBundle).

## Requirements

- PHP >= 8.0
- `oliverde8/comfy-bundle`
- `sylius/admin-bundle: ^2.0`

> **Sylius 2 only for the UI.** The templates shipped here target the **Sylius 2 admin UI**
> (Bootstrap 5 / Tabler + [Twig Hooks](https://github.com/Sylius/TwigHooks)): they extend
> `@SyliusAdmin/shared/layout/base.html.twig` and reuse the Sylius 2 CRUD partials. The
> `^1.11` part of the constraint is kept for BC of the PHP code only — the admin page will
> not render on Sylius 1.

## Install

### 1. Require the bundle

```bash
composer require oliverde8/comfy-sylius-admin-bundle
```

### 2. Register the bundles (`config/bundles.php`)

```php
oliverde8\ComfyBundle\oliverde8ComfyBundle::class => ['all' => true],
oliverde8\ComfySyliusAdminBundle\oliverde8ComfySyliusAdminBundle::class => ['all' => true],
```

### 3. Import the bundle configuration (`config/packages/comfy_sylius_admin.yaml`)

Like Sylius itself and the official plugins, the bundle ships a single configuration
entry point that the application imports:

```yaml
imports:
    - { resource: "@oliverde8ComfySyliusAdminBundle/Resources/config/app/config.yml" }
```

That file imports every `Resources/config/app/twig_hooks/**/*.yaml` shipped by the bundle,
so new hook files are picked up without touching the application.

### 4. Add the routes (`config/routes/comfy_sylius_admin.yaml`)

```yaml
comfy_bundle:
    resource: '@oliverde8ComfySyliusAdminBundle/Controller'
    type: attribute
    prefix: /admin
```

The configuration screen is then available at `/admin/comfy/configs`
(route `sylius_admin_comfy_config`), and a `Comfy` entry is added to the admin menu under
`Configuration` by `Menu\AdminMenuListener`.

### 5. Database

Config values are stored in the `comfy_config_value` table
(`oliverde8\ComfyBundle\Entity\ConfigValue`, attribute mapping). Generate and run a
migration:

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

### 6. Admin assets (`assets/admin/entrypoint.js`)

```js
import '@vendor/oliverde8/comfy-sylius-admin-bundle/assets/admin/entrypoint';
```

Rebuild frontend assets (e.g. `yarn encore dev`) to include the bundle's admin

## Translations

Config groups and config names use the `comfy.<path>.name` keys, where `<path>` is the
config path with `/` replaced by `.` (see the Comfy bundle documentation).
