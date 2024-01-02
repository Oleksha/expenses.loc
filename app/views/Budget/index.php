<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "01.01.2024"
 * Время создания = "14:13"
 **/
?>
<main role="main">
  <div class="container">
    <div class="d-flex justify-content-between">
      <h1 class="mt-1">Список бюджетных операций</h1>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
        <li class="breadcrumb-item "><a href="<?=PATH;?>/budget/upload">Загрузка данных</a></li>
        <li class="breadcrumb-item active" aria-current="page">Бюджетные операции</li>
      </ol>
    </nav>
    <div class="breadcrumb filters">
      <div class="col-auto me-3">
        <div class="input-group">
          <label class="input-group-text" for="select_year">Год</label>
          <select class="form-select" id="select_year">
            <option value="2021" <?php /** @var string $year */
            if ($year == '2021') echo ' selected'; ?>>2021</option>
            <option value="2022" <?php if ($year == '2022') echo ' selected'; ?>>2022</option>
            <option value="2023" <?php if ($year == '2023') echo ' selected'; ?>>2023</option>
            <option value="2024" <?php if ($year == '2024') echo ' selected'; ?>>2024</option>
          </select>
        </div>
      </div>
      <div class="col-auto me-3">
        <div class="input-group">
          <label class="input-group-text" for="select_month">Месяц</label>
          <select class="form-select my_option" id="select_month">
            <option class="my_option" value="01" <?php /** @var string $month */
            if ($month == '01') echo ' selected'; ?>>Январь</option>
            <option class="my_option" value="02" <?php if ($month == '02') echo ' selected'; ?>>Февраль</option>
            <option class="my_option" value="03" <?php if ($month == '03') echo ' selected'; ?>>Март</option>
            <option class="my_option" value="04" <?php if ($month == '04') echo ' selected'; ?>>Апрель</option>
            <option class="my_option" value="05" <?php if ($month == '05') echo ' selected'; ?>>Май</option>
            <option class="my_option" value="06" <?php if ($month == '06') echo ' selected'; ?>>Июнь</option>
            <option class="my_option" value="07" <?php if ($month == '07') echo ' selected'; ?>>Июль</option>
            <option class="my_option" value="08" <?php if ($month == '08') echo ' selected'; ?>>Август</option>
            <option class="my_option" value="09" <?php if ($month == '09') echo ' selected'; ?>>Сентябрь</option>
            <option class="my_option" value="10" <?php if ($month == '10') echo ' selected'; ?>>Октябрь</option>
            <option class="my_option" value="11" <?php if ($month == '11') echo ' selected'; ?>>Ноябрь</option>
            <option class="my_option" value="12" <?php if ($month == '12') echo ' selected'; ?>>Декабрь</option>
          </select>
        </div>
      </div>
      <div class="col-auto me-3">
        <a class="btn btn-primary" href="/budget/report?m=<?=$month?>&y=<?=$year?>" role="button">Остатки за месяц</a>
      </div>
      <div class="col-auto">
        <a class="btn btn-primary" href="/budget/report-year?y=<?=$year?>" role="button">Остатки за год</a>
      </div>
    </div>
    <div class="product-one">
      <?php /** @var array $budgets */
      if($budgets): ?>
        <table id="bo_view" class="table display" style="width:100%">
          <thead>
          <tr>
            <th>Сценарий</th>
            <th>МР</th>
            <th>МО</th>
            <th>Номер</th>
            <th>Сумма</th>
            <th>Оплачено</th>
            <th>Остаток</th>
            <th>НДС</th>
            <th>Статья</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($budgets as $budget): ?>
            <tr>
              <?php
                $date = date_create($budget['scenario']);
                $_monthsList = array(
                  "1"=>"Янв","2"=>"Фев","3"=>"Мар", "4"=>"Апр","5"=>"Май", "6"=>"Июн",
                  "7"=>"Июл","8"=>"Авг","9"=>"Сен", "10"=>"Окт","11"=>"Ноя","12"=>"Дек");
                $scenario = $_monthsList[date_format($date, "n")].'&nbsp;'.date_format($date, "Y");
                $date = date_create($budget['month_exp']);
                $month_exp = $_monthsList[date_format($date, "n")];
                $date = date_create($budget['month_pay']);
                $month_pay = $_monthsList[date_format($date, "n")];
              ?>
              <td><?= $scenario;?></td>
              <td><?= $month_exp;?></td>
              <td><?= $month_pay;?></td>
              <th><a href="budget/view?id=<?= $budget['id'];?>"><?= $budget['number'];?></a></th>
              <td><?= number_format((double)$budget['summa'], 2, ',', '&nbsp;');?>&nbsp;₽</td>
              <td><?= number_format((double)$budget['payment'], 2, ',', '&nbsp;');?>&nbsp;₽</td>
              <th><?= number_format((double)$budget['summa'] - (double)$budget['payment'], 2, ',', '&nbsp;');?>&nbsp;₽</th>
              <td><?= $budget['vat'];?></td>
              <td><?= $budget['name_budget_item'];?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</main>
<script type="text/javascript" src="assets/DataTables/datatables.min.js"></script>
<script>
  $(function () {
    $('#bo_view').dataTable( {
      "ordering": false,
      "language": {
        "url": "/assets/DataTables/ru.json"
      }
    });
    $('body').on('change', '.filters select', function () {
      let date_y = $('#select_year option:checked'), // Получаем отмеченный чекбокс - Год
        date_m = $('#select_month option:checked'), // Получаем отмеченный чекбокс - Месяц
        data = '';
      date_y.each(function () {
        // Пройдем в цикле по значениям чекбокса - Год
        data += this.value + '-'; // Добавляем в переменную выбранное значение года
      });
      date_m.each(function () {
        // Пройдем в цикле по значениям чекбокса - Месяц
        data += this.value + '-01'; // Добавляем в переменную выбранное значение месяца и день
      });
      if (data) {
        // Если сформирована дата, формируем AJAX запрос
        $.ajax({
          url: location.href,
          data: {filter: data},
          type: 'GET',
          beforeSend: function () {
            // Перед отправкой мы включаем прелоадер
            $('.preloader').fadeIn(100, function() {
              // Обратимся к классу показывающему БО и скроем все отображаемое на экране
              $('#bo_view').hide();
            });
          },
          success: function (res) {
            // Постепенно скроем прелоадер и
            $('.preloader').delay(100).fadeOut('show', function () {
              // в класс показывающий БО за выбранный период подгружаем
              // полученный ответ с сервера и показываем его
              $('#bo_view').html(res).fadeIn();
              let url = location.search.replace(/filter(.+?)(&|$)/g, ''); //$2
              let newURL = location.pathname + url + (location.search ? "&" : "?") + "filter=" + data;
              newURL = newURL.replace('&&', '&');
              newURL = newURL.replace('?&', '?');
              history.pushState({}, '', newURL);
              location.reload();
            });
          },
          error: function (res) {
            alert('Errors');
          }
        });
      }
    });
  });
</script>