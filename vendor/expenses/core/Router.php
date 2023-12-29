<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "27.12.2023"
 * Время создания = "9:40"
 **/

namespace expenses;

/**
 * Класс маршрутизатора
 */
class Router
{

  /**
   * Таблица всех маршрутов
   * @var array
   */
  protected static $routes = [];

  /**
   * Текущий маршрут
   * @var array
   */
  protected static $route = [];

  /**
   * Записывает правило в таблицу маршрутов
   * @param $regexp string Шаблон которому должен соответствовать адрес
   * @param $route array Опция для хранения специфики маршрута
   */
  public static function add($regexp, $route = [])
  {
    self::$routes[$regexp] = $route;
  }

  /**
   * Метод для тестирования
   * @return array возвращает всю таблицу маршрутов
   */
  public static function getRoutes()
  {
    return self::$routes;
  }

  /**
   * Метод для тестирования
   * @return array возвращает текущий маршрут
   */
  public static function getRoute()
  {
    return self::$route;
  }

  /**
   * Метод вызывающий соответствующие маршруту Controller и Action
   * @param $url string Запрошенный url-адрес
   */
  public static function dispatch($url)
  {
    $url = self::removeQueryString($url);
    if (self::matchRoute($url)) { // Если url-адрес совпадает с маршрутом из таблицы маршрутов
      // формируем имя Controller с постфиксом Controller
      $controller = 'app\controllers\\' . self::$route['prefix'] . self::$route['controller'] . 'Controller';
      // проверяем сущществует ли класс Контроллера
      if (class_exists($controller)) { // если класс Controller существует
        // создаем объект каласса и передаем в него параметры (текущий маршрут)
        $controllerObject = new $controller(self::$route);
        // формируем имя Action с постфиксом Action
        $action = self::lowerCamelCase(self::$route['action']) . 'Action';
        // Проверяем сущаствует ли такой метод в классе Controller
        if (method_exists($controllerObject, $action)) { // если метод существует вызываем его
          $controllerObject->$action();
          $controllerObject->getView();
        } else { // если метод не существует - Ошибка
          throw new \Exception("Метод $controller::$action не найден", 404);
        }
      } else { // если класса Controller нет - Ошибка
        throw new \Exception("Контроллер $controller не найден", 404);
      }
    } else { // если класса Controller нет - Ошибка
      throw new \Exception("Страница не найдена", 404);
    }
  }

  /**
   * Метод ищет соответствие адреса в таблице маршрутов
   */
  public static function matchRoute($url)
  {
    foreach (self::$routes as $pattern => $route)
    {
      if (preg_match("#{$pattern}#i", (string)$url, $matches)) {
        // если найдено соответствие
        foreach ($matches as $k => $v) {   // проходимся по всему массиву
          if (is_string($k)) {   // если ключем является строка
            // создадим переменную и поместим в нее значения ключа
            $route[$k] = $v;
          }
        }
        if (!isset($route['action'])) { // если не указан Action
          $route['action'] = 'index'; // Action по умолчанию index
        }
        if (!isset($route['prefix'])) { // если у нас не существует Prefix
          $route['prefix'] = ''; // создаем его но пустым
        } else { // если Prefix существует
          $route['prefix'] .= '\\'; // добавляем обратный слеш
        }
        $route['controller'] = self::upperCamelCase($route['controller']);
        self::$route = $route;
        return true;
      }
    }
    return false;
  }

  /**
   * Метод приводит строку к формату CamelCase для имен Controller
   * @param $name string имя Controller для приведения
   * @return string имя Controller в формате CamelCase
   */
  protected static function upperCamelCase($name)
  {
    return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
  }

  /**
   * Метод приводит строку к формату camelCase для имен Action
   * @param $name string имя Action для приведения
   * @return string имя Action в формате CamelCase
   */
  protected static function lowerCamelCase($name)
  {
    return lcfirst(self::upperCamelCase($name));
  }

  /**
   * Метод для вырезания GET-параметров
   * @param $url string адресная строка
   * @return string|void
   */
  protected static function removeQueryString($url)
  {
    if ($url)
    {
      // если параметр не пуст разбиваем на две части
      // неявные и явные GET-параметры
      $params = explode('&', $url, 2);
      // Проверяем первую часть
      if (false === strpos($params[0], '='))
      {
        // если в ней отсутствует знак =
        // возвращаем эту часть без концевого слеша
        return rtrim($params[0], '/');
      }
      else
      {
        return '';
      }
    }
  }

}