<?php

use App\Middleware\GuestMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

$app->get('/403', 'HomeController:p403')->setName('403');
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

    $this->get('/', 'TemplateController:index')->setName('admin.main');

    $this->group('/template', function () use ($container) {
        $this->get('/create', 'TemplateController:create')->setName('admin.template.create');
        $this->post('/store', 'TemplateController:store')->setName('admin.template.store');
        $this->get('/edit/{id:[0-9]+}', 'TemplateController:edit')->setName('admin.template.edit');
        $this->put('/update', 'TemplateController:update')->setName('admin.template.update');
        $this->delete('/destroy/{id:[0-9]+}', 'TemplateController:destroy')->setName('admin.template.destroy');
    });

    $this->group('/subscribers', function () use ($container) {
        $this->get('/', 'SubscribersController:index')->setName('admin.subscribers.index');
        $this->get('/create', 'SubscribersController:create')->setName('admin.subscribers.create');
        $this->post('/store', 'SubscribersController:store')->setName('admin.subscribers.store');
        $this->get('/edit/{id:[0-9]+}', 'SubscribersController:edit')->setName('admin.subscribers.edit');
        $this->get('/update', 'SubscribersController:update')->setName('admin.subscribers.update');
        $this->delete('/destroy/{id:[0-9]+}', 'SubscribersController:destroy')->setName('admin.subscribers.destroy');
        $this->get('/import', 'SubscribersController:import')->setName('admin.subscribers.import');
        $this->get('/export', 'SubscribersController:export')->setName('admin.subscribers.export');
    });

    $this->group('/category', function () use ($container) {
        $this->get('/', 'CategoryController:index')->setName('admin.category.index');
        $this->get('/create', 'CategoryController:create')->setName('admin.category.create');
        $this->post('/store', 'CategoryController:store')->setName('admin.category.store');
        $this->get('/edit/{id:[0-9]+}', 'CategoryController:edit')->setName('admin.category.edit');
        $this->put('/update', 'CategoryController:update')->setName('admin.category.update');
        $this->delete('/destroy/{id:[0-9]+}', 'CategoryController:destroy')->setName('admin.category.destroy');
    });

    $this->group('/users', function () use ($container) {
        $this->get('/', 'UsersController:index')->setName('admin.users.index');
        $this->get('/create', 'UsersController:create')->setName('admin.users.create');
        $this->post('/store', 'UsersController:store')->setName('admin.users.store');
        $this->get('/edit/{id:[0-9]+}', 'UsersController:edit')->setName('admin.users.edit');
        $this->put('/update', 'UsersController:update')->setName('admin.users.update');
        $this->delete('/destroy/{id:[0-9]+}', 'UsersController:destroy')->setName('admin.users.destroy');
    });

    $this->group('/log', function () use ($container) {
        $this->get('/', 'UsersController:index')->setName('admin.log.index');
        $this->post('/clear', 'UsersController:store')->setName('admin.log.clear');
        $this->get('/download', 'UsersController:edit')->setName('admin.log.download');
    });

    $this->group('/settings', function () use ($container) {
        $this->get('/', 'SettingsController:index')->setName('admin.settings.index');
        $this->get('/create', 'SettingsController:create')->setName('admin.settings.create');
        $this->post('/store', 'SettingsController:store')->setName('admin.settings.store');
        $this->get('/edit/{id:[0-9]+}', 'SettingsController:edit')->setName('admin.settings.edit');
        $this->put('/update', 'SettingsController:update')->setName('admin.settings.update');
        $this->delete('/destroy/{id:[0-9]+}', 'SettingsController:destroy')->setName('admin.settings.destroy');
    });

    $this->group('/datatable', function () use ($container) {
        $this->any('/templates', 'DataTableController:getTemplates')->setName('admin.datatable.templates');
    });

})->add(new AuthMiddleware($container));



