<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "05.01.2024"
 * Время создания = "12:58"
 **/

/** @var array $payments */
if ($payments) : ?>
  <table class="table table-striped table-sm">
    <thead>
    <tr class="table-active text-center">
      <td class="h-100 align-middle text-primary">Сумма ЕР</td>
      <th colspan="2" scope="col" class="h-100 align-middle text-primary"><?= /** @var array $er */
        number_format((double)$er['summa'], 2, ',', '&nbsp;'); ?>&nbsp;₽
      </th>
    </tr>
    <tr class="table-active text-center">
      <th scope="col" class="h-100 align-middle">Дата расхода</th>
      <th scope="col" class="h-100 align-middle">Сумма</th>
      <th scope="col" class="h-100 align-middle">Без НДС</th>
    </tr>
    </thead>
    <tbody>
    <?php $sum = 0.00;
    $sum_vat = 0.00; $vat = null; ?>
    <?php foreach ($payments as $payment) : ?>
      <?php
      $ids = explode(';', trim($payment['ers_id']));
      $sums = explode(';', trim($payment['sum_er']));
      $key = array_search($er['id'], $ids);
      $sum += (double)$sums[$key];
      $vat = (double)$payment['vat'];
      $sum_vat += (double)$sums[$key] / $vat;
      ?>
      <tr>
        <td class="text-center h-100 align-middle"><?= $payment['date_pay']; ?></td>
        <td class="text-center h-100 align-middle"><?= number_format((double)$sums[$key], 2, ',', '&nbsp;'); ?>&nbsp;₽</td>
        <td class="text-center h-100 align-middle"><?= number_format(($sums[$key] / $vat), 2, ',', '&nbsp;'); ?>&nbsp;₽
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr class="table-active text-center">
      <th scope="col" class="h-100 align-middle">Итого по оплатам</th>
      <th scope="col" class="h-100 align-middle"><?= number_format($sum, 2, ',', '&nbsp;'); ?>&nbsp;₽</th>
      <th scope="col" class="h-100 align-middle"><?= number_format($sum_vat, 2, ',', '&nbsp;'); ?>&nbsp;₽</th>
    </tr>
    <tr class="table-active text-center">
      <td class="h-100 align-middle text-primary">Остаток по ЕР</td>
      <th colspan="2" scope="col"
          class="h-100 align-middle text-primary"><?= number_format(($er['summa'] - $sum / $vat), 2, ',', '&nbsp;'); ?>
        &nbsp;₽
      </th>
    </tr>
    </tfoot>
  </table>
<?php else: ?>
  <h3>Для указанного документа данные не получены.</h3>
<?php endif; ?>
