<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "31.12.2023"
 * Время создания = "14:48"
 **/

namespace app\models;

use R;

/**
 * Модель связи с БД - Поступления
 */
class Receipt extends AppModel
{

  /**
   * Массив полей таблицы для заполнения
   * @var array
   */
  public $attributes = [
    'date' => '',
    'number' => '',
    'sum' => '',
    'type' => '',
    'vat_id' => '',
    'id_partner' => 0,
    'num_doc' => '',
    'date_doc' => '',
    'note' => null,
    'num_pay' => null,
    'pay_id' => null,
    'date_pay' => null,
  ];

  /**
   * Возвращает массив всех приходов отсортированных по дате
   * @param string $field поле по которому происходит отбор
   * @param mixed $value значение по которому происходит отбор
   * @return array|false
   */
  public function getReceipt(string $field, mixed $value): bool|array
  {
    $receipts = R::getAssocRow("SELECT * FROM receipt WHERE $field = ? ORDER BY date", [$value]);
    if (!empty($receipts)) return $receipts;
    return false;
  }

  /**
   * Возвращает массив для главной страницы если он есть
   * @return array|false
   */
  public function getReceiptForMain(): bool|array
  {
    $receipts = R::getAssocRow('SELECT * FROM receipt WHERE (date_pay is NULL) OR (date_pay = CURDATE())');
    if (!empty($receipts)) return $receipts;
    return false;
  }

  /**
   * Возвращает массив неоплаченных приходов для КА
   * @param int $id идентификатор КА
   * @return array|false
   */
  public function getReceiptNoPay(int $id): bool|array
  {
    $receipts = R::getAssocRow('SELECT * FROM receipt WHERE id_partner = ? AND date_pay IS NULL ORDER BY date', [$id]);
    if (!empty($receipts)) return $receipts;
    return false;
  }

}