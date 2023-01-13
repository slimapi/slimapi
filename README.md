# SlimAPI
[![PHP Version][img-php-version]][link-packagist]
[![Release][img-release]][link-release]
[![License][img-license]][link-license]
[![Build][img-build]][link-build]
[![Code Coverage][img-coverage]][link-coverage]
[![PHPStan][img-phpstan]][link-phpstan]

A collection of tools for building JSON API(s), based on [Slim Framework 4][link-slim].

## Installation
``` bash
$ composer require slimapi/slimapi
```

## Local Development & Testing
```bash
$ docker-compose up
$ docker-compose exec app composer.sh test
```
> **NOTE**: Available on http://localhost:8080. You can change the port in [docker-compose.yml](docker-compose.yml)

## License
See [LICENSE][link-license] file for more information.

[link-build]: https://github.com/slimapi/slimapi/actions
[link-coverage]: https://codecov.io/gh/slimapi/slimapi
[link-license]: LICENSE.md
[link-packagist]: https://packagist.org/packages/slimapi/slimapi
[link-phpstan]: phpstan.neon
[link-release]: https://github.com/slimapi/slimapi/tags
[link-slim]: http://www.slimframework.com

[img-build]: https://img.shields.io/github/actions/workflow/status/slimapi/slimapi/.github/workflows/ci.yml?branch=master&style=flat-square&label=Build
[img-coverage]: https://img.shields.io/codecov/c/github/slimapi/slimapi/master?style=flat-square&label=Coverage
[img-license]: https://img.shields.io/github/license/slimapi/slimapi?style=flat-square&label=License&color=blue
[img-php-version]: https://img.shields.io/packagist/dependency-v/slimapi/slimapi/php?label=PHP&style=flat-square
[img-phpstan]: https://img.shields.io/badge/style-%208%20%28strict%29-brightgreen.svg?&label=PHPStan&style=flat-square
[img-release]: https://img.shields.io/github/v/tag/slimapi/slimapi.svg?label=Release&style=flat-square
