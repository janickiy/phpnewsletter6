<?php

use Respect\Validation\Validator as v;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Translator;

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

    $view->getEnvironment()->addGlobal('_session', $_SESSION);
    $view->getEnvironment()->addGlobal('_post', $_POST);
    $view->getEnvironment()->addGlobal('_get', $_GET);
	$view->getEnvironment()->addGlobal('flash',$container->flash);

    $getSetting = new \Twig\TwigFunction('get_setting', function ($key = '') {
        $setting = \App\Models\Settings::where('name', $key)->first();

        if ($setting) {
            return $setting->value;
        } else {
            return '';
        }
    });

    $view->getEnvironment()->addFunction($getSetting);

    $lng = \App\Models\Settings::where('name', 'LANGUAGE')->first();
    $setLng = $lng ? $lng->value : 'en';
    $view->getEnvironment()->addGlobal('lng', $setLng);
    $translator = new Translator($setLng);
    $translator->setFallbackLocales([$setLng]);
    $translator->addLoader('php', new PhpFileLoader());
    $translator->addResource('php', __DIR__ . '/../resources/lang/' . $setLng . '.php', $setLng);

    $getTrans = new \Twig\TwigFunction('trans', function ($name) use ($translator) {
        return $translator->trans($name);
    });

    $view->getEnvironment()->addFunction($getTrans);

	return $view;
};

$view = $app->getContainer()['view'];
$view->getEnvironment()->addGlobal('url', function ($url) {
    return url($url);
});

$container['upload_directory'] = __DIR__ . '/../attach';
$container['keyprivate'] = __DIR__ . '/../keyprivate';
$container['tmp'] = __DIR__ . '/../tmp';

$container['validator'] = function ($container) {
    return new Awurth\SlimValidation\Validator();
};

$container['ErrosController'] = function($container) {
	return new \App\Controllers\ErrosController($container);
};

$container['FrontendController'] = function($container) {
    return new \App\Controllers\FrontendController($container);
};

$container['AuthController'] = function($container) {
	return new \App\Controllers\Auth\AuthController($container);
};

$container['TemplateController'] = function($container) {
    return new App\Controllers\Dashboard\TemplateController($container);
};

$container['ScheduleController'] = function($container) {
    return new App\Controllers\Dashboard\ScheduleController($container);
};

$container['SubscribersController'] = function($container) {
    return new \App\Controllers\Dashboard\SubscribersController($container);
};

$container['CategoryController'] = function($container) {
    return new \App\Controllers\Dashboard\CategoryController($container);
};

$container['SmtpController'] = function($container) {
    return new \App\Controllers\Dashboard\SmtpController($container);
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

$container['AjaxController'] = function($container) {
	return new \App\Controllers\Dashboard\AjaxController($container);
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