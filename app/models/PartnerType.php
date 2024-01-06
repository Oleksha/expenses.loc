<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "04.01.2024"
 * Время создания = "16:16"
 **/

namespace app\models;

use R;

/**
 * Модель связи с БД - Типы контрагентов
 */
class PartnerType extends AppModel
{

  /**
   * Содержит поля таблицы partner_type
   * @var string массив
   */
  public $attributes = [
    'name' => '',
  ];

  /**
   * Возвращает массив данных о типе контрагента
   * @param bool|int $id идентификатор типа
   * @return array|false
   */
  public function getPartnerType(bool|int $id = false): bool|array
  {
    if ($id) {
      $type = R::getAssocRow('SELECT * FROM partner_type WHERE id = ? LIMIT 1', [$id]);
      if (!empty($type)) return $type[0];
    } else {
      $type = R::getAssocRow('SELECT * FROM partner_type');
      if (!empty($type)) return $type;
    }
    return false;
  }

}