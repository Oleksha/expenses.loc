<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "26.12.2023"
 * Время создания = "10:29"
 **/

namespace expenses;

/**
 * Класс нашего приложения
 */
class App
{

  public static $app;

  /**
   * Конструктор класса приложения
   */
  public function __construct() {
    $query = trim($_SERVER['QUERY_STRING'], '/');
    session_start();
    //set_time_limit(0);
    self::$app = Registry::instance();
    $this->getParams();
    /*new ErrorHandler();
    Router::dispatch($query);*/
  }

  /**
   * Загружаем параметры нашего приложения
   */
  protected function getParams() {
    // Получаем массив с параметрами
    $params = require_once  CONF . '/params.php';
    if (!empty($params)) { // Если массив не пуст загружаем параметры
      foreach ($params as $k => $v) {
        self::$app->setProperty($k, $v);
      }
    }
  }

}