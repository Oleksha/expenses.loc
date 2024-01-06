<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "06.01.2024"
 * Время создания = "10:21"
 **/

namespace app\models;

use R;

/**
 * Модель связи с БД - Ставки НДС
 */
class Vat extends AppModel
{

  /**
   * Содержит поля таблицы vat
   * @var string массив
   */
  public $attributes = [
    'vat' => 0.00,
    'name' => '',
  ];

  /**
   * Возвращает массив данных о ставке НДС
   * @param bool|int $id идентификатор ставки
   * @return array|false
   */
  public function getVat(bool|int $id = false): bool|array
  {
    if ($id) {
      $vat = R::getAssocRow('SELECT * FROM vat WHERE id = ? LIMIT 1', [$id]);
      if (!empty($vat)) return $vat[0];
    } else {
      $vat = R::getAssocRow('SELECT * FROM vat');
      if (!empty($vat)) return $vat;
    }
    return false;
  }

}