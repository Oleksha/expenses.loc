<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "31.12.2023"
 * Время создания = "14:58"
 **/

namespace app\models;

use app\models\AppModel;
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
   * @param int $id идентификатор заявки
   * @return array|false
   */
  public function getPayment($id = false, $id_bo = false)
  {
    if ($id) {
      $payments = R::getAssocRow('SELECT * FROM payment WHERE id = ?', [$id]);
      if (!empty($payments)) return $payments[0];
    } else {
      if ($id_bo) {
        $payments_all = R::getAssocRow('SELECT * FROM payment');
        foreach ($payments_all as $item) {
          $ids = explode(';', $item['bos_id']);
          if (in_array($id_bo, $ids)) $payments[] = $item;
        }
      } else {
        $payments = R::getAssocRow('SELECT * FROM payment');
      }
      if (!empty($payments)) return $payments;
    }
    return false;
  }


}