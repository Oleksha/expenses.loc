<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "29.12.2023"
 * Время создания = "16:53"
 **/
?>
<main class="flex-shrink-0">
  <div class="container">
    <h1 class="mt-1 mb-5"><strong>Приходы требующие оплаты</strong></h1>
    <?php /** @var array $receipts */
    if($receipts): ?>
      <table id="main_index" class="table display" style="width:100%">
        <thead>
        <tr>
          <th>Имя КА</th>
          <th>Документ</th>
          <th>Сумма</th>
          <th>Дата оплаты</th>
          <th>Статус</th>
          <th>Действие</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($receipts as $item): ?>
          <?php
          $status = '';
          $color= '';
          if (!$item['num_pay']) {
            $status = 'Приход не обработан';
            $color = ' table-danger';
          } elseif (!$item['date_pay']) {
            $status = "Подано на оплату<br><b style='font-size: 10px; display: block; width: 150px;'>{$item['pay']['number']} от {$item['pay']['date']}</b>";
            $color = ' table-warning';
          } elseif ($item['date_pay'] = date('Y-m-d')) {
            $status = "Оплачено<br><b style='font-size: 10px; display: block; width: 150px;'>{$item['pay']['number']} от {$item['pay']['date']}</b>";
            $color = ' table-success';
          }
          if ($item['pay_date']) {
            $pay = $item['pay_date'];
          } else {
            if ($item['delay']) {
              $date_elements = explode('-', $item['date']);
              try {
                $date = new DateTime($item['date']);
              } catch (Exception $e) {
                throw new \Exception("Невозможно создать дату", 199);
              }
              $delay = (int)$item['delay'];
              date_add($date, date_interval_create_from_date_string("$delay days"));
              $pay = date_format($date, 'Y-m-d');
            } else {
              $pay = 'Нет данных';
            }
          }
          $text = '';
          if ($item['type'] == 'ПТ') {
            $text = 'Поступление товаров и услуг ';
          } elseif ($item['type'] == 'ЗП') {
            $text = 'Заказ поставщику ';
          } elseif ($item['type'] == 'АО') {
            $text = 'Авансовый отчет ';
          }
          ?>
          <tr>
            <th><a href="partner/<?= $item['partner_id'];?>"><?= $item['partner'];?></a></th>
            <td><?= $text;?><?= $item['number'];?> от <?= $item['date'];?></td>
            <td><?= number_format((double)$item['sum'], 2, ',', '&nbsp;');?>&nbsp;₽</td>
            <td><?= $pay;?></td>
            <td><?= $status;?></td>
            <td>
              <?php if ($color == ' table-warning') : ?>
                <a class="btn btn-outline-success main_pay_link" data-toggle="tooltip" data-placement="top" title="Ввести оплату" data-id_receipt="<?= $item['id'];?>" data-bs-toggle="modal" data-bs-target="#main-payment-modal">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z"/>
                    <path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1h-.003zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195l.054.012z"/>
                    <path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083c.058-.344.145-.678.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1H1z"/>
                    <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 5.982 5.982 0 0 1 3.13-1.567z"/>
                  </svg>
                </a>
                <?php  $links = "partner/payment?receipt=" . $item['id'] . "&type=2&parent=main"; $text = "Изменить"; ?>
              <?php else : ?>
                <?php if ($color == ' table-success') : ?>
                  <?php  $links = "partner/payment?receipt=" . $item['id'] . "&type=1&parent=main"; $text = "Просмотреть"; ?>
                <?php else : ?>
                  <?php  $links = "partner/payment?receipt=" . $item['id'] . "&type=3&parent=main"; $text = "Создать"; ?>
                <?php endif; ?>
              <?php endif; ?>
              <a href="<?= $links; ?>" class="btn btn-outline-warning" data-toggle="tooltip" data-placement="top" title="<?= $text; ?>" data-id_receipt="<?= $item['id'];?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                  <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"></path>
                </svg>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</main>

<div id="main-payment-modal" class="modal fade" tabindex="-1" data-bs-backdrop="static" aria-labelledby="main-payment-modal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title"><strong>Приход оплачен</strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <form method="post" action="main/pay" id="pay_enter" role="form" class="was-validated">
        <div class="row g-3 modal-body">

        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
          <button type="submit" class="btn btn-primary">Оплатить</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="assets/DataTables/datatables.min.js"></script>
<script>
  $(function () {
    $('body').on('click', '.main_pay_link', function (e) {
      e.preventDefault(); // отменяем действие по умолчанию для ссылки или кнопки
      // получаем необходимые нам данные
      let id = $(this).data('id_receipt') // идентификатор прихода
      // отправляем стандартный аякс запрос на сервер
      $.ajax({
        url: '/main/pay', // всегда указываем от корня
        data: {id: id}, // передаем данные
        type: 'GET', // тип передаваемого запроса
        success: function (res) {
          // если данные получены
          showModal(res, "#main-payment-modal");
        },
        error: function () {
          // если данных нет или запрос не дошел
          alert('Ошибка получения данных с сервера! Попробуйте позже.');
        }
      });
    });
    function showModal(data, object) {
      // выводим содержимое страницы
      $(object + ' .modal-body').html(data);
    }
  });
</script>
<script>
  $(function () {
    new DataTable('#main_index', {
      order: [ 3, "asc" ],
      createdRow: function ( row, data ) {
        let str = data[4];
        if ( str.substring(0, 16) === "Подано на оплату" ) {
          $('td', row).eq(3).addClass('table-warning');
        }
        if ( str === "Приход не обработан" ) {
          $('td', row).eq(3).addClass('table-danger');
        }
        if ( str.substring(0, 8) === "Оплачено" ) {
          $('td', row).eq(3).addClass('table-success');
        }
      },
      language: {
        url: "/assets/DataTables/ru.json"
      },
      lengthMenu: [
        [8, 15, 25, -1],
        [8, 15, 25, 'All']
      ],
      columnDefs: [
        {
          target: 1,
          searchable: false
        },
        {
          target: 3,
          searchable: false
        },
        {
          target: 4,
          searchable: false,
          sClass: "text-center"
        },
        {
          target: 5,
          searchable: false,
          sClass: "text-center"
        }
      ]
    });
  });
</script>