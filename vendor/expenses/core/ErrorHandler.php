<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "26.12.2023"
 * Время создания = "11:32"
 **/

namespace expenses;

/**
 * Класс обработки ошибок
 */
class ErrorHandler
{

  /**
   * Конструктор класса
   */
  public function __construct() {
    if (DEBUG) {
      // если включен режим разработки показываем все ошибки
      error_reporting(-1);
    } else {
      // иначе ошибки не показываем
      error_reporting(0);
    }
    // обрабатываем ошибки
    set_exception_handler([$this, 'exceptionHandler']);
  }

  /**
   * Метод обрабатывающий перехваченные исключения
   * @param $e
   */
  public function exceptionHandler($e) {
    $this->logErrors($e->getMessage(), $e->getFile(), $e->getLine());
    $this->displayError('Исключение', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
  }

  /**
   * Метод для логирования ошибок
   * @param string $message описание ошибки
   * @param string $file наименование файла
   * @param string $line номер строки содержащий ошибку
   */
  protected function logErrors($message = '', $file = '', $line = '') {
    error_log("[" . date('Y-m-d H:i:s') . "] Текст ошибки: {$message} | Файл: {$file} | Строка: {$line}\n--------------------\n", 3, ROOT . '/tmp/errors.log');
  }

  /**
   * Метод показывающий ошибку
   * @param $errno
   * @param $errstr
   * @param $errfile
   * @param $errline
   * @param $responce
   */
  protected function displayError($errno, $errstr, $errfile, $errline, $responce = 404) {
    http_response_code($responce);
    if ($responce == 404 && !DEBUG) {
      require WWW . '/errors/404.php';
      die;
    }
    if (DEBUG) {
      require WWW . '/errors/dev.php';
    } else {
      require WWW . '/errors/prod.php';
    }
    die;
  }

}