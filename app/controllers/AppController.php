<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "29.12.2023"
 * Время создания = "15:07"
 **/

namespace app\controllers;

use expenses\base\Controller;

/**
 * Class AppController
 * @package app\controllers
 * Контроллер этого приложения
 */
class AppController extends Controller
{

  public function __construct($route)
  {
    parent::__construct($route);
    //new AppModel();
  }

}