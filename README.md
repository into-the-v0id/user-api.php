# User API

Simple JSON API for managing Users

## About

This project is basically just an excuse to write my own little framework. It is way overkill for a simple User API. Do not use this project in production!

## Setup

### Dev

```console
$ ln -sr docker-compose.dev.yml docker-compose.yml
$ cp app/.env.development app/.env
$ docker compose build
$ docker compose run app composer install
$ docker compose up
```

### Prod

```console
$ ln -sr docker-compose.prod.yml docker-compose.yml
$ cp app/.env.production app/.env
$ docker compose build
$ docker compose up
```

## Example

```console
$ curl 'http://localhost/users/'
[
    {
        "id": "01JF9YTSWS2C67CNXQQBE34DBB",
        "name": "max",
        "dateCreated": "2024-12-17T09:22:51+00:00",
        "dateUpdated": "2024-12-17T09:22:51+00:00"
    }
]
```

## Modules

### [Framework](./app/src/Framework/)

This project does not use any existing framework. Instead it builds upon libraries to create its own little framework. The Framework module does not contain any application/domain logic for the User API - it is generic.

### [UserApi](./app/src/UserApi/)

This module uses components from the Framework module. The UserApi module contains all the application/domain logic for the User API.

## License

Copyright (C) Oliver Amann

This project is licensed under the GNU Affero General Public License Version 3 (AGPL-3.0-only). Please see [LICENSE](./LICENSE) for more information.
