<?php
use App\Middleware\GuestMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

$app->get('/','HomeController:index')->setName('home');

$app->get('/403','HomeController:p403')->setName('403');
$app->get('/temlates','TemlatesController:list');


$app->group('',function () {

	$this->get('/auth/signup','AuthController:getSignUp')->setName('auth.signup');
	$this->post('/auth/signup','AuthController:postSignUp');

	$this->get('/auth/signin','AuthController:getSignIn')->setName('auth.signin');
	$this->post('/auth/signin','AuthController:postSignIn');
	
	$this->get('/auth/confirm','AuthController:confirmEmail');
})->add(new GuestMiddleware($container));

$app->group('',function () use ($container)  {
	$this->get('/auth/signout','AuthController:getSignOut')->setName('auth.signout');

	$this->get('/auth/password/change','PasswordController:getChangePassword')->setName('auth.password.change')->add(new PermissionMiddleware($container,'moderator'));



	$this->post('/auth/password/change','PasswordController:postChangePassword');

    $this->group('/admin',function () use ($container)  {
        $this->get('/','TemplateController:list')->setName('admin.main');
        $this->get('/','TemplateController:list')->setName('admin.main');

    });


})->add(new AuthMiddleware($container));



