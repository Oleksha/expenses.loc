<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "29.12.2023"
 * Время создания = "15:18"
 **/
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <?= $this->getMeta(); ?>
</head>
<body>
<h1>Шаблон DEFAULT</h1>
<?= /** @var string $content */ $content; ?>
<?php
$logs = \R::getDatabaseAdapter()
  ->getDatabase()
  ->getLogger();

print_r( $logs->grep( 'SELECT' ) );
?>
</body>
</html>
