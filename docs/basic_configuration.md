# Basic Configuration
## Prerequisites

Make sure that you have the locale properly set up in your project:
```shell
# src/App/Infrastructure/Symfony/Framework/config/config.yml

parameters:
    locale: es_ES
```

If you wish to use default texts provided in this bundle, you have to make sure your translator is enabled in your
configuration file.
``` shell
# src/App/Infrastructure/Symfony/Framework/config/config.yml

framework:
    translator:
        fallbacks: ["%locale%"]
        paths:
            - "%kernel.root_dir%/../../Ui/Translations"
```

This **CMS** can be integrated along with **BenGorUser** bundle and **BengorFile** bundle. If you are using one
of these bundles, make sure that you have installed at least the following versions in your *composer.json* file:

```
{
    "require:" {
        "bengor-file/file-bundle": "0.3.3",
        "bengor-user/user-bundle": "^0.8"
    }
}


```
Check out [BengorUser repository](https://github.com/BengorUser) and [BengorFile repository](https://github.com/BengorFile)
for complete documentation about the installation process of these bundles.

## Install dependencies

Add these dependencies to the *composer.json* file:
```
{
    "require": {
        "lin3s/admin-bundle": "dev-master",
        "lin3s/admin-ddd-extensions-bundle": "dev-master",
        "lin3s/cms-kernel": "dev-master",
        "lin3s/distribution": "^2.4",
        "lin3s/shared-kernel": "^0.3"
    }
}
```

Update your dependencies for installing all bundles:
```shell
$ composer update
```

Once all bundles have been installed, activate them in the *AppKernel*, take care with the initialization order:
``` php
// src/App/Infrastructure/Symfony/Framework/AppKernel.php
public function registerBundles()
{
    $bundles = [

        ...

        new Lin3sSharedKernelBundle(),
        new Lin3sDistributionBundle(),

        ...

        new Lin3sAdminBundle(),
        new Lin3sAdminDDDExtensionsBundle(),
        new Lin3sCmsKernelBundle(),

        ...

        new CmsKernelAdminBridgeBundle(), // Bridges have to be initialized at the end
    ]
}
```


## Basic set up

With this basic configuration you will have access to the **Admin CMS** with access to one entity and no security
implemented and no translations.

For using the **CMS** you have to add the routes that provide the new bundle to your routing file.
```shell
# src/App/Infrastructure/Symfony/Framework/config/routing.yml
cms_kernel_admin_bridge:
    resource: "@CmsKernelAdminBridgeBundle/Resources/config/routing_locale.yml"
    prefix: "admin/"

lin3s_admin:
    resource: "@Lin3sAdminBundle/Resources/config/routing.yml"
    prefix: "admin/"

```

### Twig Templates

Now you have to add the twig templates for customizing the **CMS** to your requirements, the following files have
to be modified in the `src/App/Infrastructure/Symfony/Framework/Resources/LIN3SAdminBundle` folder. You have to
create the folder structure if it already does not exist:
```
{# views/partial/javascripts.html.twig #}

<script src="{{ asset('bundles/lin3scmskernel/js/bundle.min.js') }}"></script>
<script src="{{ asset('bundles/cmskerneladminbridge/js/bundle.min.js') }}"></script>
<script src="{{ asset('bundles/lin3scmskernel/js/event-bus-js-init.min.js') }}"></script>
```

```
{# views/partial/stylesheets.html.twig #}

<link rel="stylesheet" href="{{ asset('bundles/lin3scmskernel/css/bundle.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('bundles/cmskerneladminbridge/css/bundle.min.css') }}" type="text/css">
```

```
{# views/partial/title.html.twig #}

My awesome project name
```

```
{# views/partial/menu.html.twig #}

<a class="menu__item" href="{{ path('lin3s_admin_dashboard') }}">
    {{ 'cms_kernel_admin_bridge.menu.dashboard' | trans({}, 'CmsKernelAdminBridge') }}
</a>
```

### Add an entity to the CMS

Given a simple entity called *Category*:
```
namespace App\Domain\Model\Category\Category;

class Category
{
    private $uid;
    private $title;
    private $slug;

    public function __construct($uid, $title, $slug)
    {
        $this->uid = $uid;
        $this->title = $title;
        $this->slug = $slug;
    }

    public function uid()
    {
        return $this->uid;
    }

    public function title()
    {
        return $title;
    }

    public function slug()
    {
        return $slug;
    }
}

```

The following configuration will add to the **CMS** a list view of the Category entity. In following sections you
will be able to add and edit new categories, add translations and add custom templates and actions.
```shell
# src/App/Infrastructure/Symfony/Framework/config/admin.yml

lin3s_admin:
    entities:
    categories:
        class: App\Domain\Model\Category\Category
        name:
            singular: admin.category.name.singular
            plural: admin.category.name.plural
            list:
                fields:
                    uid:
                        name: UID
                        type: string
                        options:
                            field: uid
                    name:
                        name: admin.list.fields.name
                        type: translatable_string
                        options:
                            field: name.name
                    slug:
                        name: admin.list.fields.slug
                        type: translatable_string
                        options:
                            field: slug



cms_kernel_admin_bridge:
    locales:
        default: "%locale%"
        others:
            - en_US
```

Also add the entity to the *menu.html.twig* file for displaying it in the navigation sidebar.
```
{# views/partial/menu.html.twig #}

<a class="menu__item" href="{{ path('lin3s_admin_list', {'entity': 'categories'}) }}">
    {{ 'admin.category.name.plural' | trans }}
</a>

```
