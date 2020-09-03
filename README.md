# SlimAPI
[![Release][img-release]][link-release]
[![License][img-license]][link-license]
[![Build][img-build]][link-build]
[![Code Coverage][img-coverage]][link-coverage]
[![PHPStan][img-phpstan]][link-phpstan]

A collection of tools for building APIs. Based on [Slim Framework 4][link-slim].

## Installation
``` bash
$ composer require slimapi/slimapi
```

## Local Development & Testing
```bash
$ docker-compose up
$ docker-compose exec app composer test
```
> **NOTE**: Available on http://localhost:8080. You can change the port in [docker-compose.yml](docker-compose.yml)

## License
See [LICENSE][link-license] file for more information.

[link-build]: https://github.com/slimapi/slimapi/actions
[link-coverage]: https://codecov.io/gh/slimapi/slimapi
[link-license]: LICENSE.md
[link-phpstan]: phpstan.neon
[link-release]: https://github.com/slimapi/slimapi/releases
[link-slim]: http://www.slimframework.com

[img-build]: https://img.shields.io/github/workflow/status/slimapi/slimapi/Continuous%20Integration/master?style=flat-square&label=Build
[img-coverage]: https://img.shields.io/codecov/c/github/slimapi/slimapi/master?style=flat-square&label=Coverage
[img-license]: https://img.shields.io/github/license/slimapi/slimapi?style=flat-square&label=License&color=blue
[img-phpstan]: https://img.shields.io/badge/style-%208%20%28strict%29-brightgreen.svg?&label=PHPStan&style=flat-square
[img-release]: https://img.shields.io/github/v/tag/slimapi/slimapi.svg?label=Release&style=flat-square
