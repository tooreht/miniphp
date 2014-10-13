<?php

// require 'rb.php';

// R::setup('mysql:host=localhost;port=8889;dbname=utag','root','root');

require 'mini/Mini.php';

\mini\Mini::registerAutoloader();

// $container = new \mini\utils\Container();
// $container->set('foo', 'bar');
// var_dump($container);

$app = new \mini\Mini();
// print_r(get_class_methods(new \Mini\Mini()));
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
	// $tag = R::dispense( 'tag' );
	// $tag->name = 'php';
	// $id = R::store($tag);
	// var_dump($tag);
	// echo 'POST tag id = ' . $id;
});

// var_dump($app->container->get('environment'));

$app->run();

// R::close();