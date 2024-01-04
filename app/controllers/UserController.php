<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "30.12.2023"
 * Время создания = "15:54"
 **/

namespace app\controllers;

use app\models\User;

/**
 * Контроллер взаимодействия с пользователями
 */
class UserController extends AppController
{
  /**
   * Используем для вывода страницы шаблон по умолчанию
   * @var string */
  public $layout = 'authorization';

  /**
   * Action страницы авторизации
   */
  public function loginAction(): void
  {
    // проверяем приходят на страницу какие-нибудь данные или нет
    if (!empty($_POST)) {
      // если данные пришли, создаем объект пользователя
      $user_models = new User();
      if ($user_models->login()) {
        redirect('/');
      } else {
        $_SESSION['errors'] = 'Логин/Пароль введены не верно';
        redirect();
      }
    }
    /** Формируем метатеги для страницы */
    $this->setMeta('Авторизация пользователя');
  }
  /**
   * Action страницы регистрации
   */
  public function signupAction(): void
  {
    if (!empty($_POST)) {
      $user_models = new User();
      $data = $_POST;
      $user_models->load($data);
      if (!$user_models->validate($data) || !$user_models->checkUnique()) {
        $user_models->getErrors();
        $_SESSION['form_data'] = $data;
      } else {
        $user_models->attributes['password'] = password_hash($user_models->attributes['password'], PASSWORD_DEFAULT);
        if ($user_models->save('user')) {
          $_SESSION['success'] = 'Пользователь зарегистрирован';
          redirect('/user/login');
        } else {
          $_SESSION['errors'] = 'Возникла ошибка сохранения данных в БД';
        }
      }
      redirect();
    }
    /** Формируем метатеги для страницы */
    $this->setMeta('Регистрация нового пользователя');

  }

  /**
   * Action страницы выхода текущего пользователя
   */
  public function logoutAction(): void
  {
    if (isset($_SESSION['user'])) unset($_SESSION['user']);
    redirect();
  }

}
