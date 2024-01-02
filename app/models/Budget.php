<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "01.01.2024"
 * Время создания = "12:31"
 **/

namespace app\models;

use app\models\AppModel;
use R;

/**
 * Модель работы с БД по Бюджетным Операциям (БО)
 */
class Budget extends AppModel
{

  /**
   * Поля таблицы для заполнения
   * @var array
   */
  public $attributes = [
    'scenario' => '',
    'month_exp' => '',
    'month_pay' => '',
    'number' => '',
    'summa' => '',
    'vat' => '',
    'budget_item_id' => 0,
    'status' => '',
    'description' => '',
  ];

  /**
   * Возвращает данные БО по ее идентификатор или все БО
   * @param int $id_bo идентификатор БО
   * @param string $scenario сценарий
   * @return array|false
   */
  public function getBudget($id_bo = false, $scenario = false)
  {
    $sql = "SELECT budget.*, budget_items.name_budget_item FROM budget INNER JOIN budget_items ON budget.budget_item_id = budget_items.id WHERE status = 'Согласован' ";
    if ($id_bo) {
      $bo = R::getAssocRow($sql . "AND budget.id = ?", [$id_bo]);
      if ($bo) return $bo[0];
    } else {
      if ($scenario) {
        $bo = R::getAssocRow($sql . "AND scenario = ? ORDER BY number", [$scenario]);
      } else {
        $bo = R::getAssocRow($sql . "ORDER BY scenario, number");
      }
      if ($bo) return $bo;
    }
    return false;
  }

  /**
   * Возвращает массив оплат использующих БО
   * @param int $id Идентификатор БО
   * @return array
   */
  public function getPayment(int $id_bo): array {
    // Создаем объекты для работы с БД.
    $payment_model = new Payment(); // Для заявок на оплату.
    // Получаем заявки на оплату использующие конкретную БО.
    $pay_arrays = $payment_model->getPayment(null, $id_bo);
    $payments = []; //$pays = [];
    //$number = '%' . $number . '%';
    //$pay_arrays = R::find('payment', 'num_bo LIKE ? ORDER BY date_pay', [$number]);
    foreach ($pay_arrays as $k => $v) {

      // проходим по всем атрибутам
      foreach ($this->payment as $name => $value) {
        // если в переданных данных data есть имя ключа атрибута
        if (isset($pay_array[$name])) {
          // запоминаем в атрибуте соответсвующее значение
          $pays[$name] = $pay_array[$name];
          if ($name == 'partner') {
            $pays[$name] = $this->getBudgetPartner($pay_array[$name]);
          }
        }
      }
      $payments[] = $pays;
    }
    return $payments;
  }

}