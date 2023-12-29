<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "29.12.2023"
 * Время создания = "15:07"
 **/

namespace app\controllers;

use app\models\AppModel;
use expenses\base\Controller;

/**
 * Контроллер этого приложения
 */
class AppController extends Controller
{

  /**
   * Конструктор класса
   */
  public function __construct($route)
  {
    parent::__construct($route);
    new AppModel();
  }

}