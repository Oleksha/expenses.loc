<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "31.12.2023"
 * Время создания = "15:00"
 **/

namespace app\models;

use R;

/**
 * Модель связи с БД - Контрагенты
 */
class Partner extends AppModel
{

  /**
   * Массив полей таблицы для заполнения
   * @var array
   */
  public $attributes = [
    'name' => '',
    'alias' => '',
    'type_id' => 0,
    'inn' => null,
    'kpp' => null,
    'bank' => null,
    'bic' => null,
    'account' => null,
    'address' => null,
    'phone' => null,
    'email' => null,
    'delay' => null,
    'vat' => null,
  ];

  /**
   * Возвращает массив данных о КА по идентификатору или о всех если он не указан
   * @param bool|int $id идентификатор КА или null
   * @return array|false
   */
  public function getPartner(bool|int $id = false): bool|array
  {
    if ($id !== false) {
      $partner = R::getAssocRow('SELECT * FROM partner WHERE id = ? LIMIT 1', [$id]);
      if (!empty($partner)) return $partner[0];
    } else {
      $partner = R::getAssocRow('SELECT * FROM partner ORDER BY name');
      if (!empty($partner)) return $partner;
    }
    return false;
  }

  /**
   * Проверяет наличие имеющихся КА с такими inn или alias
   * @return bool TRUE если inn и alias свободны, и FALSE если заняты
   */
  public function checkUnique(): bool
  {
    // Попытаемся найти в БД пользователя с таким inn или alias
    $ka = R::getRow('SELECT * FROM partner WHERE inn = ? OR alias = ?', [$this->attributes['inn'], $this->attributes['alias']]);
    if ($ka) {
      // если нашли такую запись
      if ($ka['inn'] == $this->attributes['inn']) {
        // совпадает inn
        $this->errors['unique'][] = "C таким ИНН ({$ka['inn']}) в БД существует КА ({$ka['name']})...";
      }
      if ($ka['alias'] == $this->attributes['alias']) {
        // совпадает alias
        $this->errors['unique'][] = "C таким номером ({$ka['alias']}) в БД существует КА ({$ka['name']})...";
      }
      return false;
    }
    return true;
  }

}