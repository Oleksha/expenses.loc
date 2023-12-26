<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "26.12.2023"
 * Время создания = "11:05"
 **/

/**
 * Вывод на экран значений переменных
 * @param mixed $arr значение переменной
 */
function debug($arr) {
  echo '<pre>' . print_r($arr, true) . '</pre>';
}

/**
 * Перенаправление на указанную страницу
 * @param string $http cстраница для переадресации
 */
function redirect($http = false) {
  if ($http) {
    $redirect = $http;
  } else {
    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : PATH;
  }
  header("Location: $redirect");
  exit;
}

/**
 * Преобразует специальные символы в HTML-сущности
 * @param string $str строка для преобразования
 * @return string
 */
function h($str) {
  return htmlspecialchars($str, ENT_QUOTES);
}

/**
 * Возвращает разницу из элементов массива
 * @param $arr1 array массив содержащий большее число элементов
 * @param $arr2 array массив содержащий часть элементов первого массива
 * @return array
 */
function my_array_diff(&$arr1, &$arr2): array {
  $diff = [];
  if(is_array($arr1) and is_array($arr2)) {
    foreach ($arr1 as $item) {
      $key = false;
      foreach ($arr2 as $value) {
        if ($item['number'] === $value['number']) {
          $key = true;
          break;
        }
      }
      if (!$key) {
        $diff[] = $item;
      }
    }
  }
  return $diff;
}

/**
 * Преобразует данные в формат НОМЕР/ГОД
 * @param string $number номер
 * @param string $date строковое представление даты
 * @return string
 */
function dataYear($number, $date) {
  $year = date('Y', strtotime($date));
  return $number . '/' . $year;
}