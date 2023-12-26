<?php
/**
 * Автор кода = "Oleksha"
 * Дата создания = "26.12.2023"
 * Время создания = "10:44"
 **/

namespace expenses;

trait TSingletone
{

  private static $instance;

  public static function instance() {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

}