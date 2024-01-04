<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "30.12.2023"
 * Время создания = "16:06"
 **/

namespace app\models;

use R;

/**
 * Модель взаимодействия с данными о пользователях
 */
class User extends AppModel
{

  /**
   * Массив свойств модели (идентичен полям базы данных)
   * @var array
   */
  public $attributes = [
    'login' => '',
    'password' => '',
    'email' => '',
    'name' => '',
    'avatar' => '',
    'role' => '',
  ];

  /**
   * Содержит правила проверки формы
   * @var string многомерный массив
   */
  public $rules = [
    'required' => [
      ['login'],
      ['password'],
      ['name'],
      ['email'],
    ],
    'email' => [
      ['email'],
    ],
    'equals' => [
      ['password', 'password_confirm']
    ]
  ];

  /**
   * Проверяет наличие имеющихся пользователей с такими login или email
   * @return bool TRUE если login иЛи email свободны, и FALSE если заняты
   */
  public function checkUnique(): bool {
    // попытаемся найти в БД пользователя с таким login или email
    $user = R::getRow("SELECT * FROM user WHERE login = ? OR email = ?", [$this->attributes['login'], $this->attributes['email']]);
    if ($user) {
      // если нашли такую запись
      if ($user['login'] == $this->attributes['login']) {
        // совпадает inn
        $this->errors['unique'][] = "Логин ({$user['login']}) уже занят";
      }
      if ($user['email'] == $this->attributes['email']) {
        // совпадает alias
        $this->errors['unique'][] = "Email ({$user['email']}) уже занят";
      }
      return false;
    }
    return true;
  }

  /**
   * Функция авторизации пользователя
   * @param $isAdmin bool Администратор или Пользователь
   * @return bool
   */
  public function login(bool $isAdmin = false): bool {
    /** Получаем введенные данные пользователя */
    $login = !empty(trim($_POST['login'])) ? trim($_POST['login']) : null;
    $password = !empty(trim($_POST['password'])) ? trim($_POST['password']) : null;
    if ($login && $password) {
      /** Если есть и логин и пароль */
      if ($isAdmin) {
        /** Если это администратор - попытаемся найти в БД пользователя с таким login и статусом Администратор */
        $user = R::getRow("SELECT * FROM user WHERE login = ? AND role = 'admin' LIMIT 1", [$login]);
      } else {
        /** Если это обычный пользователь - попытаемся найти в БД пользователя с таким login */
        $user = R::getRow("SELECT * FROM user WHERE login = ? LIMIT 1", [$login]);
      }
      if ($user) {
        /** Если пользователь найден проверяем пароль */
        if (password_verify($password, $user['password'])) {
          /** Если пароль совпадает - авторизуем пользователя */
          foreach ($user as $k => $v) {
            /** Записываем в сессию все данные о пользователе кроме пароля */
            if ($k != 'password') $_SESSION['user'][$k] = $v;
          }
          return true;
        }
      }
    }
    return false;
  }

}