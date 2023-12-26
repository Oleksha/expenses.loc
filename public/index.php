<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "25.12.2023"
 * Время создания = "22:18"
 **/
require_once dirname(__DIR__) . '/config/init.php';
require_once LIBS . '/functions.php';

new \expenses\App();

throw new Exception('Страница не найдена', 505);