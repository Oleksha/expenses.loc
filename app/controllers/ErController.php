<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "05.01.2024"
 * Время создания = "11:15"
 **/

namespace app\controllers;

use app\models\BudgetItems;
use app\models\Er;
use app\models\Partner;
use app\models\Payment;
use R;

/**
 * Класс обработки Единоличных решений
 */
class ErController extends AppController
{

  /**
   * Добавляет новое ЕР
   */
  public function addAction(): void
  {
    if (!empty($_POST)) {
      // Создаем необходимый объект для связи с БД.
      $er_models = new Er(); // Для единоличных решений.
      // Загружаем данные пришедшие методом POST.
      $er_models->load($_POST);
      // Сохраняем данные в таблице БД.
      $er_models->save('er');
      redirect();
    }
    // Создаем необходимые объекты для связи с БД.
    $partner_models = new Partner(); // Для контрагентов.
    $budget_items_models = new BudgetItems(); // Для статей бюджета.
    // Получаем переданный идентификатор КА.
    $id_partner = !empty($_GET['id']) ? (int)$_GET['id'] : null;
    // Получаем данные о контрагенте из БД
    $partner = $partner_models->getPartner($id_partner);
    // Получаем данные о всех статьях расходов для поля со списком.
    $budget = $budget_items_models->getBudgetItems();
    if ($this->isAjax()) {
      // Если запрос пришел АЯКСом
      $this->loadView('add', compact('partner', 'budget'));
    }
    redirect();
  }

  /**
   * Редактирование существующей ЕР
   * @throws \Exception
   */
  public function editAction(): void
  {
    // Создаем необходимые объекты для связи с БД.
    $er_models = new Er(); // Для единоличных решений.
    $partner_models = new Partner(); // Для контрагентов.
    $budget_items_models = new BudgetItems(); // Для статей бюджета.
    if (!empty($_POST)) {
      // Загружаем данные пришедшие методом POST.
      $er_models->load($_POST);
      // Сохраняем отредактированные данные в таблице БД.
      $er_models->edit('er', $_POST['id']);
      redirect();
    }
    // Получаем переданный идентификатор ЕР.
    $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id) {
      // Если у нас есть ID получаем все данные об этом единоличном решении.
      $er = $er_models->getEr($id);
      // Если данных не получены дальнейшие действия бессмысленны.
      if (!$er) throw new \Exception("Не получены данные по выбранному ID", 199);
      // Получаем данные о контрагенте.
      $partner = $partner_models->getPartner((int)$er['id_partner']);
      // Получаем данные о всех статьях расходов для поля со списком.
      $budget = $budget_items_models->getBudgetItems();
      if ($this->isAjax()) {
        // Если запрос пришел АЯКСом
        $this->loadView('edit', compact('er', 'partner', 'budget'));
      }
    }
    redirect();
  }

  /**
   * Просмотр платежей по ЕР
   * @throws \Exception
   */
  public function viewAction(): void
  {
    // Создаем необходимые объекты связи с БД.
    $payment_models = new Payment(); // Для заявок на оплату.
    $er_models = new Er();           // Для единоличных решений.
    // Получаем переданный идентификатор ЕР.
    $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id) {
      // Если у нас есть номер получаем все данные об этом ЕР.
      $er = $er_models->getEr($id);
      // Если данных не получены дальнейшие действия бессмысленны.
      if (!$er) throw new \Exception("Не получены данные по выбранному ID", 199);
      // Если у нас есть ЕР получаем данные об оплатах использующих это ЕР
      $payments = $payment_models->getPayment(false, false, $id);
      if ($this->isAjax()) {
        // Если запрос пришел АЯКСом
        $this->loadView('view', compact('payments', 'er'));
      }
      redirect();
    }
  }

  /**
   * Удаление выбранного ЕР
   */
  public function delAction(): void
  {
    // Получаем переданный идентификатор ЕР.
    $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id) R::hunt('er', 'id = ?', [$id]);
    redirect();
  }

}