<?php declare(strict_types=1);
/**
 * Автор кода = "Oleksha"
 * Дата создания = "27.12.2023"
 * Время создания = "9:40"
 **/

use expenses\Router;

/** Пользовательские маршруты  */
Router::add('^partner/(?P<id>[0-9]+)/?$', ['controller' => 'Partner', 'action' => 'view']);

/** Маршруты по умолчанию */
Router::add('^admin$', ['controller' => 'Main', 'action' => 'index', 'prefix' => 'admin']);
Router::add('^admin/?(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$', ['prefix' => 'admin']);

Router::add('^$', ['controller' => 'Main', 'action' => 'index']);
Router::add('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');