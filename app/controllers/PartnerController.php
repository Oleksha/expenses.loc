<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "04.01.2024"
 * Время создания = "9:56"
 **/

namespace app\controllers;

use app\models\Er;
use app\models\Partner;
use app\models\Receipt;

/**
 * Контроллер обработки операций с КА
 */
class PartnerController extends AppController
{

  /**
   * Обрабатывает страницу по умолчанию
   */
  public function indexAction(): void
  {
    // создаем необходимые объекты связи с БД
    $partner_models = new Partner(); // Для контрагенты
    $er_models = new Er();           // Для единоличных решений
    $receipt_models = new Receipt(); // Для поступлений товаров и услуг
    // получаем информацию обо всех КА
    $partners = $partner_models->getPartner();
    foreach ($partners as $k => $partner) {
      // Получаем количество действующих ЕР
      $date_now = date("Y-m-d");
      $ers = $er_models->getERFromDate((int)$partner['id'], $date_now);
      $partners[$k]['er'] = $ers ? count($ers) : 0;
      // Получаем сумму дебиторской задолженности
      $sum = 0;
      $receipts = $receipt_models->getReceiptNoPay((int)$partner['id']); // получаем неоплаченные поступления
      if ($receipts) foreach ($receipts as $receipt) $sum += $receipt['sum']; // подсчитываем сумму задолженности
      $partners[$k]['sum'] = $sum;
    }
    // формируем метатеги для страницы
    $this->setMeta('Список активных контрагентов', 'Содержит список активных КА с дополнительной информацией о каждом', 'контрагент,дебиторская,задолженность,отсрочка,ер,единоличные,решения');
    // Передаем полученные данные в вид
    $this->set(compact('partners'));
  }

}