# JmikolaJsAssetsHelperBundle

This bundle exposes the AssetsHelper service from Symfony2's templating
component to JavaScript,  which allows relative or absolute asset URI's to be
generated client-side.

## Compatibility

This bundle depends [PR #2502][], which is included in Symfony 2.0.5 and higher.

## Configuration

If you do not configure the bundle explicitly, it will only expose the default
package defined in the `templating` block of the FrameworkBundle configuration.

Named packages you wish to expose must be explicitly listed:

```yml
jmikola_js_assets_helper:
    packages_to_expose: [ cloudfront, s3 ]
```

While an array of package names is the normal format, the configuration will
also accept a scalar to expose a single package:

```yml
jmikola_js_assets_helper:
    packages_to_expose: cloudfront
```

In these examples, "cloudfront" and "s3" correspond to named packages in the
FrameworkBundle configuration. For example:

```yml
framework:
    templating:
        # The default package will be a PathPackage
        assets_version:        123
        assets_version_format: "%%s?version=%%s"
        packages:
            # The cloudfront package will be a UrlPackage
            cloudfront:
                version:        123
                version_format: "%%s?version=%%s"
                base_urls:      https://example.cloudfront.net
```

Additional information on configuring templating asset packages may be found in
the [FrameworkBundle docs][].

### Routing

The bundle defines one route to a dynamically generated JavaScript file. Ensure
this route is including in your application's routing configuration:

```yml
jmikola_js_assets_helper:
    resource: "@JmikolaJsAssetsHelperBundle/Resources/config/routing/routing.xml"
```

### Assets

The bundle includes a compiled JavaScript asset, which must be published to your
`web/` directory:

```bash
$ php app/console assets:install --symlink web
```

Include the compiled and dynamic JavaScript in your applications template:

```jinja
<script src="{{ asset('bundles/jmikolajsassetshelper/js/assets_helper.js') }}"></script>
<script src="{{ path('jmikola_js_assets_helper_js') }}"></script>
```

## Usage

Once configured, the bundle creates a single `AssetsHelper` global in JavaScript.
This is modeled after the PHP class from Symfony2's Templating component and
has the following methods:

```js
/**
 * Returns the public path.
 *
 * Absolute paths (i.e. http://...) are returned unmodified.
 *
 * @param string path        A public path
 * @param string packageName A package name (optional)
 *
 * @return string A public path which takes into account the base path and URL path
 */
function getUrl(path, packageName);

/**
 * Gets the version to add to public URL.
 *
 * @param string packageName A package name (optional)
 * @return string The current version
 */
function getVersion(packageName);
```

Typically, you will want to use the `getUrl()` method to generate asset paths.
Keep in mind that if you refer to a named package that has not been exposed, an
`InvalidPackageError` will be thrown.

The following equivalent snippets demonstrate how `AssetsHelper.getUrl()`
compares to Symfony2's asset helper for Twig: 

```js
// JavaScript
'<img src="' + AssetsHelper.getUrl('/images/logo.png') + '">';
```

```jinja
{# Twig #}
<img src="{{ asset('/images/logo.png') }}">
```

## Development

Note: This bundle includes a static JavaScript asset, which is pre-compiled with
Google's [Closure Compiler][]. Any changes to the static JavaScript will require
that you recompile the asset. For your convenience, you may want to install
[JMSGoogleClosureBundle][] and use the following command:

```bash
$ php app/console plovr:build @JmikolaJsAssetsHelperBundle/compile.js
```

  [PR #2502]: https://github.com/symfony/symfony/pull/2502
  [FrameworkBundle docs]: http://symfony.com/doc/current/reference/configuration/framework.html#templating
  [Closure Compiler]: http://code.google.com/closure/compiler/
  [JMSGoogleClosureBundle]: https://github.com/schmittjoh/JMSGoogleClosureBundle
