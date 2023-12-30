<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "29.12.2023"
 * Время создания = "15:18"
 **/
if (!isset($_SESSION['user'])) {
  header('Location: /user/login');
}
?>
<!doctype html>
<html lang="ru" class="h-100">
<head>
  <base href="/">
  <link rel="shorcut icon" href="img/logo.png" type="image/png" />
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <?= $this->getMeta(); ?>
  <link rel="stylesheet" href="assets/bootstrap-5.3.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/fonts.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column h-100">
  <!-- Эмблема сайта -->
  <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="grid" fill="#ffffff" viewBox="0 0 16 16">
      <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"></path>
    </symbol>
  </svg>
  <!-- Эмблема сайта -->
  <!-- Меню сайта -->
  <header class="p-3 bg-dark text-white">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="<?=PATH;?>" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
          <svg class="bi me-2" width="40" height="32" role="img" aria-label="Grid"><use xlink:href="#grid"></use></svg>
        </a>
        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="<?=PATH;?>" class="nav-link px-2
            <?php echo ($this->route['controller'] == 'Main') ? ' text-secondary' : ' text-white'; ?>">Главная</a></li>
          <li><a href="<?=PATH;?>/partner" class="nav-link px-2 <?php echo ($this->route['controller'] == 'Partner') ? ' text-secondary' : ' text-white'; ?>">Контрагенты</a></li>
          <li><a href="<?=PATH;?>/budget" class="nav-link px-2 <?php
            echo ($this->route['controller'] == 'Budget') ? ' text-secondary' : ' text-white'; ?>">Бюджет</a></li>
          <?php if (isset($_SESSION['user'])): ?>
            <?php if ($_SESSION['user']['role'] == 'admin') : ?>
              <li><a href="/admin" class="nav-link px-2 text-white">Администрирование</a></li>
            <?php endif; ?>
          <?php endif; ?>
        </ul>
        <?php if (isset($_SESSION['user'])): ?>
          <?php
          if ($_SESSION['user']['avatar'] == '') {
            $img = '/upload/no-avatar.png';
          } else {
            $img = $_SESSION['user']['avatar'];
          }
          ?>
          <div class="dropdown text-end my-style">
            <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?=$img;?>" alt="<?=$_SESSION['user']['login'];?>" class="rounded-circle" width="32" height="32">
            </a>
            <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1" style="">
              <li><a class="dropdown-item" href="#">Профиль, <?=$_SESSION['user']['login'];?></a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="user/logout">Выход</a></li>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header>
  <!-- Меню сайта -->
  <!-- Содержимое страницы сайта -->
  <div class="content">
    <?= /** @var string $content */ $content;?>
  </div>
  <?php
  /*$logs = \R::getDatabaseAdapter()->getDatabase()->getLogger();
  print_r($logs->grep('SELECT'));*/
  ?>
  <!-- Содержимое страницы сайта -->
  <!-- Подвал сайта -->
  <footer class="footer mt-auto py-3">
    <div class="container text-center">
      <span class="text-muted">Разработано ИТО ОП «Тольятти» &copy; 2021-2024</span>
    </div>
  </footer>
  <!-- Подвал сайта -->
  <script type="text/javascript" src="assets/bootstrap-5.3.2-dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let path = '<?=PATH;?>';
  </script>
  <?php
  /** @var array $scripts */
  foreach ($scripts as $script) {
    echo $script;
  }
  ?>
</body>
</html>
