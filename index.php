<?php

require 'mini/Mini.php';

\mini\Mini::registerAutoloader();

$app = new \mini\Mini();

$app->get('/fu/:id', function ($id) {
	echo 'GET fu #' . $id;
})->conditions(array('id' => '\d+'));

$app->get('/name/:foo/:bar', function ($foo, $bar) {
	echo 'GET names ' . $foo . ' ' . $bar;
});

$app->get('/team/:members+', function ($members) use ($app) {
	echo $app->container->request->method();
	foreach ($members as $member) {
		echo $member . "\n";
	}
});

$app->post('/tag/create', function () {
	echo 'POST tag id = ';
});

$app->run();