<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "31.12.2023"
 * Время создания = "14:58"
 **/

namespace app\models;

use R;

/**
 * Модель работы с БД по Оплатам
 */
class Payment extends AppModel
{

  /**
   * Массив полей таблицы для заполнения
   * @var array
   */
  public $attributes = [
    'date' => '',
    'number' => '',
    'sum' => '',
    'receipt' => '',
    'receipts_id' => '',
    'vat' => '',
    'id_partner' => 0,
    'num_er' => null,
    'ers_id' => null,
    'sum_er' => null,
    'num_bo' => null,
    'bos_id' => null,
    'sum_bo' => null,
    'date_pay' => null,
  ];

  /**
   * Возвращает массив всех заявок на оплату или конкретной заявки
   * @param bool|int $id идентификатор заявки
   * @param bool|int $id_bo идентификатор бюджетной операции
   * @param bool|int $id_er идентификатор единоличного решения
   * @param bool|int $id_receipt идентификатор поступления
   * @return bool|array
   */
  public function getPayment(bool|int $id = false, bool|int $id_bo = false, bool|int $id_er = false, bool|int $id_receipt = false): bool|array
  {
    if ($id) {
      $payments = R::getAssocRow('SELECT * FROM payment WHERE id = ?', [$id]);
      if (!empty($payments)) return $payments[0];
    } else {
      $payments_all = R::getAssocRow('SELECT * FROM payment');
      if ($id_bo) {
        foreach ($payments_all as $item) {
          $ids = explode(';', $item['bos_id']);
          if (in_array($id_bo, $ids)) $payments[] = $item;
        }
      } elseif ($id_er) {
        foreach ($payments_all as $payment) {
          if (in_array($id_er, explode(';', $payment['ers_id']))) $payments[] = $payment;
        }
        if (!empty($payments)) return $payments;
        return false;
      } elseif ($id_receipt) {
        foreach ($payments_all as $payment) {
          if (in_array($id_receipt, explode(';', $payment['receipts_id']))) $payments[] = $payment;
        }
        if (!empty($payments)) return $payments[0];
        return false;
      }
      else {
        $payments = $payments_all;
      }
      if (!empty($payments)) return $payments;
    }
    return false;
  }


}