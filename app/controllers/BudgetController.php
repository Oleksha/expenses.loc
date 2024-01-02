<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "01.01.2024"
 * Время создания = "12:21"
 **/

namespace app\controllers;

use app\controllers\AppController;
use app\models\Budget;
use app\models\BudgetItems;
use app\models\Partner;
use app\models\Payment;

/**
 * Класс обработки Бюджетных Операций (БО)
 */
class BudgetController extends AppController
{

  /**
   * Контроллер главной страницы БО
   */
  public function indexAction()
  {
    if (isset($_GET['filter'])) {
      // Получаем текущий сценарий.
      if (isset($_SESSION['scenario'])) {
        // Если сессия существует сравниваем ее с запрошенными данными.
        if ($_SESSION['scenario'] != $_GET['filter']) {
          // Если данные отличаются заменяем.
          $_SESSION['scenario'] = $_GET['filter'];
        }
      } else {
        $_SESSION['scenario'] = $_GET['filter'];
      }
      $year = mb_substr($_GET['filter'], 0, 4);  // Выделяем месяц сценария.
      $month = mb_substr($_GET['filter'], 5, 2); // Выделяем год сценария.
      $scenario = $_GET['filter'];
    } elseif (isset($_SESSION['scenario'])) {
      // Читаем данные из сессии если они есть, а если нет берем текущие.
      $year = mb_substr($_SESSION['scenario'], 0, 4);  // Выделяем месяц сценария.
      $month = mb_substr($_SESSION['scenario'], 5, 2); // Выделяем год сценария.
      $scenario = $_SESSION['scenario'];
    } else {
      // получаем сценарий для просмотра бюджетных операций если он есть
      $filter_date = date('Y-m-d');
      $year = mb_substr($filter_date, 0, 4);  // выделяем месяц сценария
      $month = mb_substr($filter_date, 5, 2); // выделяем год сценария
      $scenario = $year . '-' . $month . '-01';
    }
    // Создаем объекты для работы с БД
    $budget_model = new Budget(); // Для бюджетных операций
    $payment_model = new Payment(); // Для заявок на оплату
    // Получение данных из БД соответственно сценарию
    $budgets = $budget_model->getBudget(null, $scenario);
    if ($budgets) {
      // Получаем расходы по выбранным БО
      foreach ($budgets as $k => $item) {
        // Получаем заявки на оплату использующие конкретную БО
        $payments = $payment_model->getPayment(null, $item['id']);
        // Рассчитываем израсходованную сумму с конкретной БО
        if ($payments) $budgets[$k]['payment'] = $this->get_sum($payments, (string)$item['id'], $item['vat']);
        else $budgets[$k]['payment'] = 0.00;
      }
    }
    // Если данные пришли AJAX-запросом
    if ($this->isAjax()) {
      $this->loadView('filter', compact('budgets', 'year', 'month'));
    }
    // Формируем метатеги для страницы
    $this->setMeta('Список бюджетных операций', 'Описание...', 'Ключевые слова...');
    // Передаем полученные данные в вид
    $this->set(compact('budgets', 'year', 'month'));
  }

  /**
   * Функция подсчитывающая расход по БО
   * @param array $payments Все оплаты содержащие проверяемую БО
   * @param string $id_bo Составной номер БО (НОМЕР/ГОД)
   * @param float|string $vat_bo Ставка НДС проверяемой БО
   * @return float Сумма расходов по БО
   */
  private function get_sum(array $payments, string $id_bo, $vat_bo): float
  {
    $sum = 0.00; // расход по данной БО
    foreach ($payments as $payment) { // просматриваем все оплаты использующие нашу БО
      $ids = explode(';', trim($payment['bos_id']));
      $sums = explode(';', trim($payment['sum_bo']));
      $key = array_search($id_bo, $ids);
      if ($vat_bo == '1.20') { // если БО с НДС 20%
        if ($payment['vat'] == '1.20') $sum += (float)$sums[$key]; // если платеж с НДС 20%
        if ($payment['vat'] == '1.10') $sum += round((float)$sums[$key] / 1.1 * 1.2, 2); // если платеж с НДС 10%
        if ($payment['vat'] == '1.00') $sum += round((float)$sums[$key] * 1.2, 2); // если платеж без НДС
      }
      if ($vat_bo == '1.00') { // если БО без НДС
        if ($payment['vat'] == '1.00') $sum += (float)$sums[$key]; // если платеж без НДС
        if ($payment['vat'] == '1.10') $sum += round((float)$sums[$key] / 1.1, 2); // если платеж с НДС 10%
        if ($payment['vat'] == '1.20') $sum += round((float)$sums[$key] / 1.2, 2); // если платеж с НДС 20%
      }
    }
    return $sum;
  }

  /**
   * Функция просмотра выбранной БО
   */
  public function viewAction()
  {
    $id_bo = isset($_GET['id']) ? (int)$_GET['id'] : null;
    // Создаем объекты для работы с БД
    $budget_model = new Budget(); // Для бюджетных операций
    $payment_model = new Payment(); // Для заявок на оплату
    $bo = $budget_model->getBudget($id_bo);
    $payments = $payment_model->getPayment(null, $id_bo);
    // добавляем в массив дополнительные данные
    if ($payments) $bo['payment'] = $this->get_sum($payments, $_GET['id'], $bo['vat']);
    else $bo['payment'] = 0.00;
    $bo['pay_arr'] = $this->get_array_sum($payments, $_GET['id'], $bo['vat']);
    // формируем метатеги для страницы
    $this->setMeta("Просмотр бюджетной операции {$bo['number']}", 'Описание...', 'Ключевые слова...');
    // Передаем полученные данные в вид
    $this->set(compact('bo', 'payments'));
  }

  /**
   * Функция возвращающая массив расходов по БО
   * @param array $payments
   * @param string $id_bo
   * @param string $vat_bo
   * @return array
   */
  private function get_array_sum(array $payments, string $id_bo, string $vat_bo): array
  {
    // Создаем объекты для работы с БД
    $partner_model = new Partner();
    $pay_arr = []; // массив возвращаемых данных
    foreach ($payments as $payment) {
      $pay['date_pay'] = $payment['date_pay'];
      $ids = explode(';', trim($payment['bos_id']));//->num_bo));
      $sums = explode(';', trim($payment['sum_bo']));//->sum_bo));
      $key = array_search($id_bo, $ids);
      if ($vat_bo == '1.20') {
        // если БО с НДС - 20%
        if ($payment['vat'] == '1.20') $pay['summa'] = $sums[$key]; // если платеж с НДС 20%
        if ($payment['vat'] == '1.10') $pay['summa'] = round($sums[$key] / 1.1 * 1.2, 2); // если платеж с НДС 10%
        if ($payment['vat'] == '1.00') $pay['summa'] = round($sums[$key] * 1.2, 2); // если платеж без НДС
      }
      if ($vat_bo == '1.00') {
        // если БО без НДС
        if ($payment['vat'] == '1.00') $pay['summa'] = $sums[$key]; // если платеж без НДС
        if ($payment['vat'] == '1.10') $pay['summa'] = round($sums[$key] / 1.1, 2); // если платеж с НДС 10%
        if ($payment['vat'] == '1.20') $pay['summa'] = round($sums[$key] / 1.2, 2); // если платеж с НДС 20%
      }
      $pay['partner'] = $partner_model->getPartner((int)$payment['id_partner']);
      $pay_arr[] = $pay;
    }
    return $pay_arr;
  }

  /**
   * Функция редактирования данных БО
   */
  public function editAction()
  {
    // Создаем объекты для работы с БД
    $budget_model = new Budget(); // Для бюджетных операций
    $budget_items_model = new BudgetItems(); // Для бюджетных статей
    if (!empty($_POST)) {
      // получаем данные пришедшие методом POST
      $edit_budget = $_POST;
      $_POST['budget_item_id'] = (int)$_POST['budget_item_id'];
      $budget_model->load($edit_budget);
      $budget_model->attributes['budget_item_id'] = (int)$budget_model->attributes['budget_item_id'];
      $budget_model->attributes['summa'] = (float)$budget_model->attributes['summa'];
      $budget_model->attributes['vat'] = (float)$budget_model->attributes['vat'];
      $budget_model->edit('budget', (int)$edit_budget['id']);
      redirect();
    }
    $id_bo = $_GET['id'] ?? null;
    // Получаем данные по БО
    $budget = $budget_model->getBudget((int)$id_bo);
    // Получаем все статьи расхода
    $budget_items = $budget_items_model->getBudgetItems();
    if ($this->isAjax()) {
      // Если запрос пришел АЯКСом
      $this->loadView('edit', compact('budget', 'budget_items'));
    }
    redirect();
  }

}