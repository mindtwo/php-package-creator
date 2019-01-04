# PHP Package Creator

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

Ask 100 developers what defines an awesome PHP package and you’ll get a lot of different answers. That also sounds like a really long and boring task, so the The League of Extraordinary Packages come up with a list of rules that we think make a package awesome.

This installer is a wrapper around the [thephpleague/skeleton][link-skeleton] package.

## Install

Via Composer

``` bash
$ composer require mindtwo/php-package-creator --global
```

Make sure to place the `$HOME/.composer/vendor/bin directory` (or the equivalent directory for your OS) in your `$PATH` so the executable can be located by your system.

## Usage

Once installed, the `php-package-creator new` command will create a fresh php package based on the thephpleague skeleton repository.

Only the first argumen

``` bash
php-package-creator new test-package
```

### Argument & Options

There is only one required argument ans some optional options for the `php-package-creator new` command.

- Argument: `wordpress new folder_name` (Required)
- Option: `wordpress new folder_name --author_name=John` (Optional)
- Option: `wordpress new folder_name --author_github_username=john` (Optional)
- Option: `wordpress new folder_name --author_email=john@doe.com` (Optional)
- Option: `wordpress new folder_name --author_twitter=john` (Optional)
- Option: `wordpress new folder_name --author_website=https://example.com` (Optional)
- Option: `wordpress new folder_name --package_vendor=doe` (Optional)
- Option: `wordpress new folder_name --package_name=john` (Optional)
- Option: `wordpress new folder_name --package_description="Example..."` (Optional)
- Option: `wordpress new folder_name --psr4_namespace=Doe\John` (Optional)
- Option: `wordpress new folder_name --force` (Optional)

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [mindtwo GmbH][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/mindtwo/php-package-creator.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/mindtwo/php-package-creator.svg?style=flat-square
[link-skeleton]: https://github.com/thephpleague/skeleton
[link-packagist]: https://packagist.org/packages/mindtwo/php-package-creator
[link-downloads]: https://packagist.org/packages/mindtwo/php-package-creator
[link-author]: https://github.com/mindtwo
[link-contributors]: ../../contributors
