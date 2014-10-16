<?php

require 'mini/AutoLoader.php';

mini\AutoLoader::register();

$app = new mini\Mini();

$app->get('/', function () use ($app) {
	return 'Welcome!';
});

$app->get('/info', function () use ($app) {
	var_dump($app->environment);
	print_r(get_class_methods(new \Mini\Mini()));
});

$app->get('/tag/:id', function ($id) use ($app) {
	return $app->request->getMethod() . ' ' . $id;
});

$app->post('/tag/add', function () use ($app) {
	return $app->request->getMethod();
});

$app->put('/tag/create/', function () {
	return $app->request->getMethod();
});

$app->patch('/tag/update/:id', function ($id) use ($app) {
	return $app->request->getMethod() . ' ' . $id;
});

$app->delete('/tag/delete/:id', function ($id) use ($app) {
	return $app->request->getMethod() . ' ' . $id;
})->conditions(array('id' => '\d+'));

$app->get('/tag/:tags+', function ($tags) use ($app) {
	$out =  $app->request->getMethod();
	$out .= ' ';
	foreach ($tags as $tag) {
		$out .= $tag . "\n";
	}
	return $out;
});

$app->run();
