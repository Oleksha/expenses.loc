<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "27.12.2023"
 * Время создания = "10:21"
 **/

namespace app\controllers;

use app\models\Partner;
use app\models\Payment;
use app\models\Receipt;

/**
 * Контроллер главной страницы приложения
 */
class MainController extends AppController
{

  /**
   * Вывод страницы по умолчанию
   */
  public function indexAction()
  {
    // Создаем объекты для связи с БД.
    $partners = new Partner();      // Для контрагентов.
    $receipt_model = new Receipt(); // Для приходов.
    $payment_model = new Payment(); // Для заявок на оплату.
    // Получаем все неоплаченные или оплаченные сегодня поступления.
    $receipts = $receipt_model->getReceiptForMain();
    if ($receipts) {
      foreach ($receipts as $k => $v) {
        // Получаем всю информацию о контрагенте
        $partner = $partners->getPartner($v['id_partner']);
        if ($partner) { // Если КА существует,
          // дописываем в массив его ID и наименование
          $receipts[$k]['partner_id'] = $partner['id'];
          $receipts[$k]['partner'] = $partner['name'];
          // дата планируемой оплаты
          $receipts[$k]['pay_date'] = $this->getDatePayment($v['id']);
          // задержка
          $receipts[$k]['delay'] = $partner['delay'] ?? null;
          if ($receipts[$k]['pay_id']) {
            $receipts[$k]['pay'] = $payment_model->getPayment($receipts[$k]['pay_id']);
          }
        }
      }
    }
    // Формируем метатеги для страницы.
    $this->setMeta('Главная страница', 'Содержит информацию о неоплаченных приходах', 'дебиторская,задолженность,оплата,заявка');
    // Передаем полученные данные в вид.
    $this->set(compact('receipts'));
  }

  /**
   * Функция обработки нажатия кнопки Ввод оплаты
   * @return void
   */
  public function payAction()
  {
    if (!empty($_POST)) {
      // Создаем объекты для работы с БД
      $receipt_model = new Receipt(); // Для приходов
      $id_receipt = (int)$_POST['id'];
      // Получаем приход в который необходимо внести дату оплаты
      $edit_receipt = $receipt_model->getReceipt('id', $id_receipt);
      // Так-как возвращается только одна запись выбираем ее
      $edit_receipt = $edit_receipt[0];
      $edit_receipt['date_pay'] = $_POST['date']; // заполняем дату оплаты
      // записываем исправленные данные в БД
      $receipt_model->load($edit_receipt);
      $receipt_model->edit('receipt', $id_receipt);
      redirect();
    }
    // Получаем переданный идентификатор прихода
    $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
    if ($this->isAjax()) {
      // Если запрос пришел АЯКСом
      $this->loadView('payment', compact('id'));
    }
    redirect();
  }

  /**
   * Функция получения данных об оплате конкретного прихода
   * @param string $payment_id идентификатор прихода
   * @return mixed
   */
  public function getDatePayment(string $payment_id)
  {
    $date_payment = '';
    // Создаем объекты для связи с БД.
    $payment_model = new Payment(); // Для оплат.
    // Получаем данные об оплате данного прихода.
    $payments = $payment_model->getPayment();
    foreach ($payments as $payment) {
      if (in_array($payment_id, explode(';', $payment['receipts_id']))) {
        // Найдена оплата.
        $date_payment = $payment['date_pay'];
      }
    }
    return $date_payment;
  }

}