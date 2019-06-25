<?php

use App\Middleware\GuestMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

$app->get('/400', 'ErrosController:error400')->setName('400');
$app->get('/401', 'ErrosController:error401')->setName('401');
$app->get('/403', 'ErrosController:error403')->setName('403');
$app->get('/404', 'ErrosController:error404')->setName('404');
$app->get('/500', 'ErrosController:error500')->setName('500');


$app->get('/temlates', 'TemlatesController:list');

$app->group('', function () {

    $this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/auth/signup', 'AuthController:postSignUp');

    $this->get('/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/signin', 'AuthController:postSignIn')->setName('signin');

    $this->get('/auth/confirm', 'AuthController:confirmEmail');
})->add(new GuestMiddleware($container));

$app->group('', function () use ($container) {

    $this->get('/signout', 'AuthController:getSignOut')->setName('signout');

    $this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change')->add(new PermissionMiddleware($container, 'moderator'));

    $this->post('/auth/password/change', 'PasswordController:postChangePassword');

    $this->get('/', 'TemplateController:index')->setName('admin.main')->add(new PermissionMiddleware($container,'moderator|editor'));

    $this->group('/template', function () use ($container) {
        $this->get('/create', 'TemplateController:create')->setName('admin.template.create')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->post('/store', 'TemplateController:store')->setName('admin.template.store')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->get('/edit/{id:[0-9]+}', 'TemplateController:edit')->setName('admin.template.edit')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->map(['GET', 'POST'],'/update', 'TemplateController:update')->setName('admin.template.update')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->delete('/destroy/{id:[0-9]+}', 'TemplateController:destroy')->setName('admin.template.destroy')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->get('/remove_attach/{id:[0-9]+}', 'TemplateController:removeAttach')->setName('admin.template.edit')->add(new PermissionMiddleware($container,'moderator|editor'));
    });

    $this->group('/subscribers', function () use ($container) {
        $this->get('/', 'SubscribersController:index')->setName('admin.subscribers.index')->add(new PermissionMiddleware($container,'moderator'));
        $this->get('/create', 'SubscribersController:create')->setName('admin.subscribers.create')->add(new PermissionMiddleware($container,'moderator'));
        $this->post('/store', 'SubscribersController:store')->setName('admin.subscribers.store')->add(new PermissionMiddleware($container,'moderator'));
        $this->get('/edit/{id:[0-9]+}', 'SubscribersController:edit')->setName('admin.subscribers.edit')->add(new PermissionMiddleware($container,'moderator'));
        $this->map(['GET', 'POST'],'/update', 'SubscribersController:update')->setName('admin.subscribers.update')->add(new PermissionMiddleware($container,'moderator'));
        $this->delete('/destroy/{id:[0-9]+}', 'SubscribersController:destroy')->setName('admin.subscribers.destroy')->add(new PermissionMiddleware($container,'moderator'));
        $this->get('/import', 'SubscribersController:import')->setName('admin.subscribers.import')->add(new PermissionMiddleware($container,'moderator'));
        $this->post('/import-subscribers', 'SubscribersController:importSubscribers')->setName('admin.subscribers.import_subscribers')->add(new PermissionMiddleware($container,'moderator'));
        $this->get('/export', 'SubscribersController:export')->setName('admin.subscribers.export')->add(new PermissionMiddleware($container,'moderator'));
        $this->get('/remove-all', 'SubscribersController:removeAll')->setName('admin.subscribers.remove_all')->add(new PermissionMiddleware($container,'moderator'));
        $this->post('/status', 'SubscribersController:status')->setName('admin.subscribers.status')->add(new PermissionMiddleware($container,'moderator'));
    });

    $this->group('/category', function () use ($container) {
        $this->get('/', 'CategoryController:index')->setName('admin.category.index')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->get('/create', 'CategoryController:create')->setName('admin.category.create')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->post('/store', 'CategoryController:store')->setName('admin.category.store')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->get('/edit/{id:[0-9]+}', 'CategoryController:edit')->setName('admin.category.edit')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->map(['GET', 'POST'],'/update', 'CategoryController:update')->setName('admin.category.update')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->delete('/destroy/{id:[0-9]+}', 'CategoryController:destroy')->setName('admin.category.destroy')->add(new PermissionMiddleware($container,'moderator|editor'));
    });

    $this->group('/users', function () use ($container) {
        $this->get('/', 'UsersController:index')->setName('admin.users.index')->add(new PermissionMiddleware($container,'moderator'));;
        $this->get('/create', 'UsersController:create')->setName('admin.users.create')->add(new PermissionMiddleware($container,'moderator'));;
        $this->post('/store', 'UsersController:store')->setName('admin.users.store')->add(new PermissionMiddleware($container,'moderator'));;
        $this->get('/edit/{id:[0-9]+}', 'UsersController:edit')->setName('admin.users.edit')->add(new PermissionMiddleware($container,'moderator'));;
        $this->map(['GET', 'POST'],'/update', 'UsersController:update')->setName('admin.users.update')->add(new PermissionMiddleware($container,'moderator'));;
        $this->delete('/destroy/{id:[0-9]+}', 'UsersController:destroy')->setName('admin.users.destroy')->add(new PermissionMiddleware($container,'moderator'));;
    });

    $this->group('/log', function () use ($container) {
        $this->get('/', 'UsersController:index')->setName('admin.log.index')->add(new PermissionMiddleware($container,'moderator|editor'));;
        $this->post('/clear', 'UsersController:store')->setName('admin.log.clear')->add(new PermissionMiddleware($container,'moderator|editor'));;
        $this->get('/download', 'UsersController:edit')->setName('admin.log.download')->add(new PermissionMiddleware($container,'moderator|editor'));;
    });

    $this->group('/settings', function () use ($container) {
        $this->get('/', 'SettingsController:index')->setName('admin.settings.index');
        $this->get('/create', 'SettingsController:create')->setName('admin.settings.create');
        $this->post('/store', 'SettingsController:store')->setName('admin.settings.store');
        $this->get('/edit/{id:[0-9]+}', 'SettingsController:edit')->setName('admin.settings.edit');
        $this->map(['GET', 'POST'],'/update', 'SettingsController:update')->setName('admin.settings.update');
        $this->delete('/destroy/{id:[0-9]+}', 'SettingsController:destroy')->setName('admin.settings.destroy');
    });

    $this->group('/datatable', function () use ($container) {
        $this->get('/templates', 'DataTableController:getTemplates')->setName('admin.datatable.templates')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->get('/category', 'DataTableController:getCategory')->setName('admin.datatable.category')->add(new PermissionMiddleware($container,'moderator|editor'));
        $this->get('/subscribers', 'DataTableController:getSubscribers')->setName('admin.datatable.subscribers')->add(new PermissionMiddleware($container,'moderator'));
        $this->get('/settings', 'DataTableController:getSettings')->setName('admin.datatable.settings');
        $this->get('/users', 'DataTableController:getUsers')->setName('admin.datatable.users');
    });

})->add(new AuthMiddleware($container));



