<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "30.12.2023"
 * Время создания = "16:11"
 **/
?>
<main class="form-sign-in">
  <form action="user/login" method="post" class="was-validated">
    <h1 class="mb-3">Вход в систему</h1>
    <div class="form-floating mb-3">
      <input type="text" name="login" class="form-control" id="floatingName" placeholder="Имя пользователя" required>
      <label for="floatingName">Имя пользователя</label>
    </div>
    <div class="form-floating mb-3">
      <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
      <label for="floatingPassword">Введите пароль</label>
    </div>
    <button class="w-100 btn btn-lg btn-primary mb-3" type="submit">Войти</button>
    <p class="text-center">У вас нет аккаунта? <a href="/user/signup">Зарегистрируйтесь</a></p>
  </form>
</main>
