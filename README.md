# Laravel Actions

> Actions are like version control for your migration process, allowing your team to modify and share the application's actionable schema. If you have ever had to tell a teammate to manually perform any action on a producton server, you've come across an issue that actions solves.

![laravel-actions](https://user-images.githubusercontent.com/10347617/101372725-4276fa00-38bd-11eb-818b-0e6a599edcb7.png)

[![StyleCI Status][badge_styleci]][link_styleci]
[![Github Workflow Status][badge_build]][link_build]
[![Coverage Status][badge_coverage]][link_scrutinizer]
[![Scrutinizer Code Quality][badge_quality]][link_scrutinizer]

[![Stable Version][badge_stable]][link_packagist]
[![Unstable Version][badge_unstable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![License][badge_license]][link_license]

[![For Laravel][badge_laravel]][link_packagist]
[![For Lumen][badge_lumen]][link_packagist]

## Installation

To get the latest version of Laravel Actions, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require andrey-helldar/laravel-actions
```

Or manually update `require` block of `composer.json` and run `composer update`.

```json
{
    "require": {
        "andrey-helldar/laravel-actions": "^1.7"
    }
}
```

#### Laravel Framework

Nothing else needs to be done. All is ready 😊

#### Lumen Framework

This package is focused on Laravel development, but it can also be used in Lumen with some workarounds. Because Lumen works a little different, as it is like a
barebone version of Laravel and the main configuration parameters are instead located in `bootstrap/app.php`, some alterations must be made.

You can install `Laravel Actions` in `app/Providers/AppServiceProvider.php`, and uncommenting this line that registers the App Service Providers so it can
properly load.

```
// $app->register(App\Providers\AppServiceProvider::class);
```

If you are not using that line, that is usually handy to manage gracefully multiple Lumen installations, you will have to add this line of code under
the `Register Service Providers` section of your `bootstrap/app.php`.

```php
$app->register(\Helldar\LaravelActions\ServiceProvider::class);
```

## How to use

### Generating actions

To create a migration, use the `make:migration:action` Artisan command:

```
php artisan make:migration:action my_action
```

The new action will be placed in your `database/actions` directory. Each action file name contains a timestamp, which allows Laravel to determine the order of
the actions.

> At the first start, you need to create a table by running the `migrate:actions:install` command.
>
> If you execute `migrate:actions` with the first command, the `migrate:actions:install` command will be called automatically.

### Running actions

To run all of your outstanding actions, execute the `migrate:actions` Artisan command:

```
php artisan migrate:actions
```

#### Forcing Actions To Run In Production

Some action operations are destructive, which means they may cause you to lose data. In order to protect you from running these commands against your production
database, you will be prompted for confirmation before the commands are executed. To force the commands to run without a prompt, use the `--force` flag:

```
php artisan migrate:actions --force
```

#### Execution Every Time

In some cases, you need to call the code every time you deploy the application. For example, to call reindexing.

To do this, override the `$once` variable in the action file:

```php
use Helldar\LaravelActions\Support\Actionable;

class Reindex extends Actionable
{
    protected $once = false;

    public function up(): void
    {
        // your code
    }
}
```

If the value is `$once = false`, the `up` method will be called every time the `migrate:actions` command called.

In this case, information about it will not be written to the `migration_actions` table and, therefore, the `down` method will not be called when the rollback
command is called.

#### Execution In A Specific Environment

In some cases, it becomes necessary to execute an action in a specific environment. For example `production`.

For this you can use the `$environment` parameter:

```php
use Helldar\LaravelActions\Support\Actionable;

class Reindex extends Actionable
{
    /** @var string|array|null */
    protected $environment = 'production';

    public function up(): void
    {
        // your code
    }
}
```

You can also specify multiple environment names:

```php
use Helldar\LaravelActions\Support\Actionable;

class Reindex extends Actionable
{
    /** @var string|array|null */
    protected $environment = ['testing', 'staging'];

    public function up(): void
    {
        // your code
    }
}
```

By default, the action will run in all environments. The same will happen if you specify `null` or `[]` as the value.

#### Execution Excluding Certain Environments

In some cases, it becomes necessary to execute an action excluding certain environments. For example `production`.

For this you can use the `$except_environment` parameter:

```php
use Helldar\LaravelActions\Support\Actionable;

class Reindex extends Actionable
{
    /** @var string|array|null */
    protected $except_environment = 'production';

    public function up(): void
    {
        // your code
    }
}
```

You can also specify multiple environment names:

```php
use Helldar\LaravelActions\Support\Actionable;

class Reindex extends Actionable
{
    /** @var string|array|null */
    protected $except_environment = ['testing', 'staging'];

    public function up(): void
    {
        // your code
    }
}
```

By default, no actions will be excluded. The same happens if you specify `null` or `[]` value.

#### Database Transactions

In some cases, it becomes necessary to undo previously performed actions in the database. For example, when code execution throws an error. To do this, the code
must be wrapped in a transaction.

By setting the `$transactions = true` parameter, you will ensure that your code is wrapped in a transaction without having to manually call
the `DB::transaction()` method. This will reduce the time it takes to create the action.

```php
use Helldar\LaravelActions\Support\Actionable;

class AddSomeData extends Actionable
{
    protected $transactions = true;

    protected $transaction_attempts = 3;

    public function up(): void
    {
        // ...

        $post = Post::create([
            'title' => 'Random Title'
        ]);

        $post->tags()->sync($ids);
    }
}
```

### Rolling Back Actions

To roll back the latest action operation, you may use the `rollback` command. This command rolls back the last "batch" of actions, which may include multiple
action files:

```
php artisan migrate:actions:rollback
```

You may roll back a limited number of actions by providing the `step` option to the rollback command. For example, the following command will roll back the last
five actions:

```
php artisan migrate:actions:rollback --step=5
```

The `migrate:actions:reset` command will roll back all of your application's migrations:

```
php artisan migrate:actions:reset
```

### Roll Back & Action Using A Single Command

The `migrate:actions:refresh` command will roll back all of your migrations and then execute the `migrate:actions` command. This command effectively re-creates
your entire database:

```
php artisan migrate:actions:refresh
```

You may roll back & re-migrate a limited number of migrations by providing the `step` option to the `refresh` command. For example, the following command will
roll back & re-migrate the last five migrations:

```
php artisan migrate:actions:refresh --step=5
```

### Actions Status

The `migrate:actions:status` command displays the execution status of actions. In it you can see which actions were executed and which were not:

```
php artisan migrate:actions:status
```

## License

This package is licensed under the [MIT License](LICENSE).

## For Enterprise

Available as part of the Tidelift Subscription.

The maintainers of `andrey-helldar/laravel-actions` and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for
the open source packages you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact packages
you
use. [Learn more](https://tidelift.com/subscription/pkg/packagist-andrey-helldar-laravel-actions?utm_source=packagist-andrey-helldar-laravel-actions&utm_medium=referral&utm_campaign=enterprise&utm_term=repo)
.


[badge_build]:          https://img.shields.io/github/workflow/status/andrey-helldar/laravel-actions/phpunit?style=flat-square

[badge_downloads]:      https://img.shields.io/packagist/dt/andrey-helldar/laravel-actions.svg?style=flat-square

[badge_laravel]:        https://img.shields.io/badge/Laravel-6.x%20%7C%207.x%20%7C%208.x-orange.svg?style=flat-square

[badge_lumen]:          https://img.shields.io/badge/Lumen-6.x%20%7C%207.x%20%7C%208.x-orange.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/andrey-helldar/laravel-actions.svg?style=flat-square

[badge_coverage]:       https://img.shields.io/scrutinizer/coverage/g/andrey-helldar/laravel-actions/main?style=flat-square

[badge_quality]:        https://img.shields.io/scrutinizer/quality/g/andrey-helldar/laravel-actions/main?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/andrey-helldar/laravel-actions?label=stable&style=flat-square

[badge_styleci]:        https://styleci.io/repos/317845207/shield

[badge_unstable]:       https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_build]:           https://github.com/andrey-helldar/laravel-actions/actions

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/andrey-helldar/laravel-actions

[link_scrutinizer]:     https://scrutinizer-ci.com/g/andrey-helldar/laravel-actions/?branch=main

[link_styleci]:         https://github.styleci.io/repos/317845207
