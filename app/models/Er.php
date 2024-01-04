<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "04.01.2024"
 * Время создания = "9:59"
 **/

namespace app\models;

use app\models\AppModel;
use R;

/**
 * Модель для связи с БД - Единоличные решения
 */
class Er extends AppModel
{

  /**
   * Массив полей таблицы для заполнения
   * @var array
   */
  public $attributes = [
    'id_partner' => '',
    'id_budget_item' => '',
    'number' => '',
    'data_start' => '',
    'data_end' => '',
    'delay' => '',
    'summa' => '',
  ];

  /**
   * Возвращает массив данных о ЕР по идентификатору или о всех если он не указан
   * @param bool|int $id идентификатор ЕР или null
   * @return array|false
   */
  public function getEr(bool|int $id = false): bool|array
  {
    if ($id !== false) {
      $er = R::getAssocRow('SELECT * FROM er WHERE id = ? LIMIT 1', [$id]);
      if (!empty($er)) return $er[0];
    } else {
      $er = R::getAssocRow('SELECT * FROM er ORDER BY data_start');
      if (!empty($er)) return $er;
    }
    return false;
  }

  /**
   * Возвращает все ЕР на указанную дату
   * @param int $partner_id идентификатор КА
   * @param string $date строковое представление даты
   * @return array
   */
  public function getERFromDate(int $partner_id, string $date): array {
    $ers = R::getAll("SELECT er.*, budget_items.name_budget_item FROM er, budget_items WHERE (budget_items.id = er.id_budget_item) AND (data_start <= '$date') AND (data_end >= '$date') AND id_partner = ?", [$partner_id]);
    $ers_returned = [];
    foreach ($ers as $er) {
      if (($er['summa'] - $this->getERCosts((int)$er['id'])) > 0) {
        $ers_returned[] = $er;
      }
    }
    return $ers_returned;
  }

  /**
   * Возвращает расход денежных средств по ЕР
   * @param int $er_id идентификатор ЕР
   * @return float
   */
  public function getERCosts(int $er_id): float {
    // Создаем необходимые объекты связи с БД.
    $payment_models = new Payment();  // Для заявок на оплату.
    $summa_costs = 0.00; // Содержит расходы по ЕР.
    // Получаем все оплаты использующие ЕР.
    $payments = $payment_models->getPayment(false, false, $er_id);
    // Если таковые есть проходимся по всему массиву
    if ($payments) {
      foreach ($payments as $payment) {
        $vat = $payment['vat']; // НДС текущей ЗО
        $nums = explode(';', $payment['ers_id']); // массив всех идентификаторов ЕР в ЗО
        $sums = explode(';', $payment['sum_er']); // массив всех сумм ЕР в ЗО
        $key = array_search($er_id, $nums);  // индекс текущей ЕР в массиве ЕР
        $sum = $sums[$key];                         // сумма текущей ЕР
        // добавляем сумму ЗО в расходы по ЕР без НДС
        $summa_costs += round($sum / $vat, 2);
      }
    }
    return $summa_costs;
  }

}