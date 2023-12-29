<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "29.12.2023"
 * Время создания = "15:01"
 **/

namespace expenses\base;

/**
 * Class Controller - базовый класс код из которого будет выполняться во
 * всех других контроллерах через контролеер приложения AppController
 */
abstract class Controller
{

  /**
   * Данные о текущем маршруте
   * @var array
   */
  public $route;
  /**
   * Текущий Controller
   * @var string
   */
  public $controller;
  /**
   * Текущая Model
   * @var string
   */
  public $model;
  /**
   * Текущий View
   * @var string
   */
  public $view;
  /**
   * Текущий шаблон
   * @var string
   */
  public $layout;
  /**
   * Текущий префикс
   * @var string
   */
  public $prefix;
  /**
   * Данные передающиеся из контроллера в вид
   * @var array
   */
  public $data = [];
  /**
   * Метаданные которые будут передаваться из контроллера в вид
   * title - заголовок страницы
   * description - описание страницы
   * keywords - ключевые слова используемые на странице
   * @var array
   */
  public $meta = ['title' => '', 'description' => '', 'keywords' => ''];

  /**
   * Конструктор класса
   * @param $route array текщий маршрут
   */
  public function __construct($route)
  {
    $this->route = $route;
    $this->controller = $route['controller'];
    $this->model = $route['controller'];
    $this->view = $route['action'];
    $this->prefix = $route['prefix'];
  }

  /**
   * Получает объект вида и вызывает его метод Render
   */
  public function getView()
  {
    $viewObject = new View($this->route, $this->layout, $this->view, $this->meta);
    $viewObject->render($this->data);
  }

  /**
   * Запись данных для передачи в вид
   * @param array $data
   */
  public function set($data)
  {
    $this->data = $data;
  }

  /**
   * Запись meta-данных для передачи в вид
   * @param $title string Заголовок страницы
   * @param $desc string Описание  страницы
   * @param $keywords string Ключевые слова
   */
  public function setMeta($title = '', $desc = '', $keywords = '')
  {
    $this->meta['title'] = $title;
    $this->meta['description'] = $desc;
    $this->meta['keywords'] = $keywords;
  }

  public function isAjax()
  {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
  }

  /**
   * Загрузчик вида
   * @param $view string самм вид который требуется загрузить
   * @param $vars array набор параметров для передачи в вид
   */
  public function loadView($view, $vars = [])
  {
    extract($vars);
    require APP . "/views/{$this->prefix}{$this->controller}/{$view}.php";
    die; // завершаем работу программы
  }

}