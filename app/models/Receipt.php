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
    'vat_id' => 0,
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
   * Функция возвращающая массив полных данных по приходам
   * @param array $ids Строка ID приходов оплачиваемых данной ЗО
   * @return array Полные данные о приходах
   */
  public function getReceipts(array $ids): array
  {
    $receipts = []; // Объявляем возвращаемый массив.
    // Проходимся по всем элементам полученного массива поступлений.
    foreach ($ids as $id) {
      $receipt_full = R::getAssocRow("SELECT * FROM receipt WHERE id = ?", [$id]);
      if (!empty($receipt_full))  $receipts[] = $receipt_full[0];
    }
    return $receipts;
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

  /**
   * Возвращает текущий тип поступления
   * (1 - просмотр - для уже оплаченных поступлений)
   * (2 - редактор - поданные на оплату, но еще не оплаченные поступления)
   * (3 - оплата - не поданные на оплату (по умолчанию))
   * @param int $id номер поступления товаров или услуг
   * @return int
   */
  public function isTypeReceipt(int $id): int  {
    $isType = 3;
    $receipt = $this->getReceipt('id', $id);
    if ($receipt) {
      $receipt = $receipt[0];
      if (!empty($receipt['date_pay'])) {
        $isType = 1;
      } elseif (!empty($receipt['pay_id'])) {
        $isType = 2;
      }
    }
    return $isType;
  }

}