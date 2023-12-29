<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "29.12.2023"
 * Время создания = "15:11"
 **/

namespace expenses\base;

/**
 * Базовый класс Вида
 */
class View
{

  /**
   * текущий маршрут и параметры (controller, action, params)
   * @var array
   */
  public $route;
  /**
   * текущий controller
   * @var string
   */
  public $controller;
  /**
   * текущая model
   * @var string
   */
  public $model;
  /**
   * текущий вид
   * @var string
   */
  public $view;
  /**
   * текущий шаблон
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
   * свойство для хранения вырезаемых скриптов
   * @var array
   */
  public $scripts = [];

  /**
   * Конструктор класса
   * @param array $route данные о текущем маршруте
   * @param string $layout Шаблон для вывода вида
   * @param string $view Вид встраиваемый в шаблон
   * @param array $meta метаданнные для страницы
   */
  public function __construct($route, $layout = '', $view = '', $meta)
  {
    $this->route = $route;
    $this->controller = $route['controller'];
    $this->model = $route['controller'];
    $this->view = $view;
    $this->prefix = $route['prefix'];
    $this->meta = $meta;
    if ($layout === false) {
      // Если шаблон не нужен
      $this->layout = false;
    } else {
      // Если шаблон нужен берем его, если он есть
      // инначе берем шаблон по умолчанию из настроек
      $this->layout = $layout ?: LAYOUT;
    }
  }

  /**
   * Метод формирующий страницу на основании данных $data
   * @param $data array
   */
  public function render($data)
  {
    // проверяем переданные данные являются массивом
    if (is_array($data)) extract($data);
    // формируем путь к view
    $viewFile = APP . "/views/{$this->prefix}{$this->controller}/{$this->view}.php";
    if (is_file($viewFile)) { // если файл существует
      // подключаем его, но не выводим сразу, а используем буфферизацию
      ob_start();
      require_once $viewFile;
      // поместим все что загрузили в переменную $content и очистим буффер
      $content = ob_get_clean();
    } else {   // если файла нет - ошибка
      throw new \Exception("Файл вида $viewFile не найден", 500);
    }
    if (false !== $this->layout) {   // если мы собираемя выводить шаблон
      // формируем путь к layout
      $layoutFile = APP . "/views/layouts/{$this->layout}.php";
      if (is_file($layoutFile)) {   // если файл найден вырезаем скрипты
        $content = $this->getScript($content);
        $scripts = []; //для хранения скриптов
        if (!empty($this->scripts[0])) {
          // если скрипты найдены преобразуем массив в одномерный
          $scripts = $this->scripts[0];
        }
        // и подключаем шаблон
        require_once $layoutFile;
      } else {   // если файла нет ошибка
        throw new \Exception("Шаблон {$this->layout} не найден", 500);
      }
    }
  }

  /**
   * Метод формирующий метатеги
   * @return string
   */
  public function getMeta()
  {
    $output = '<title>' . $this->meta['title'] . '</title>' . PHP_EOL;
    $output .= '    <meta name="description" content="' . $this->meta['description'] . '">' . PHP_EOL;
    $output .= '    <meta name="keywords" content="' . $this->meta['keywords'] . '">' . PHP_EOL;
    return $output;
  }

  /**
   * Находит и вырезает все касающееся скриптов из вида
   * @param $content string html-текст вида
   * @return string html-текст вида без скриптов
   */
  protected function getScript($content) {
    // переменная для хранения шаблона вдля поиска ивыпезания скриптов
    $pattern = "#<script.*?>.*?</script>#si";
    preg_match_all($pattern, $content, $this->scripts);
    if (!empty($this->scripts)) {
      // если скрипты найдены вырезаем их из вида
      $content = preg_replace($pattern, '', $content);
    }
    return $content;
  }

}