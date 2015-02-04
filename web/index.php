<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

$dbopts = parse_url(getenv('DATABASE_URL'));

$dbname = ltrim($dbopts['path'], '/');
$db = new PDO("pgsql:host=${dbopts['host']};port=${dbopts['port']};dbname=${dbname}", $dbopts['user'], $dbopts['pass']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register the Twig templating engine
$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__.'/../views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app->redirect('/sphere');
});

$app->get('/twig/{name}', function ($name) use ($app) {
  return $app['twig']->render('index.twig', array(
    'name' => $name,
  ));
});

$app->get('/sphere', function() use($app, $db) {
  $st = $db->prepare("SELECT * FROM people");
  $st->execute();

  $friends = [];
  $people = [];
  $result = $st->setFetchMode(PDO::FETCH_ASSOC);

  while($row = $st->fetch()) {
    $people[] = $row;
    $tmp_friend = $row['friends_with'];
    $tmp_st = $db->prepare("SELECT * FROM users WHERE name = :friends_with");
    $tmp_st->bindParam(':friends_with', $tmp_friend);
    $tmp_st->execute();

    $friends[$row['name']] = [];
    while($tmp_row = $tmp_st->fetch()) {
      $friends[$row['name']][] = $tmp_row['name'];
    }
  }
  return $app['twig']->render('sphere.twig', array(
    'people' => $people,
    'friends' => $friends
  ));
});

$app->get('/sphere/{token}', function($token) use($app, $db) {

  return $app['twig']->render('form.twig', array(
    'token' => $token
  ));
});

// Main Logic Goes here for handling the friends
$app->post('/sphere/create', function() use($app, $db) {
  $st = $db->prepare("SELECT name from people where token = :token");
  $st->bindParam(':token', $app['request']->get('token'));
  $st->execute();

  // set the resulting array to associative
  $result = $st->setFetchMode(PDO::FETCH_ASSOC);
  $people = $st->fetch();

  $st2 = $db->prepare("INSERT INTO users (name, email, friends_with) VALUES(:name, :email, :friends_with)");
  $st2->bindParam(':name', $name);
  $st2->bindParam(':email', $email);
  $st2->bindParam(':friends_with', $friends_with);

  $name = $app['request']->get('name');
  $email = $app['request']->get('email');
  $friends_with = $people['name'];
  $st2->execute();

  /*$st3 = $db->prepare("UPDATE people set friends_with = :new_friends_with where token = :token");
  $st3->bindParam(':new_friends_with', $new_friends_with);
  $st3->bindParam(':token', $token);
  $new_friends_with = $name;
  $token = $app['request']->get('token');

  $st3->execute();*/
  $st3 = $db->prepare("INSERT INTO users(name) VALUES(:name)");
  $st3->bindParam(':name', $new_friends_with);
  $new_friends_with = $name;
  $st3->execute();

  return $app->redirect('/sphere');
});

$app->get('/sphere/add/{name}', function($name) use($app, $db) {
  $st = $db->prepare("INSERT into people(name, token) VALUES(:name, :token)");
  $st->bindParam(':name', $name);
  $st->bindParam(':token', $token);

  $token = substr(md5($name),0,10);

  $st->execute();

  return $app['twig']->render('add.twig', array(
    'name' => $name,
    'token' => $token
  ));
});

$app->run();

?>
