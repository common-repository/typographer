=== Typographer ===
Contributors: axaple, zaebalo
Donate link: http://axaple.ru/
Tags: axaple, typograph, типограф, typograf, typographer, висячая пунктуация
Requires at least: 3.1
Tested up to: 4.4.2
Stable tag: 1.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Типограф для Wordpress автоматически приводит в порядок тексты для публикации сайте.

== Description ==

Типограф для Wordpress автоматически приводит в порядок тексты для публикации сайте. Неразрывные пробелы для предлогов, союзов и сокращений, висячая пунктуация и замена символов.

Возможности:
* Замена обычных пробелов после предлогов и союзов на неразрывные;
* Замена символов кавычек, многоточия, тире на правильные;
* Оборачивание открывающих скобок и кавычек в классы, для свешивания;
* Замена (c) (tm) (r) на © ™ ®.

== Installation ==

1. Загрузите файлы плагина в папку /wp-content/plugins/typographer своего сайта или установите плагин через каталог плагинов WordPress.
2. Активируйте плагин в разделе «Плагины» вашего сайта.

Свешивание символов осуществляется за счет добавления отрицательных отступов для самих символов и добавление такого же положительного отступа для пробела.

<style>
/* Кавычка */
.hpquote-space {
  margin-right: 0.7em;
}
.hpquote {
  margin-left: -0.7em;
}
/* Скобка */
.hpbrace-space {
  margin-right: 0.5em;
}
.hbracet {
  margin-left: -0.5em;
}
</style>

== Changelog ==

= 1.1.5 =
* Add version support.
* <a> and <img> fix.
* Another change.

= 1.1.4 =
* Add version support.
* <script> and <pre> fix.
* Another change.

= 1.1.3 =
* Add version support.
* Another change.

= 1.1.2 =
* Add version support.
* Another change.

= 1.1.1 =
* Add html tags support.
* Another change.

= 1.0 =
* A change since the previous version.
* Another change.