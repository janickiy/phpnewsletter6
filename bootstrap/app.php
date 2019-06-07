<?php

use Respect\Validation\Validator as v;

session_start();

require __DIR__ . '/../vendor/autoload.php';

try {
	$dotenv = (new \Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
	//
}

$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails' => true,
		'mailer' => [
		    'host' => getenv('MAIL_HOST'),
            'username' => getenv('MAIL_USERNAME'),
            'password' => getenv('MAIL_PASSWORD')
		],
		'baseUrl' => getenv('BASE_URL')
	],
]);

require_once __DIR__ . '/database.php';

$container = $app->getContainer();

$container['db'] = function ($container) use ($capsule) {
	return $capsule;
};

$container['auth'] = function($container) {
	return new \App\Auth\Auth;
};

$container['flash'] = function($container) {
	return new \Slim\Flash\Messages;
};

$container['mailer'] = function($container) {
	return new Nette\Mail\SmtpMailer($container['settings']['mailer']);
};

$container['view'] = function ($container) {
	$view = new \Slim\Views\Twig(__DIR__ . '/../resources/views/', [
		'cache' => false,
	]);

	$view->addExtension(new \Slim\Views\TwigExtension(
		$container->router,
		$container->request->getUri()
	));

	$view->getEnvironment()->addGlobal('auth',[
		'check' => $container->auth->check(),
		'user' => $container->auth->user()
	]);

	$view->getEnvironment()->addGlobal('flash',$container->flash);

	return $view;
};

$container['upload_directory'] = __DIR__ . '/../attach';

$container['validator'] = function ($container) {
	//return new App\Validation\Validator;

    return new Awurth\SlimValidation\Validator();
};


$container['IndexController'] = function($container) {
	return new \App\Controllers\IndexController($container);
};

$container['AuthController'] = function($container) {
	return new \App\Controllers\Auth\AuthController($container);
};

$container['TemplateController'] = function($container) {
    return new App\Controllers\Dashboard\TemplateController($container);
};

$container['SubscribersController'] = function($container) {
    return new \App\Controllers\Dashboard\SubscribersController($container);
};

$container['CategoryController'] = function($container) {
    return new \App\Controllers\Dashboard\CategoryController($container);
};

$container['UsersController'] = function($container) {
    return new \App\Controllers\Dashboard\UsersController($container);
};

$container['LogController'] = function($container) {
    return new \App\Controllers\Dashboard\LogController($container);
};

$container['SettingsController'] = function($container) {
    return new \App\Controllers\Dashboard\SettingsController($container);
};

$container['DataTableController'] = function($container) {
    return new \App\Controllers\Dashboard\DataTableController($container);
};

$container['PasswordController'] = function($container) {
	return new \App\Controllers\Auth\PasswordController($container);
};

$container['csrf'] = function($container) {
	return new \Slim\Csrf\Guard;
};

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));

$app->add($container->csrf);

v::with('App\\Validation\\Rules\\');

require __DIR__ . '/../app/routes.php';
