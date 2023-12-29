<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "27.12.2023"
 * Время создания = "10:21"
 **/

namespace app\controllers;

/**
 * Контроллер главной страницы приложения
 */
class MainController extends AppController
{

  /**
   * Вывод страницы по умолчанию
   */
  public function indexAction()
  {
    $this->setMeta('Главная страница', 'Описание...', 'Ключевые слова...');
  }

}