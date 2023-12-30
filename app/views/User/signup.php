<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "30.12.2023"
 * Время создания = "16:12"
 **/
?>
<main class="form-sign-in">
  <form action="/user/signup" method="post" enctype="multipart/form-data" class="was-validated">
    <h1 class="mb-3">Регистрация</h1>
    <div class="form-floating mb-3">
      <input type="text" name="name" class="form-control" id="name" placeholder="Иванов Иван Иванович" value="<?=$_SESSION['form_data']['name'] ?? ''?>" required>
      <label for="name">Полное имя</label>
    </div>
    <div class="form-floating mb-3">
      <input type="text" name="login" class="form-control" id="login" placeholder="ivan" value="<?=$_SESSION['form_data']['login'] ?? ''?>" required>
      <label for="login">Ваш логин</label>
    </div>
    <div class="form-floating mb-3">
      <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" value="<?=$_SESSION['form_data']['email'] ?? ''?>" required>
      <label for="email">Адрес электронной почты</label>
    </div>
    <div class="mb-3">
      <input type="file" name="avatar" class="form-control" id="avatar">
    </div>
    <div class="form-floating mb-3">
      <input type="password" name="password" class="form-control" id="password" placeholder="" required>
      <label for="password">Введите пароль</label>
    </div>
    <div class="form-floating mb-3">
      <input type="password" name="password_confirm" class="form-control" id="password_confirm" placeholder="" required>
      <label for="password_confirm">Подтвердите пароль</label>
    </div>
    <div class="text-center">
      <button class="w-100 btn btn-lg btn-primary mb-3" type="submit">Зарегистрироваться</button>
    </div>
    <p class="text-center">У вас есть аккаунт? <a href="/user/login">Авторизуйтесь</a></p>
    <?php if (isset($_SESSION['errors'])) : ?>
      <div class="alert alert-danger"><?php  echo $_SESSION['errors']; unset($_SESSION['errors']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])) : ?>
      <div class="alert alert-success"><?php  echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
  </form>
</main>
