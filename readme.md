# API - Project

----------

## Overview

API Project is an API used for Frontend application to manage data of Api Project.

### Application Information

* Uses [`Laravel Framework 5.4`](https://laravel.com/docs/5.4/)

----------

### Application Requirement

* PHP >= 5.6.4 or later
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* [`PostgreSQL 9.6.4`](https://www.postgresql.org/docs/9.6/static/release-9-6-4.html) or later as database server.
* [`Composer`](https://getcomposer.org/) as application package manager.

#### Required Packages

**Note:** Packages are declared in composer.json and automatically installed in vendor directory when executing `composer install` successfully.

#### Required Packages for Development


----------

## Installation

API project works well with Apache or PHP built-in web server.

### Virtual Host

Set application document root pointed to `public` directory of this application.
For Apache, please enable `rewrite` mod.

----------

### Application Setup

#### Directory Permission
* `storage` directory and it's contents should be **writeable** by server-user (i.e www-data or apache).
* `public/uploads` directory and it's contents should be **writeable** by server-user (i.e www-data or apache).

#### Environment Setup

Laravel uses dotenv to manage environment of application, this is useful to control application behaviour to distinguish it from `local`, `dev`, `stage`, or `testing` environment.

This application supplied with `.env.example` file in it's root. The content of this file will looks like:
```
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_LOG_LEVEL=debug
APP_URL=http://localhost
APP_API_VER="(version not set)"
APP_WEB_VER="(version not set)"

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=

ERROR_REPORT_OPTION=TRUE
ERROR_REPORT_MAIL=error@example.com

```

Please set the value of each variables to your needs, then save as `.env` file.

#### Composer

See composer installation on [`this document`](https://getcomposer.org/doc/00-intro.md) to download composer on your machine.

When ready, execute **`composer install`** in application root directory (ie: `/var/www/api-project/`).

#### Database Migration

To initiate database tables of the application, run `artisan migrate`.

#### Database Seeding

This application requires user to login first before doing any action. Thus, valid credential is required.

Fake data generator is provided within application. See complete seeder in `database/seeds/DatabaseSeeder.php`.

See `DatabaseSeeder.php` file for details.

When ready, execute **`php artisan db:seed`**.

#### Queue

There are feature utilizes queue using Laravel Queue API. Please visit [`Queues`](https://laravel.com/docs/5.4/queues) for more documentation.

This document will share settings with supervisor driver. See [`Supervisor Configuration`](https://laravel.com/docs/5.4/queues#supervisor-configuration) how to install supervisor.

Configuration for supervisor included in `.supervisor.example` on root directory of this application.

##### Running Queue Manually (without service)

Run this command if there is no service installed:

`php ../api-project/artisan queue:work --tries=1 & >> ../api-project/storage/logs/queue-worker-manual-1.log`

Takes note if server restart, this command need to be executed manually again.
