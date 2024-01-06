<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "06.01.2024"
 * Время создания = "10:12"
 **/

namespace app\controllers;

use app\models\Partner;
use app\models\Receipt;
use app\models\Vat;
use Exception;
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

}