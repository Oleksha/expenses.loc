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
   * Данные для отчета
   * @param string $scenario
   * @return array
   */
  public function getForReport(string $scenario): array
  {
    $bo_report = [];
    $bos = R::getAssocRow("SELECT budget.*, budget_items.name_budget_item, budget_items.choice FROM budget JOIN budget_items ON (budget.budget_item_id=budget_items.id) WHERE budget.status = 'Согласован' AND budget.scenario = ? AND budget_items.choice='1'  ORDER BY budget_items.name_budget_item", [$scenario]);
    foreach ($bos as $bo) {
      $summa = 0;
      $id = (int)$bo['id'];
      $coasts = $this->getPaymentCoast($id);
      foreach ($coasts as $coast) {
        $summa += (double)$coast['summa'];
      }
      $bo['coast'] = $summa;
      $bo_report[] = $bo;
    }
    return $bo_report;
  }

  /**
  * Возвращает массив содержащий идентификаторы ЗО и суммы расхода по БО
  * @param int $id_bo идентификатор БО
  * @return array
  */
  public function getPaymentCoast(int $id_bo): array {
    // Создаем объекты для работы с БД
    $payment_model = new Payment(); // Для заявок на оплату
    $bo = $this->getBudget($id_bo);
    $pay = [];
    // получаем все оплаты использующие эту БО
    $payments = $payment_model->getPayment(null, $id_bo);
    if ($payments) {
      // проходимся по всем оплатам использующим это ЕР
      foreach ($payments as $payment) {
        $vat = $payment['vat'];
        $ids = explode(';', trim($payment['bos_id']));
        $sums = explode(';', trim($payment['sum_bo']));
        $key = array_search($bo['id'], $ids);
        $sum = $sums[$key];
        //$pay_bo['number'] = $payment['number'] . '/' . substr($payment['date'], 0, 4);
        $pay_bo['id'] = $payment['id'];
        if ($bo['vat'] == '1.20') {
          // Если БО с НДС 20%
          if ($vat == '1.20') $pay_bo['summa'] = $sum; // Если оплата с НДС 20%
          if ($vat == '1.10') $pay_bo['summa'] = round($sum / 1.1 * 1.2, 2); // Если оплата с НДС 10%
          if ($vat == '1.00') $pay_bo['summa'] = round($sum * 1.2, 2); // Если оплата без НДС
        }
        if ($bo['vat'] == '1.00') {
          // Если БО без НДС
          if ($vat == '1.20') $pay_bo['summa'] = round($sum / 1.2, 2); // Если оплата с НДС 20%
          if ($vat == '1.10') $pay_bo['summa'] = round($sum / 1.1, 2); // Если оплата с НДС 10%
          if ($vat == '1.00') $pay_bo['summa'] = $sum; // Если оплата без НДС
        }
        $pay[] = $pay_bo;
      }
    }
    return $pay;
  }

}