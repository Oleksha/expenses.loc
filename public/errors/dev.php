<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Произошла неизвестная ошибка!....</title>
  <link rel="stylesheet" href="/errors/css/style.css">
</head>
<body>
  <h1>Возникла ошибка</h1>
  <div class="main">
    <h2><?= /** @var integer $responce */ $responce ?></h2>
    <p><span class="info">Код ошибки: </span><?= /** @var string $errno */ $errno ?></p>
    <p><span class="info">Текст ошибки: </span><?= /** @var string $errstr */ $errstr ?></p>
    <p><span class="info">Файл, в котором ошибка: </span><?= /** @var string $errfile */ $errfile ?></p>
    <p><span class="info">Строка, в которой ошибка: </span><?= /** @var string $errline */ $errline ?></p>
  </div>
  <div class="copyright-w3-agile">
      <p> © 2024 Smooth Error Page. Все права защищены | Разработано <a href="https://w3layouts.com/" target="_blank">W3layouts</a></p>
  </div>
</body>
</html>

