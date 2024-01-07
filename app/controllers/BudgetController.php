<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "01.01.2024"
 * Время создания = "12:21"
 **/

namespace app\controllers;

use app\models\Budget;
use app\models\BudgetItems;
use app\models\Partner;
use app\models\Payment;
use JetBrains\PhpStorm\NoReturn;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Border, Fill, NumberFormat};
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use R;

/**
 * Класс обработки Бюджетных Операций (БО)
 */
class BudgetController extends AppController
{

  /**
   * Контроллер главной страницы БО
   */
  public function indexAction(): void
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
    $budgets = $budget_model->getBudget(false, $scenario);
    if ($budgets) {
      // Получаем расходы по выбранным БО
      foreach ($budgets as $k => $item) {
        // Получаем заявки на оплату использующие конкретную БО
        $payments = $payment_model->getPayment(false, (int)$item['id']);
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
  private function get_sum(array $payments, string $id_bo, float|string $vat_bo): float
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
  public function viewAction(): void
  {
    $id_bo = isset($_GET['id']) ? (int)$_GET['id'] : null;
    // Создаем объекты для работы с БД
    $budget_model = new Budget(); // Для бюджетных операций
    $payment_model = new Payment(); // Для заявок на оплату
    $bo = $budget_model->getBudget($id_bo);
    $payments = $payment_model->getPayment(false, $id_bo);
    // добавляем в массив дополнительные данные
    if ($payments) {
      $bo['payment'] = $this->get_sum($payments, $_GET['id'], $bo['vat']);
      $bo['pay_arr'] = $this->get_array_sum($payments, $_GET['id'], $bo['vat']);
    }
    else {
      $bo['payment'] = 0.00;
      $bo['pay_arr'] = [];
    }
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
  public function editAction(): void
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

  /**
   * Функция вывода отчета
   * @return void
   * @throws Exception
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  public function reportAction(): void
  {
    $month = $_GET['m'] ?? null;
    $year = $_GET['y'] ?? null;
    $monthsList = [
      '01' => 'ЯНВАРЬ', '02' => 'ФЕВРАЛЬ', '03' => 'МАРТ', '04' => 'АПРЕЛЬ', '05' => 'МАЙ', '06' => 'ИЮНЬ',
      '07' => 'ИЮЛЬ', '08' => 'АВГУСТ', '09' => 'СЕНТЯБРЬ', '10' => 'ОКТЯБРЬ', '11' => 'НОЯБРЬ', '12' => 'ДЕКАБРЬ'];
    //Создаем экземпляр класса электронной таблицы
    $spreadsheet = new Spreadsheet();
    //Получаем текущий активный лист
    $sheet = $spreadsheet->getActiveSheet();
    //Установка ширины столбца
    $sheet->getColumnDimension('A')->setWidth(35);
    $sheet->getColumnDimension('B')->setWidth(70);
    $sheet->getColumnDimension('C')->setWidth(14);
    $sheet->getColumnDimension('D')->setWidth(14);
    $sheet->getColumnDimension('E')->setWidth(14);
    $sheet->getStyle('C:E')->applyFromArray([
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
      ]
    ]);
    $sheet->getStyle('C:E')->getNumberFormat()
      ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    //Установка высоты для строки
    $sheet->getRowDimension(1)->setRowHeight(23.25);
    // Записываем в ячейку A1 данные
    $sheet->setCellValue('A1', 'Сводные данные по бюджету ' . $monthsList[$month] . ' ' . $year);
    $sheet->setCellValue('A2', 'на ' . date('j.m.Y'));
    // Получаем ячейку для которой будем устанавливать стили
    $sheet->getStyle('A')->applyFromArray([
      'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
      ]
    ]);
    $sheet->getStyle('B')->applyFromArray([
      'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
      ]
    ]);
    $sheet->getStyle('A1')->applyFromArray([
      'font' => [
        'name' => 'Calibri',
        'size' => 18,
        'bold' => true
      ],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
      ]
    ]);
    $sheet->getStyle('A2')->applyFromArray([
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
      ]
    ]);
    $sheet->getStyle('A3:E3')->applyFromArray([
      'font' => [
        'name' => 'Calibri',
        'size' => 12,
        'bold' => true
      ],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
      ]
    ]);
    // Заголовок
    $sheet->setCellValue('A3', 'Статья расхода');
    $sheet->setCellValue('B3', 'Комментарий');
    $sheet->setCellValue('C3', 'Заложено');
    $sheet->setCellValue('D3', 'Оплачено');
    $sheet->setCellValue('E3', 'Остаток');
    $sheet->getStyle('A3:E3')->getFill()
      ->setFillType(Fill::FILL_SOLID)
      ->getStartColor()->setARGB('00DCE6F1');
    // Получение данных для вывода отчета
    $scenario = $year . '-' . $month . '-01';
    $budget_model = new Budget();
    $bos = $budget_model->getForReport($scenario);
    $row = 4;
    $start = 4;
    $result = [];
    foreach ($bos as $val) {
      $result[$val['name_budget_item']][] = $val;
    }
    foreach ($result as $key => $value) {
      $num_str = 0;
      $sheet->setCellValue('A' . $row, $key);
      foreach ($value as $item) {
        $richText = new RichText();
        $payable = $richText->createTextRun($item['number']);
        $payable->getFont()->setBold(true);
        $payable->getFont()->setItalic(true);
        $richText->createText(' - ' . $item['description']);
        $sheet->setCellValue('B' . ($row + $num_str), $richText);
        $sheet->setCellValue('C' . ($row + $num_str), $item['summa']);
        $sheet->setCellValue('D' . ($row + $num_str), (string)$item['coast']);
        $summa = (float)$item['summa'] - $item['coast'];
        $sheet->setCellValue('E' . ($row + $num_str), (string)$summa);
        if ($item['summa'] - $item['coast'] != 0) {
          $sheet->getStyle('E' . ($row + $num_str))->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00CCFFCC');
          $sheet->getStyle('E' . ($row + $num_str))->applyFromArray([
            'font' => [
              'bold' => true
            ]
          ]);
        }
        $num_str += 1;
      }
      // Объединяем ячейки
      if ($num_str > 1) {
        $sheet->mergeCells('A' . $row . ':A' . ($row + $num_str - 1));
      }
      $row += $num_str;
    }
    $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
      'font' => [
        'bold' => true
      ]
    ]);
    $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
      ->setFillType(Fill::FILL_SOLID)
      ->getStartColor()->setARGB('00DCE6F1');
    $sheet->setCellValue('A' . $row, 'Общий итог');
    $sheet->setCellValue('C' . $row, '=SUM(C' . $start . ':C' . ($row - 1) . ')');
    $sheet->setCellValue('D' . $row, '=SUM(D' . $start . ':D' . ($row - 1) . ')');
    $sheet->setCellValue('E' . $row, '=SUM(E' . $start . ':E' . ($row - 1) . ')');
    $sheet->getStyle('A' . ($start - 1) . ':E' . $row)->applyFromArray([
      'borders' => [
        'allBorders' => [
          'borderStyle' => Border::BORDER_THIN,
        ],
      ],
    ]);
    //Объединяем ячейки
    $sheet->mergeCells('A1:E1');
    $sheet->getStyle('A2')->applyFromArray([
      'font' => [
        'bold' => true
      ]
    ]);
    $sheet->mergeCells('A2:E2');
    // установки для печати
    $sheet->getPageSetup()->setFitToWidth(1);
    $sheet->getPageSetup()->setFitToHeight(1);
    $sheet->getPageSetup()->setHorizontalCentered(true); // Центрирование при печати
    $sheet->getPageMargins()->setTop(0);
    $sheet->getPageMargins()->setRight(0);
    $sheet->getPageMargins()->setLeft(0);
    $sheet->getPageMargins()->setBottom(0);

    $writer = new Xlsx($spreadsheet);
    // Сохраняем файл в текущей папке, в которой выполняется скрипт.
    // Чтобы указать другую папку для сохранения.
    // Прописываем полный путь до папки и указываем имя файла
    $writer->save(ROOT . '\Расходы.xlsx');
    redirect();
  }

  /**
   * Функция вывода отчета за год
   * @return void
   * @throws Exception
   * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
   */
  public function reportYearAction(): void
  {
    $year = $_GET['y'] ?? null;
    $months = array('01','02','03','04','05','06','07','08','09','10','11','12');
    $monthsList = [
      '01' => 'ЯНВАРЬ', '02' => 'ФЕВРАЛЬ', '03' => 'МАРТ', '04' => 'АПРЕЛЬ', '05' => 'МАЙ', '06' => 'ИЮНЬ',
      '07' => 'ИЮЛЬ', '08' => 'АВГУСТ', '09' => 'СЕНТЯБРЬ', '10' => 'ОКТЯБРЬ', '11' => 'НОЯБРЬ', '12' => 'ДЕКАБРЬ'];
    //Создаем экземпляр класса электронной таблицы
    $spreadsheet = new Spreadsheet();
    $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'ЯНВАРЬ');
    $spreadsheet->addSheet($myWorkSheet, 0);
    $spreadsheet->removeSheetByIndex(1);
    $list = 0;
    foreach ($months as $month) {
      if ($list > 0) {
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $monthsList[$month]);
        $spreadsheet->addSheet($myWorkSheet, $list);
      }
      $spreadsheet->setActiveSheetIndex($list);
      //Получаем текущий активный лист
      $sheet = $spreadsheet->getSheet($list);
      //Установка ширины столбца
      $sheet->getColumnDimension('A')->setWidth(35);
      $sheet->getColumnDimension('B')->setWidth(70);
      $sheet->getColumnDimension('C')->setWidth(14);
      $sheet->getColumnDimension('D')->setWidth(14);
      $sheet->getColumnDimension('E')->setWidth(14);
      $sheet->getStyle('C:E')->applyFromArray([
        'alignment' => [
          'horizontal' => Alignment::HORIZONTAL_CENTER,
          'vertical' => Alignment::VERTICAL_CENTER,
          'wrapText' => true,
        ]
      ]);
      $sheet->getStyle('C:E')->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
      //Установка высоты для строки
      $sheet->getRowDimension(1)->setRowHeight(23.25);
      // Записываем в ячейку A1 данные
      $sheet->setCellValue('A1', 'Сводные данные по бюджету ' . $monthsList[$month] . ' ' . $year);
      $sheet->setCellValue('A2', 'на ' . date('j.m.Y'));
      // Получаем ячейку для которой будем устанавливать стили
      $sheet->getStyle('A')->applyFromArray([
        'alignment' => [
          'vertical' => Alignment::VERTICAL_CENTER,
          'wrapText' => true,
        ]
      ]);
      $sheet->getStyle('B')->applyFromArray([
        'alignment' => [
          'vertical' => Alignment::VERTICAL_CENTER,
          'wrapText' => true,
        ]
      ]);
      $sheet->getStyle('A1')->applyFromArray([
        'font' => [
          'name' => 'Calibri',
          'size' => 18,
          'bold' => true
        ],
        'alignment' => [
          'horizontal' => Alignment::HORIZONTAL_CENTER,
          'vertical' => Alignment::VERTICAL_CENTER,
          'wrapText' => true,
        ]
      ]);
      $sheet->getStyle('A2')->applyFromArray([
        'font' => [
          'bold' => true
        ],
        'alignment' => [
          'horizontal' => Alignment::HORIZONTAL_CENTER,
        ]
      ]);
      //Объединяем ячейки
      $sheet->mergeCells('A1:E1');
      $sheet->mergeCells('A2:E2');

      // Заголовок
      $sheet->setCellValue('A3', 'Статья расхода');
      $sheet->setCellValue('B3', 'Комментарий');
      $sheet->setCellValue('C3', 'Заложено');
      $sheet->setCellValue('D3', 'Оплачено');
      $sheet->setCellValue('E3', 'Остаток');
      $sheet->getStyle('A3:E3')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00DCE6F1');
      // Получение данных для вывода отчета
      $scenario = $year . '-' . $month . '-01';
      $budget = new Budget();
      $bos = $budget->getForReport($scenario);
      if (!empty($bos)) {
        $row = 4;
        $start = 4;
        $result = [];
        foreach ($bos as $val) {
          $result[$val['name_budget_item']][] = $val;
        }
        foreach ($result as $key => $value) {
          $num_str = 0;
          $sheet->setCellValue('A' . $row, $key);
          foreach ($value as $item) {
            $richText = new RichText();
            $payable = $richText->createTextRun($item['number']);
            $payable->getFont()->setBold(true);
            $payable->getFont()->setItalic(true);
            $richText->createText(' - ' . $item['description']);
            $sheet->setCellValue('B' . ($row + $num_str), $richText);
            $sheet->setCellValue('C' . ($row + $num_str), $item['summa']);
            $sheet->setCellValue('D' . ($row + $num_str), (string)$item['coast']);
            $summa = (float)$item['summa'] - $item['coast'];
            $sheet->setCellValue('E' . ($row + $num_str), (string)$summa);
            if ($item['summa'] - $item['coast'] != 0) {
              $sheet->getStyle('E' . ($row + $num_str))->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('00CCFFCC');
              $sheet->getStyle('E' . ($row + $num_str))->applyFromArray([
                'font' => [
                  'bold' => true
                ]
              ]);
            }
            $num_str += 1;
          }
          // Объединяем ячейки
          if ($num_str > 1) {
            $sheet->mergeCells('A' . $row . ':A' . ($row + $num_str - 1));
          }
          $row += $num_str;
        }
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
          'font' => [
            'bold' => true
          ]
        ]);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setARGB('00DCE6F1');
        $sheet->setCellValue('A' . $row, 'Общий итог');
        $sheet->setCellValue('C' . $row, '=SUM(C' . $start . ':C' . ($row - 1) . ')');
        $sheet->setCellValue('D' . $row, '=SUM(D' . $start . ':D' . ($row - 1) . ')');
        $sheet->setCellValue('E' . $row, '=SUM(E' . $start . ':E' . ($row - 1) . ')');
        $sheet->getStyle('A' . ($start - 1) . ':E' . $row)->applyFromArray([
          'borders' => [
            'allBorders' => [
              'borderStyle' => Border::BORDER_THIN,
            ],
          ],
        ]);

        // установки для печати
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);
        $sheet->getPageSetup()->setHorizontalCentered(true); // центрирование при печати
        $sheet->getPageMargins()->setTop(0);
        $sheet->getPageMargins()->setRight(0);
        $sheet->getPageMargins()->setLeft(0);
        $sheet->getPageMargins()->setBottom(0);
      }
      $sheet->getStyle('A3:E3')->applyFromArray([
        'font' => [
          'name' => 'Calibri',
          'size' => 12,
          'bold' => true
        ],
        'alignment' => [
          'horizontal' => Alignment::HORIZONTAL_CENTER,
          'vertical' => Alignment::VERTICAL_CENTER,
          'wrapText' => true,
        ]
      ]);
      $list += 1;
    }
    $writer = new Xlsx($spreadsheet);
    // Сохраняем файл в текущей папке, в которой выполняется скрипт.
    // Чтобы указать другую папку для сохранения.
    // Прописываем полный путь до папки и указываем имя файла
    $writer->save(ROOT . '\Расходы_за_год.xlsx');
    redirect();
  }

  /**
   * Функция загрузки данных из файла
   * @return void
   * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
   */
  public function uploadAction(): void
  {
    unset($_SESSION['success']); // удаляем сессию с успешными сообщениями
    unset($_SESSION['error']);   // удаляем сессию с ошибочными сообщениями
    //unset($_SESSION['file']);
    if (isset($_SESSION['file'])) {
      $reader = IOFactory::createReader('Xlsx');
      $spreadsheet = $reader->load(WWW . "/uploads/{$_SESSION['file']}");
      $reader->setReadDataOnly(true);                 // Устанавливаем только чтение данных
      $worksheet = $spreadsheet->getActiveSheet();    // считываем данные из активного листа
      unlink(WWW . "/uploads/{$_SESSION['file']}");   // Удаляем файл. После доработки перенести удаление в конец
      unset($_SESSION['file']);                       // удаляем сессию с именем обрабатываемого файла
      $header = true;     // ключ, что это строка заголовков
      $header_array = []; // массив содержащий заголовки столбцов
      $row_array = [];    // массив со значениями текущей строки
      $budget_array = []; // массив со значениями текущей строки*/
      /* упорядочиваем полученные данные - начало */
      foreach ($worksheet->getRowIterator() as $row) {
        // обрабатываем очередную строку из файла
        $i = 0;
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true); // просматривать только заполненные ячейки
        foreach ($cellIterator as $cell) {
          if ($header) { // если это заголовок заполняем массив
            $header_array[] = $cell->getValue(); // получаем наименования столбцов в массив
          } else {        // если это строка с данными
            $row_array[$header_array[$i]] = $cell->getValue();
            $i = $i + 1;
          }
        }
        if (!$header) $budget_array[] = $row_array; // если это не заголовок добавляем в массив данных
        $header = false;
      }
      /* Упорядочиваем полученные данные - конец */
      /* массив $budget_array содержит данные из файла */
      /* преобразование данных для добавления в БД - начало */
      $budget_obj = new Budget(); // экземпляр модели Budget
      $budget_success = [];
      $success = true;
      $_SESSION['error'] = '';
      $i = 2;
      foreach ($budget_array as $item) {
        // Обрабатываем только строки со статусом СОГЛАСОВАН и НА СОГЛАСОВАНИИ
        if ($item['Статус документа'] != 'Не согласован' && $item['Статус документа'] != 'Формирование') {
          $bo['scenario'] = $spreadsheet->getActiveSheet()->getTitle();  // сценарий берем из имени листа
          $bo['month_exp'] = $this->dateChange($item['Месяц расходов']);
          $bo['month_pay'] = $this->dateChange($item['Месяц оплаты']);
          $bo['number'] = $item['Номер'];
          $bo['summa'] = $item['Сумма'];
          if ($item['Ставка НДС'] == 'Без НДС') {
            $bo['vat'] = '1.00';
          } elseif ($item['Ставка НДС'] == '20%') {
            $bo['vat'] = '1.20';
          } else {
            $bo['vat'] = '1.10';
          }
          $bo_item = R::getAssocRow('SELECT * FROM budget_items WHERE name_budget_item = ?', [$item['Статья бюджета']]);
          if ($bo_item) {
            $bo_item = $bo_item[0];
            $bo['budget_item_id'] = $bo_item['id'];
          } else {
            $_SESSION['error'] .= "Все очень плохо. В БД отсутствует запись - {$item['Статья бюджета']}. Строка - $i ";
            $this->setMeta('Загрузка новых БО');
            return;
          }
          $bo['status'] = $item['Статус документа'];
          $bo['description'] = $item['Комментарий'];
          $budget_success[] = $bo;
        }
        $i += 1;
      }
      $i = 0;
      $y = 0;
      if ($success) {
        $_SESSION['success'] = 'Все прошло хорошо. ';
        foreach ($budget_success as $item) {
          $budget_obj->load($item);
          // проверка на наличии БО с этим номером в БД
          if (!$this->checkBO($item)) {
            // БО нет в БД
            $budget_obj->save('budget');
            $i = $i + 1;
          } else {
            $id = $this->checkBO($item);
            $budget_obj->edit('budget', $id);
            $y = $y + 1;
          }
          unset($_SESSION['error']);
        }
        if ($i > 0) $_SESSION['success'] .= "Добавлено $i бюджетных операций. ";
        if ($y > 0) $_SESSION['success'] .= "Исправлено $y бюджетных операций. ";
      } else {
        $_SESSION['error'] .= 'Все очень плохо. Ошибка загрузки файла. ';
      }
    }
    // формируем метатеги для страницы
    $this->setMeta('Загрузка новых БО');
  }

  /**
   * @return void
   */
  #[NoReturn] public function uploadFileAction(): void
  {
    $res = [];
    if (!empty($_FILES)) {
      $path = WWW . '/uploads/'; // папка в которую будут складываться файлы
      if ($this->uploadFile('file', $path)) {
        $res = ['answer' => 'ok'];
      } else {
        $res = ['answer' => 'error', 'error' => 'Тестовая ошибка'];
      }
    }
    exit(json_encode($res));
  }

  /**
   * @param $name
   * @param $path
   * @return bool
   */
  public function uploadFile($name, $path): bool
  {
    $ext = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $_FILES[$name]['name']));

    $new_name = sha1(uniqid()) . ".$ext";
    $uploadfile = $path.$new_name;
    echo $uploadfile;
    if (@move_uploaded_file($_FILES[$name]['tmp_name'], $uploadfile)) {
      $_SESSION['file'] = $new_name;
      return true;
    }
    return false;
  }

  /**
   * Функция преобразования даты из 31.01.1999 в 1999-01-31
   * @param $date_str
   * @return string
   */
  public function dateChange($date_str): string
  {
    $year = substr($date_str, 6, 4);
    $month = substr($date_str, 3, 2);
    $day = substr($date_str, 0, 2);
    return $year . '-' . $month . '-' . $day;
  }

  /**
   * Проверяет есть ли в БД БО с таким номером
   * @param array $data
   * @return false|mixed
   */
  public function checkBO(array $data): mixed
  {
    $bo_item = R::getAssocRow('SELECT * FROM budget WHERE scenario = ? AND number = ?', [$data['scenario'], $data['number']]);
    if ($bo_item) {
      $bo_item = $bo_item[0];
      return $bo_item['id'];
    } else {
      return false;
    }
  }

}