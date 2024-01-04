<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "01.01.2024"
 * Время создания = "13:35"
 **/

namespace app\models;

use R;

/**
 * Модель связи с БД - Бюджетные статьи расхода
 */
class BudgetItems extends AppModel
{

  /**
   * Массив полей таблицы для заполнения
   * @var array
   */
  public $attributes = [
    'name_budget_items' => '',
    'choice' => '',
    'name_old' => '',
  ];

  /**
   * Возвращает массив данных о статьях расхода по идентификатору или о всех если он не указан
   * @param bool|int $id идентификатор статьи расхода
   * @return array|false
   */
  public function getBudgetItems(bool|int $id = false): bool|array
  {
    if ($id !== false) {
      $budgetItems = R::getAssocRow('SELECT * FROM budget_items WHERE id = ? LIMIT 1', [$id]);
      if (!empty($budgetItems)) return $budgetItems[0];
    } else {
      $budgetItems = R::getAssocRow('SELECT * FROM budget_items ORDER BY name_budget_item');
      if (!empty($budgetItems)) return $budgetItems;
    }
    return false;
  }

}