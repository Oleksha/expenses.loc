<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "04.01.2024"
 * Время создания = "9:56"
 **/

namespace app\controllers;

use app\models\Er;
use app\models\Partner;
use app\models\PartnerType;
use app\models\Payment;
use app\models\Receipt;
use app\models\Vat;
use Exception;

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
    // Создаем необходимые объекты связи с БД.
    $partner_models = new Partner(); // Для контрагенты.
    $er_models = new Er();           // Для единоличных решений.
    $receipt_models = new Receipt(); // Для поступлений товаров и услуг.
    // Получаем информацию обо всех КА.
    $partners = $partner_models->getPartner();
    foreach ($partners as $k => $partner) {
      // Получаем количество действующих ЕР.
      $date_now = date("Y-m-d");
      $ers = $er_models->getERFromDate((int)$partner['id'], $date_now);
      $partners[$k]['er'] = $ers ? count($ers) : 0;
      // Получаем сумму дебиторской задолженности.
      $sum = 0;
      $receipts = $receipt_models->getReceiptNoPay((int)$partner['id']); // Получаем неоплаченные поступления.
      if ($receipts) foreach ($receipts as $receipt) $sum += $receipt['sum']; // Подсчитываем сумму задолженности.
      $partners[$k]['sum'] = $sum;
    }
    // Формируем метатеги для страницы.
    $this->setMeta('Список активных контрагентов', 'Содержит список активных КА с дополнительной информацией о каждом', 'контрагент,дебиторская,задолженность,отсрочка,ер,единоличные,решения');
    // Передаем полученные данные в вид.
    $this->set(compact('partners'));
  }

  /**
   * Обрабатывает Выдачу информации о выбранном КА
   * @throws Exception
   */
  public function viewAction(): void
  {
    // Создаем необходимые объекты связи с БД.
    $partner_models = new Partner(); // Для контрагентов.
    $er_models = new Er();           // Для единоличных решений.
    $receipt_models = new Receipt(); // Для приходов.
    // Получение ID запрашиваемого контрагента.
    $id = (int)$this->route['id'];
    // Получение данных по КА из БД.
    $partner = $partner_models->getPartner($id);
    if (!$partner) throw new Exception('Контрагент с ID ' . $id . ' не найден', 500);
    /* ----- ЕДИНОЛИЧНЫЕ РЕШЕНИЯ ----- */
    $date_now = date("Y-m-d");
    $ers = $er_models->getERFromDate((int)$partner['id'], $date_now); // Действующие на данный момент ЕР.
    $ers_all = $er_models->getEr(false,(int)$partner['id']);       // Все ЕР в базе данных.
    $diff = my_array_diff($ers_all, $ers); // Не действующие на сегодня ЕР.
    // Добавляем в массив данные по расходам этого ЕР.
    if ($ers) {
      foreach ($ers as $k => $er) {
        // получаем расходы по этому ЕР.
        $ers[$k]['costs'] = $er_models->getERCosts((int)$er['id']);
      }
    }
    /* ------------ КОНЕЦ ------------ */
    /* ----------- ПРИХОДЫ ----------- */
    $receipt = $receipt_models->getReceipt('id_partner', $id);
    if ($receipt) {
      foreach ($receipt as $k => $v) {
        // Добавляем тип прихода (оплаченный, неоплаченный, поданный на оплату).
        $receipt[$k]['type'] = $receipt_models->isTypeReceipt((int)$v['id']);
      }
    }
    /* ------------ КОНЕЦ ------------ */
    // Формируем метатеги для страницы.
    $this->setMeta($partner['name'], 'Наименование КА');
    // Передаем полученные данные в вид.
    $this->set(compact('partner', 'ers', 'diff', 'receipt'));
  }

  /**
   * Добавление нового контрагента в БД
   */
  public function addAction(): void
  {
    unset($_SESSION['form_data']); // Очищаем сессию данных о КА.
    // Если существуют переданные данные методом POST
    if (!empty($_POST)) {
      // Создаем объект для связи с БД.
      $partner_model = new Partner();
      // Запоминаем переданные данные.
      $data = $_POST;
      // Загружаем переданные данные.
      $partner_model->load($data);
      // Проверяем заполненные данные на уникальность.
      if (!$partner_model->checkUnique()) {
        // Если проверка не пройдена запишем ошибки в сессию.
        $partner_model->getErrors();
        // запоминаем уже введенные данные
        $_SESSION['form_data'] = $data;
      } else {
        $partner_model->attributes['delay'] = (int)$partner_model->attributes['delay'];
        $partner_model->attributes['vat'] = (double)$partner_model->attributes['vat'];
        // Если проверка пройдена записываем данные в таблицу.
        if ($id = $partner_model->save('partner')) {
          // Если все прошло хорошо в ID номер зарегистрированного пользователя.
          // Перейдем на страницу созданного контрагента.
          redirect('/partner/' . $id);
        } else {
          $_SESSION['errors'] = 'Возникли ошибки при сохранении данных в БД';
          redirect();
        }
      }
    }
    // Создаем объект для связи с БД.
    $partnerType_model = new PartnerType(); // Типы контрагентов
    $types = $partnerType_model->getPartnerType();
    // Устанавливаем метаданные
    $this->setMeta('Добавление нового контрагента');
    $this->set(compact('types'));
  }

  /**
   * Изменяет данные о КА
   * @return false|void
   */
  public function editAction()
  {
    // Создаем объект для связи с БД.
    $partner_models = new Partner(); // Для контрагентов.
    if (!empty($_POST)) {
      // Загружаем полученные данные.
      $partner_models->load($_POST);
      // Сохраняем измененные данные в БД.
      $partner_models->edit('partner', $_POST['partner_id']);
      unset($_SESSION['form_data']); // Очищаем сессию от ненужных больше данных.
      redirect();
    }
    // Получаем переданный идентификатор КА.
    $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
    // Создаем объект для связи с БД.
    $partnerType_model = new PartnerType();
    // Получаем все типы контрагентов для поля со списком.
    $types = $partnerType_model->getPartnerType();
    if ($id) {
      // Если у нас есть ID получаем все данные об этом KA
      $partner = $partner_models->getPartner($id);
      if (!$partner) return false; // Если КА не найден дальнейшие действия бессмысленны
      // запоминаем полученные данные
      $_SESSION['form_data'] = $partner;
    }
    if ($this->isAjax()) {
      // Если запрос пришел АЯКСом
      $this->loadView('edit', compact('types'));
    }
    redirect();
  }

  /**
   * Обработка заявки на оплату
   */
  public function paymentAction(): void
  {
    // Получаем переданные GET данные.
    $receipt_id = !empty($_GET['receipt']) ? (int)$_GET['receipt'] : null; // Идентификатор прихода.
    $type = !empty($_GET['type']) ? (int)$_GET['type'] : null; // Тип выводимой информации.
    $parent = !empty($_GET['parent']) ? $_GET['parent'] : null; // Откуда пришел запрос.
    // Создаем объекты для работы с БД.
    $receipt_model = new Receipt(); // Для приходов.
    $partner_model = new Partner(); // Для контрагентов.
    $er_model = new Er();           // Для единоличных решений.
    $payment_model = new Payment(); // Для заявок на оплату.
    $vat_model = new Vat();         // Для ставок НДС.
    // Получаем все ставки НДС для заполнения поля со списком.
    $vats = $vat_model->getVat();
    // Получаем данные о заявке на оплату если она есть.
    $payment = $payment_model->getPayment(false, false, false, $receipt_id);
    // Получаем всю данные о текущем поступлении.
    $receipt = $receipt_model->getReceipt('id', $receipt_id);
    $receipt = $receipt[0];
    // Получаем данные обо всех неоплаченных поступлениях КА.
    $receipt_all = $receipt_model->getReceiptNoPay((int)$receipt['id_partner']);
    // Получаем всю информацию о КА.
    $partner = $partner_model->getPartner((int)$receipt['id_partner']);
    // Получаем все действующие ЕР для этого КА на момент прихода
    $ers = $er_model->getERFromDate((int)$partner['id'], $receipt['date']);
    $er = [];
    foreach ($ers as $k => $v) {
      $er[$k]['id'] = $v['id'];                    // Идентификатор.
      $er[$k]['budget'] = $v['name_budget_item'];  // Статья расхода.
      $er[$k]['number'] = $v['number'];            // Номер ЕР.
    }
    $ers = $er;
    // Формируем метатеги для страницы.
    $this->setMeta('Заявка на оплату - ' . $partner['name'], 'Заявка на оплату');
    // Передаем полученные данные в вид.
    $this->set(compact('payment', 'receipt', 'receipt_all', 'partner', 'ers', 'type', 'parent', 'vats'));
  }

}