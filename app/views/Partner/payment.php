<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "06.01.2024"
 * Время создания = "14:50"
 **/
?>
<main role="main">
  <div class="container">
    <h1 class="mt-1">Заявка на оплату</h1>
    <?php /** @var array $partner */  if ($partner) : ?>
      <!-- Хлебные крошки -->
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= PATH; ?>">Главная</a></li>
          <li class="breadcrumb-item"><a href="<?= PATH; ?>/partner">Список контрагентов</a></li>
          <li class="breadcrumb-item"><a href="<?= PATH; ?>/partner/<?= $partner['id']; ?>"><?= $partner['name']; ?></a></li>
          <?php /** @var int $type */ if ($type == 1) : ?>
            <li class="breadcrumb-item active" aria-current="page">Просмотр ЗО</li>
          <?php elseif ($type == 2) : ?>
            <li class="breadcrumb-item active" aria-current="page">Изменение ЗО</li>
          <?php else : ?>
            <li class="breadcrumb-item active" aria-current="page">Ввод оплат</li>
          <?php endif; ?>
        </ol>
      </nav>
      <!-- Хлебные крошки -->
      <!-- Вывод ошибок -->
      <div class="row d-flex justify-content-center">
        <div class="col-9">
          <?php if (isset($_SESSION['error_payment'])): ?>
            <div class="alert alert-danger">
              <ul>
                <?php foreach ($_SESSION['error_payment'] as $item): ?>
                  <li><?= $item; ?></li>
                <?php endforeach; ?>
              </ul>
              <?php unset($_SESSION['error_payment']); ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <!-- Вывод ошибок -->
      <div class="row d-flex justify-content-center">
        <div class="col-9">
          <!-- Форма ввода -->
          <form method="post" action="receipt/pay-receipt" id="partner_payment" class="was-validated" novalidate>
            <div class="row g-3">
              <!-- Поле - Наименование контрагента -->
              <div class="col-12 has-feedback">
                <label for="name"><strong>Наименование контрагента</strong></label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Наименование КА" value="<?= $partner['name'] ?? 'Нет данных'; ?>" disabled>
              </div>
              <!-- Поле - Наименование контрагента -->
              <!-- Поле - Дата заявки на оплату -->
              <div class="has-feedback col-md-6">
                <label for="date"><strong>Дата заявки на оплату</strong></label>
                <input type="date" name="date" class="form-control" id="date" placeholder="01.01.2021"
                       value="<?= $_SESSION['form_data']['date'] ?? ($payment['date'] ?? ''); ?>" <?= $type == 1 ? 'disabled' : '' ?>
                       required>
                <div class="invalid-feedback">
                  Введите дату формирования заявки на оплату
                </div>
              </div>
              <!-- Поле - Дата заявки на оплату -->
              <!-- Поле - Номер заявки -->
              <div class="has-feedback col-md-6">
                <label for="number"><strong>Номер заявки</strong></label>
                <input type="text" name="number" class="form-control" id="number" placeholder="Номер"
                       value="<?= $_SESSION['form_data']['number'] ?? ($payment['number'] ?? ''); ?>" <?= $type == 1 ? 'disabled' : '' ?>
                       required>
                <div class="invalid-feedback">
                  Введите номер сформированной заявки на оплату
                </div>
              </div>
              <!-- Поле - Номер заявки -->
              <!-- Поле select - Сумма оплаты -->
              <div class="has-feedback col-md-6">
                <label for="sum_select" id="sum"><strong>Сумма оплаты</strong></label>
                <select name="sum[]" id="sum_select" data-placeholder="Выберите сумму..." class="sum_receipt_select" <?= $type == 1 ? 'disabled' : '' ?> multiple>
                  <?php if ($_SESSION['form_data']['sum']) : ?>
                    <!-- если в сессии находятся сохраненные данные -->
                    <?php /** @var array $receipt_all */ foreach ($receipt_all as $k => $value) : ?>
                      <option value="<?= $value['sum']; ?>" <?php if (in_array($value['sum'], $_SESSION['form_data']['sum'])) echo ' selected'; ?>><?= number_format((float)$value['sum'], 2, '.', '&nbsp;');?></option>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <!-- если нет сохраненных данных -->
                    <?php if ($type == 1) : ?>
                      <!-- Это просмотр уже оплаченной заявки на оплату -->
                      <?php /** @var array $payment */ foreach (explode(';', $payment['sum']) as $value) : ?>
                        <option value="<?= $value; ?>" selected><?= $value; ?></option>
                      <?php endforeach; ?>
                    <?php elseif ($type == 2) : ?>
                      <!-- Это редактирование уже сформированной заявки на оплату -->
                      <?php /** @var array $receipt_all */ foreach ($receipt_all as $k => $value) : ?>
                        <option value="<?= $value['sum']; ?>" data-id="<?= $value['id']; ?>" <?php /** @var array $payment */ if (in_array($value['id'], explode(';', $payment['receipts_id']))) echo ' selected'; ?>><?= number_format((float)$value['sum'], 2, '.', '&nbsp;');?></option>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <!-- Это заполнение новой заявки на оплату -->
                      <?php /** @var array $receipt_all */ foreach ($receipt_all as $k => $value) : ?>
                        <option value="<?= $value['sum']; ?>" data-id="<?= $value['id']; ?>" <?php /** @var array $receipt */ if (in_array($value['id'], explode(';', $receipt['id']))) echo ' selected'; ?>><?= number_format((float)$value['sum'], 2, '.', '&nbsp;');?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </select>
                <div class="invalid-feedback">
                  Выберите оплачиваемую сумму
                </div>
              </div>
              <!-- Поле select - Сумма оплаты -->
              <!-- Поле select - Ставка НДС -->
              <div class="has-feedback col-md-6">
                <label for="vat"><strong>Ставка НДС</strong></label>
                <select class="form-control" name="vat" id="vat" <?= $type == 1 ? 'disabled' : '' ?>>
                  <?php if ($_SESSION['form_data']['vat']) : ?>
                    <!-- если в сессии находятся сохраненные данные -->
                    <?php /** @var array $vats */ foreach ($vats as $vat) : ?>
                      <option value="<?=$vat['vat']?>" <?php if ($_SESSION['form_data']['vat'] == $vat['vat']) { echo ' selected'; } ?>><?=$vat['name']?></option>
                    <?php endforeach; ?>
                    <!-- <option value="1.20" <?php //if ($_SESSION['form_data']['vat'] == '1.20') { echo ' selected'; } ?>>20%</option>
                    <option value="1.10" <?php //if ($_SESSION['form_data']['vat'] == '1.10') { echo ' selected'; } ?>>10%</option>
                    <option value="1.00" <?php //if ($_SESSION['form_data']['vat'] == '1.00') { echo ' selected'; } ?>>Без НДС</option>-->
                  <?php else : ?>
                    <!-- если нет сохраненных данных -->
                    <?php /** @var array $payment */ if ($payment) : ?>
                      <!-- если сформирована заявка на оплату -->
                      <?php /** @var array $vats */ foreach ($vats as $vat) : ?>
                        <option value="<?=$vat['vat']?>" <?php if ($payment['vat'] == $vat['vat']) { echo ' selected'; } ?>><?=$vat['name']?></option>
                      <?php endforeach; ?>
                      <!--<option value="1.20" <?php //if ($payment['vat'] == '1.20') { echo ' selected'; } ?>>20%</option>
                      <option value="1.10" <?php //if ($payment['vat'] == '1.10') { echo ' selected'; } ?>>10%</option>
                      <option value="1.00" <?php //if ($payment['vat'] == '1.00') { echo ' selected'; } ?>>Без НДС</option>-->
                    <?php else : ?>
                      <!-- если формируется новая заявка на оплату -->
                      <?php /** @var array $vats */ foreach ($vats as $vat) : ?>
                        <option value="<?=$vat['vat']?>" <?php /** @var array $receipt */ if ($receipt['vat_id'] == $vat['id']) { echo ' selected'; } ?>><?=$vat['name']?></option>
                      <?php endforeach; ?>
                      <!--<option value="1.20" <?php //if ($partner['vat'] == '1.20') { echo ' selected'; } ?>>20%</option>
                      <option value="1.10" <?php //if ($payment['vat'] == '1.10') { echo ' selected'; } ?>>10%</option>
                      <option value="1.00" <?php //if ($partner['vat'] == '1.00') { echo ' selected'; } ?>>Без НДС</option>-->
                    <?php endif; ?>
                  <?php endif; ?>
                </select>
                <div class="invalid-feedback">
                  Выберите ставку НДС
                </div>
              </div>
              <!-- Поле select - Ставка НДС -->
              <!-- Поле select - Номера приходов -->
              <div class="has-feedback col-md-6">
                <label for="receipt_select"><strong>Номера приходов</strong></label><br>
                <select name="receipt[]" id="receipt_select" data-placeholder="Выберите приход..." class="number_receipt_select" multiple <?= $type == 1 ? 'disabled' : '' ?>>
                  <?php if ($_SESSION['form_data']['receipt']) : ?>
                    <!-- если в сессии находятся сохраненные данные -->
                    <?php /** @var array $receipt_all */ foreach ($receipt_all as $k => $value) : ?>
                      <option value="<?= $value['id']; ?>" <?php if (in_array($value['id'], $_SESSION['form_data']['receipt'])) echo ' selected'; ?>><?= $value['number']; ?></option>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <!-- если нет сохраненных данных -->
                    <?php if ($type == 1) : ?>
                      <!-- Это просмотр уже оплаченной заявки на оплату -->
                      <?php /** @var array $payment */ foreach (explode(';', $payment['receipt']) as $value) : ?>
                        <option value="<?= $value; ?>" selected><?= $value; ?></option>
                      <?php endforeach; ?>
                    <?php elseif ($type == 2) : ?>
                      <!-- Это редактирование уже сформированной заявки на оплату -->
                      <?php /** @var array $receipt_all */ foreach ($receipt_all as $k => $value) : ?>
                        <option value="<?= $value['id']; ?>" <?php if (in_array($value['id'], explode(';', $payment['receipts_id']))) echo ' selected'; ?>><?= $value['number']; ?></option>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <!-- Это заполнение новой заявки на оплату -->
                      <?php /** @var array $receipt_all */ foreach ($receipt_all as $k => $value) : ?>
                        <option value="<?= $value['id']; ?>" <?php /** @var array $receipt */ if (in_array($value['id'], explode(';', $receipt['id']))) echo ' selected'; ?>><?= $value['number']; ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </select>
                <div class="invalid-feedback">
                  Выберите приход для оплаты
                </div>
              </div>
              <!-- Поле select - Номера приходов -->
              <!-- Поле - Дата оплаты -->
              <div class="has-feedback col-md-6">
                <label for="date_pay"><strong>Дата оплаты</strong></label>
                <input type="date" name="date_pay" class="form-control" id="date_pay" placeholder=""
                       value="<?= $_SESSION['form_data']['date_pay'] ?? ($payment['date_pay'] ?? ''); ?>"
                       required <?= $type == 1 ? 'disabled' : '' ?>>
                <div class="invalid-feedback">
                  Введите дату предполагаемой оплаты
                </div>
              </div>
              <!-- Поле - Дата оплаты -->
              <!-- Поле select - Номера ЕР -->
              <div class="has-feedback col-md-6">
                <label for="num_er"><strong>Номера ЕР</strong></label><br>
                <select name="num_er[]" id="num_er" data-placeholder="Выберите ЕР..." class="num_er_select" multiple <?= $type == 1 ? 'disabled' : '' ?>>
                  <?php if ($_SESSION['form_data']['num_er']) : ?>
                    <!-- если в сессии находятся сохраненные данные -->
                    <?php /** @var array $ers */ foreach ($ers as $k => $v) : ?>
                      <optgroup label="<?= $v['budget']; ?>">
                        <option value="<?= $v['id']; ?>" <?php if (in_array($v['id'], $_SESSION['form_data']['num_er'])) echo ' selected'; ?>><?= $v['number']; ?></option>
                      </optgroup>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <!-- если нет сохраненных данных -->
                    <?php if ($type == 1) : ?>
                      <!-- Это просмотр уже оплаченной заявки на оплату -->
                      <?php /** @var array $payment */ foreach (explode(';', $payment['num_er']) as $item) : ?>
                        <option value="<?= $item; ?>" selected><?= $item; ?></option>
                      <?php endforeach; ?>
                    <?php elseif ($type == 2) : ?>
                      <!-- Это редактирование уже сформированной заявки на оплату -->
                      <?php /** @var array $ers */ foreach ($ers as $k => $v) : ?>
                        <optgroup label="<?= $v['budget']; ?>">
                          <option value="<?= $v['id']; ?>" <?php /** @var array $payment */ if (in_array($v['id'], explode(';', $payment['ers_id']))) echo ' selected'; ?>><?= $v['number']; ?></option>
                        </optgroup>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <!-- Это заполнение новой заявки на оплату -->
                      <?php /** @var array $ers */ foreach ($ers as $k => $v) : ?>
                        <optgroup label="<?= $v['budget']; ?>">
                          <option value="<?= $v['id']; ?>"><?= $v['number']; ?></option>
                        </optgroup>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </select>
                <div class="invalid-feedback">
                  Выберите ЕР которые служат для оплаты
                </div>
              </div>
              <!-- Поле select - Номера ЕР -->
              <!-- Поле - Сумма ЕР -->
              <div class="has-feedback col-md-6">
                <label for="sum_er"><strong>Сумма ЕР</strong></label>
                <?php if (isset($_SESSION['form_data']['sum_er'])) {
                  $str = implode(';', $_SESSION['form_data']['sum_er']);
                } ?>
                <input type="text" name="sum_er[]" class="form-control" id="sum_er" placeholder="" value="<?= $str ?? ($payment['sum_er'] ?? ''); ?>" required <?= $type == 1 ? 'disabled' : '' ?>>
                <div class="invalid-feedback">
                  Введите суммы для оплаты
                </div>
              </div>
              <!-- Поле - Сумма ЕР -->
              <!-- Поле - Номера БО -->
              <div class="has-feedback col-md-6">
                <label for="num_bo"><strong>Номера БО</strong></label>
                <input type="text" name="num_bo" class="form-control" id="num_bo" placeholder="Номер документа"
                       value="<?= $_SESSION['form_data']['num_bo'] ?? ($payment['num_bo'] ?? ''); ?>"
                       required <?= $type == 1 ? 'disabled' : '' ?>>
                <div class="invalid-feedback">
                  Введите номера БО используемых для оплаты
                </div>
              </div>
              <!-- Поле - Номера БО -->
              <!-- Поле - Сумма БО -->
              <div class="has-feedback col-md-6">
                <label for="sum_bo"><strong>Сумма БО</strong></label>
                <input type="text" name="sum_bo" class="form-control" id="sum_bo" placeholder=""
                       value="<?= $_SESSION['form_data']['sum_bo'] ?? ($payment['sum_bo'] ?? ''); ?>"
                       required <?= $type == 1 ? 'disabled' : '' ?>>
                <div class="invalid-feedback">
                  Введите суммы БО используемых для оплаты
                </div>
              </div>
              <!-- Поле - Сумма БО -->
              <!-- Скрытые поля -->
              <input type="hidden" name="id_partner" value="<?= $partner['id'] ?? ''; ?>">
              <input type="hidden" name="id" value="<?= $payment['id'] ?? ''; ?>">
              <input type="hidden" name="inn" value="<?= $partner['inn'] ?? ''; ?>">
              <input type="hidden" name="type" value="<?= $type ?? ''; ?>">
              <input type="hidden" name="parent" value="<?= $parent ?? ''; ?>">
              <!-- Скрытые поля -->
              <!-- Кнопки формы -->
              <div class="form-group text-center">
                <?php if ($type == 1) : ?>
                  <button type="button" class="btn btn-primary mt-3" onclick="history.back();">Закрыть</button>
                <?php else : ?>
                  <?php unset($_SESSION['form_data']); ?>
                  <?php if ($type == 2) : ?>
                    <button type="submit" class="btn btn-primary mt-3">Сохранить изменения</button>
                  <?php else : ?>
                    <button type="submit" class="btn btn-primary mt-3">Создать оплату</button>
                  <?php endif; ?>
                  <button type="button" class="btn btn-primary mt-3" id="copyText">Скопировать БО</button>
                <?php endif; ?>
              </div>
              <!-- Кнопки формы -->
            </div>
          </form>
          <!-- Форма ввода -->
        </div>
      </div>
    <?php else : ?>
      <h3>Отсутствуют данные для обработки</h3>
    <?php endif; ?>
  </div>
</main>
<script type="text/javascript" src="assets/chosen/chosen.jquery.min.js"></script>
<script>
  $(function () {
    $(".number_receipt_select").chosen({
      width: "100%"
    });
    $(".num_er_select").chosen({
      width: "100%"
    });
    $(".sum_receipt_select").chosen({
      width: "100%"
    });
    $("#sum_select").change(function () {
      const ids = $(this).val();
      let sum = 0;
      for (let i = 0; i < ids.length; i++) {
        /*let $select = $(this);
        console.log($select.children().eq(i).data('number'));*/
        sum += parseFloat(ids[i]);
      }
      if ($('#sum_er').val().length < 5) {
        $('#sum_er').val(sum.toFixed(2));
      }
      if ($('#sum_bo').val().length < 5) {
        $('#sum_bo').val(sum.toFixed(2));
      }
    });
    /* копирование текста в буфер обмена */
    let btn = document.getElementById("copyText");
    /* вызываем функцию при нажатии на кнопку */
    btn.onclick = function () {
      let num = document.getElementById("num_bo");
      let sum = document.getElementById("sum_bo");
      let num_arr = num.value;
      let sum_arr = sum.value;
      num_arr = num_arr.split(';');
      sum_arr = sum_arr.split(';');
      let text = "";
      num_arr.forEach(function (elem, num) {
        text = text + elem.slice(0, -5) + " - " + sum_arr[num] + " руб; ";
      });

      let copyTextarea = document.createElement("textarea");
      copyTextarea.style.position = "fixed";
      copyTextarea.style.opacity = "0";
      copyTextarea.textContent = text;document.body.appendChild(copyTextarea);
      copyTextarea.select();
      document.execCommand("copy");
      document.body.removeChild(copyTextarea);
    }
  });
</script>
<script>
  let search_bo = document.getElementById('sum_bo');
  let search_er = document.getElementById('sum_er');
  search_bo.oninput = function () {
    this.value = this.value.replace(/\s/g, '');
    this.value = this.value.replace(/,/g, '.');
  };
  search_er.oninput = function () {
    this.value = this.value.replace(/\s/g, '');
    this.value = this.value.replace(/,/g, '.');
  };
</script>
