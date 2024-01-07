<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "06.01.2024"
 * Время создания = "10:12"
 **/

namespace app\controllers;

use app\models\Budget;
use app\models\Er;
use app\models\Partner;
use app\models\Payment;
use app\models\Receipt;
use app\models\Vat;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use R;

/**
 * Класс контроллера поступлений
 */
class ReceiptController extends AppController
{

  /**
   * Функция добавления нового прихода
   */
  public function addAction(): void
  {
    if (!empty($_POST)) { // Если существуют переданные данные методом POST.
      // Создаем необходимый объект связи с БД.
      $receipt_model = new Receipt();
      // Загружаем полученные данные.
      $receipt_model->load($_POST);
      // Записываем данные о новом поступлении в БД.
      $receipt_model->save('receipt');
      redirect();
    }
    // Получаем данные пришедшие методом GET
    $id = !empty($_GET['id']) ? (int)$_GET['id'] : null; // Идентификатор КА
    // Создаем необходимые объекты связи с БД.
    $partner_model = new Partner(); // Для контрагентов.
    $vat_model = new Vat();         // Для ставок НДС.
    // Получаем всю информацию о КА.
    $partner = $partner_model->getPartner($id);
    // Получаем всю информацию о ставках НДС.
    $vats = $vat_model->getVat();
    if ($this->isAjax()) {
      // Если запрос пришел АЯКСом
      $this->loadView('add', compact('partner', 'vats'));
    }
    redirect();
  }

  /**
   * Функция редактирования данных о приходе
   * @throws Exception
   */
  public function editAction(): void
  {
    // Создаем необходимые объекты связи с БД.
    $receipt_model = new Receipt(); // Для приходов.
    $partner_model = new Partner(); // Для контрагентов.
    $vat_model = new Vat();         // Для ставок НДС.
    if (!empty($_POST)) { // Если существуют переданные данные методом POST.
      // Загружаем полученные данные.
      $receipt_model->load($_POST);
      // Записываем измененные данные в БД.
      $receipt_model->edit('receipt', $_POST['id']);
      redirect();
    }
    // Получаем всю информацию о ставках НДС.
    $vats = $vat_model->getVat();
    // Получаем переданный идентификатор прихода.
    $id_receipt = !empty($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id_receipt) {
      // Если у нас есть ID получаем все данные об этом приходе.
      $receipt = $receipt_model->getReceipt('id', $id_receipt);
      $receipt = $receipt[0];
      // Если данных не получены дальнейшие действия бессмысленны.
      if (!$receipt) throw new Exception("Не получены данные по выбранному ID", 199);
      // Получаем все данные о КА.
      $partner = $partner_model->getPartner((int)$receipt['id_partner']);
      if ($this->isAjax()) {
        // Если запрос пришел АЯКСом.
        $this->loadView('edit', compact('receipt', 'partner', 'vats'));
      }
    }
    redirect();
  }

  /**
   * Функция удаления выбранного прихода
   */
  public function delAction(): void
  {
    // Получаем переданный идентификатор прихода
    $id_receipt = !empty($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id_receipt) R::hunt('receipt', 'id = ?', [$id_receipt]);
    redirect();
  }

  /**
   * Функция записывающая в БД ЗО и вносящая исправления в приходы оплаченные этой ЗО
   */
  #[NoReturn] public function payReceiptAction(): void
  {
    // Создаем объекты для работы с БД.
    $receipt_model = new Receipt(); // Для приходов.
    $er_model = new Er();           // Для ЕР.
    $budget_model = new Budget();   // Для бюджетных операций.
    // Сохраняем данные пришедшие методом POST.
    $pay_receipt = !empty($_POST) ? $_POST : null;
    // Проверяем полученные данные.
    if (!$this->checkPay($pay_receipt)) {
      // Если в данных ошибка запоминаем значения полей формы.
      $_SESSION['form_data'] = $pay_receipt;
      redirect();
    }
    $receipts = $receipt_model->getReceipts($pay_receipt['receipt']); // Получаем массив приходов.
    // Исправляем данные пришедшие в виде массива.
    $pay_receipt['sum'] = $this->prepareData($pay_receipt['sum']);
    $pay_receipt['receipts_id'] = $this->prepareData($pay_receipt['receipt']);
    $str = ''; // Инициируем переменную.
    foreach ($receipts as &$value) {
      $str .= $value['number'] . '/' . mb_substr($value['date'], 0, 4) . ';';  // Добавляем значение массива с символом ';' в конце.
    }
    $pay_receipt['receipt'] = rtrim($str, ';');
    $pay_receipt['ers_id'] = $this->prepareData($pay_receipt['num_er']);
    $str = ''; // Обнуляем переменную.
    foreach ($pay_receipt['num_er'] as &$value) {
      $er = $er_model->getEr((int)$value);
      $str .= $er['number'] . ';';  // Добавляем значение массива с символом ';' в конце.
    }
    $pay_receipt['num_er'] = rtrim($str, ';');
    if (empty($pay_receipt['date_pay'])) $pay_receipt['date_pay'] = null;
    $pay_receipt['sum_er'] = $this->prepareData($pay_receipt['sum_er']);
    // добавляем ID бюджетных операций
    $str = ''; // обнуляем переменную
    foreach (explode(';', $pay_receipt['num_bo']) as &$value) {
      $bo = $budget_model->getBudget(false, false, $value);
      $str .= $bo['id'] . ';';  // добавляем значение массива с символом ';' в конце
    }
    $pay_receipt['bos_id'] = rtrim($str, ';');
    // внесение изменений в ЗО
    $payment_model = new Payment();
    $payment_model->load($pay_receipt);
    if (empty($pay_receipt['id'])) {
      // это новая ЗО
      $payment_id = $payment_model->save('payment');
    } else {
      // это редактируемая ЗО
      $payment_model->edit('payment', $pay_receipt['id']);
      $payment_id = $pay_receipt['id'];
    }
    // внесение изменений в приходы
    foreach ($receipts as $item) {
      $edit_receipt['id'] = $item['id'];
      $edit_receipt['date'] = $item['date'];
      $edit_receipt['number'] = $item['number'];
      $edit_receipt['sum'] = $item['sum'];
      $edit_receipt['type'] = $item['type'];
      $edit_receipt['vat_id'] = (int)$item['vat']['id'];
      $edit_receipt['id_partner'] = $item['id_partner'];
      $edit_receipt['num_doc'] = $item['num_doc'];
      $edit_receipt['date_doc'] = $item['date_doc'];
      $edit_receipt['note'] = $item['note'];
      $edit_receipt['num_pay'] = dataYear($pay_receipt['number'], $pay_receipt['date']);
      $edit_receipt['pay_id'] = $payment_id;
      $edit_receipt['date_pay'] = $item['date_pay'];
      $receipt = new Receipt();
      $receipt->load($edit_receipt);
      $receipt->edit('receipt', $edit_receipt['id']);
    }
    unset($_SESSION['form_data']);
    if ($pay_receipt['parent'] == 'main') {
      redirect('/');
    } else {
      redirect("/partner/{$pay_receipt['id_partner']}");
    }
  }

  /**
   * Проверка правильности заполнения полей формы
   * @param array $data проверяемый массив
   * @return bool TRUE ошибок нет FALSE есть ошибки
   */
  protected function checkPay(array $data): bool
  {
    // Убираем ошибки влияющие на последующие проверки
    if (strripos($data['sum_er'][0], ',')) {
      $_SESSION['error_payment'][] = 'В поле СУММА ЕР присутствует символ (,)';
      return false;
    }
    if (strripos($data['sum_bo'], ',')) {
      $_SESSION['error_payment'][] = 'В поле СУММА БО присутствует символ (,)';
      return false;
    }
    if (!$this->checkNumBO($data['num_bo'])) {
      $_SESSION['error_payment'][] = 'Ошибка заполнения поля НОМЕР БО';
      return false;
    }
    // По умолчанию ошибок нет
    $verify = true;
    /* ----- Проверка логики заполнения полей формы ----- */
    if (count($data['receipt']) != count($data['sum'])) {
      $y = count($data['receipt']);
      $x = count($data['sum']);
      $_SESSION['error_payment'][] = "Не совпадает количество выбранных приходов ($y) и сумм ($x)";
      $verify = false;
    }
    if (count($data['num_er']) != count(explode(';', $data['sum_er'][0]))) {
      $y = count($data['num_er']);
      $x = count(explode(';', $data['sum_er'][0]));
      $_SESSION['error_payment'][] = "Не совпадает количество выбранных ЕР ($y) и введенных сумм ($x)";
      $verify = false;
    }
    if (count(explode(';', $data['num_bo'])) != count(explode(';', $data['sum_bo']))) {
      $y = count(explode(';', $data['num_bo']));
      $x = count(explode(';', $data['sum_bo']));
      $_SESSION['error_payment'][] = "Не совпадает количество введенных БО ($y) и введенных сумм ($x)";
      $verify = false;
    }
    $a = array_sum($data['sum']); // Общая сумма выбранных приходов
    $b = array_sum(explode(';', $data['sum_er'][0])); // Общая сумма выбранных ЕР
    $epsilon = 0.00001;
    if (abs($a - $b) >= $epsilon) {
      $_SESSION['error_payment'][] = "Не совпадает сумма выбранных приходов $a и суммы ЕР $b";
      $verify = false;
    }
    $b = array_sum(explode(';', $data['sum_bo'])); // Общая сумма выбранных БО
    if (abs($a - $b) >= $epsilon) {
      $_SESSION['error_payment'][] = "Не совпадает сумма выбранных приходов $a и суммы БО $b";
      $verify = false;
    }
    /* ----- Проверка правильности заполнения полей формы ----- */
    if (count(explode(';', $data['sum_er'][0])) == count($data['num_er'])) {
      // Создаем необходимый объект связи с БД.
      $er_models = new Er();   // Единоличные решения
      $ers = $data['num_er'];
      $sums = explode(';', $data['sum_er'][0]);
      foreach ($ers as $k => $v) {
        $sum = $sums[$k]; // Получаем сумму, списываемую с ЕР.
        $sum = round($sum / $data['vat'], 2); // Сумма должна быть без НДС.
        // Получаем остаток средств на ЕР.
        $v = (int)$v; // Идентификатор ЕР.
        $data_er = $er_models->getEr($v); // Получаем данные по ЕР.
        if ($data['type'] == 3) { // Если это создание заявки на оплату.
          $coast = $data_er['summa'] - $er_models->getERCosts($v);
        } elseif ($data['type'] == 2) { // Если это корректировка заявки на оплату.
          // Нужно убрать из остатка сумму текущей оплаты
          $coast = $data_er['summa'] - $er_models->getERCosts($v) + $sum;
        } else {
          $coast = 0.00;
          $_SESSION['error_payment'][] = 'Ошибка заполнения данных';
          $verify = false;
        }
        if (abs($sum - $coast) > $epsilon) {
          if ($sum > $coast) {
            $_SESSION['error_payment'][] = "Не хватает средств. Требуется сумма $sum, а в ЕР ($v) осталось $coast";
            $verify = false;
          }
        }
      }
    } else {
      $a = count($data['num_er']);
      $b = count(explode(';', $data['sum_er']));
      $_SESSION['error_payment'][] = "Не совпадает количество введенных ЕР $a и количество сумм ЕР $b";
      $verify = false;
    }
    if (count(explode(';', $data['sum_bo'])) == count(explode(';', $data['num_bo']))) {
      // Создаем необходимый объект связи с БД.
      $budget_model = new Budget();
      $bo_array = explode(';', $data['num_bo']);
      $sum_array = explode(';', $data['sum_bo']);
      foreach ($bo_array as $k => $v) {
        $sum = $sum_array[$k]; // Сумма необходимая для оплаты с данной БО.
        // Получаем всю информацию по БО
        $bo = $budget_model->getBudget(false, false, $v);
        // Получаем все оплаты по этой БО.
        $payments_array = $budget_model->getPaymentCoast((int)$bo['id']);
        // Получаем текущие данные.
        $current['id'] = $bo['id'];
        if ($bo['vat'] == '1.20') {
          if ($data['vat'] == '1.20') {
            $current['summa'] = $sum;
          }
          if ($data['vat'] == '1.10') {
            $current['summa'] = $sum / 1.1 * 1.2;
            $current['summa'] = round($current['summa'], 2);
          }
          if ($data['vat'] == '1.00') {
            $current['summa'] = $sum * 1.2;
            $current['summa'] = round($current['summa'], 2);
          }
        }
        if ($bo['vat'] == '1.00') {
          if ($data['vat'] == '1.00') {
            $current['summa'] = $sum;
          }
          if ($data['vat'] == '1.10') {
            $current['summa'] = $sum / 1.1;
            $current['summa'] = round($current['summa'], 2);
          }
          if ($data['vat'] == '1.20') {
            $current['summa'] = $sum / 1.2;
            $current['summa'] = round($current['summa'], 2);
          }
        }
        $summa = $bo['summa'];
        $total = 0.00;
        foreach ($payments_array as $item) {
          if (($item['summa'] !== $current['summa']) && ($item['id'] !== $current['id'])) {
            $total += $item['summa'];
          }
        }
        $coast = $summa - $total; // оставшаяся сумма БО
        if (abs((float)$current['summa'] - $coast) > $epsilon) {
          if ($current['summa'] > $coast) {
            $_SESSION['error_payment'][] = "Не хватает средств. Требуется сумма $sum, а в БО ($v) осталось $coast";
            $verify = false;
          }
        }
      }
    } else {
      $a = count(explode(';', $data['num_bo']));
      $b = count(explode(';', $data['sum_bo']));
      $_SESSION['error_payment'][] = "Не совпадает количество введенных БО $a и количество сумм БО $b";
      $verify = false;
    }
    return $verify;
  }

  /**
   * проверка правильности заполнения поля НОМЕРА БО
   * @param string $data содержимое поля
   * @return bool результат проверки
   */
  protected function checkNumBO(string $data): bool
  {
    // Получаем массив с номерами БО.
    $bos = explode(';', $data);
    // Просматриваем каждую строку массива.
    foreach ($bos as $bo) {
      if (strlen($bo) != 18) {
        // Проверка длинны каждой БО.
        return false;
      } else {
        // Проверка соответствия БО шаблону CUB0123456789/2022
        preg_match('/CUB[0-9]{10}\/[0-9]{4}/', $bo, $matches);
        if (empty($matches)) {
          return false;
        } else {
          // Проверяем Наличие БО в БД
          $budget_model = new Budget();   // экземпляр модели Budget
          if (!($budget_model->getBudget((int)$bo))) return false;
        }
      }
    }
    return true;
  }

  /**
   * Функция строкового представления массива
   * @param array $data входной массив
   * @return string строка значений массива разделенных символом ';'
   */
  public function prepareData(array $data): string
  {
    $data_str = ''; // Обнуляем переменную
    foreach ($data as &$value) {
      // добавляем значение массива с символом ';' в конце
      $data_str .= $value . ';';
    }
    // возвращаем строку без конечного знака ';'
    return rtrim($data_str, ';');
  }

}