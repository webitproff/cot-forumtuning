<?php
/**
 * Russian Language File for ForumTuning
 *
 * forumtuning plugin for CMF Cotonti 0.9.26, PHP 8.4+
 * Filename: plugins/forumtuning/lang/forumtuning.ru.lang.php
 *
 *
 * Date: May 6Th, 2026
 * @package forumtuning
 * @version 2.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/cot-forumtuning
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Info
 */
$L['info_name'] = 'Forum Tuning';
$L['info_desc'] = 'Всякое для расширения функционала форума';

/**
 * Plugin Config
 */
$L['cfg_blacktreecatsforums'] = 'Черный список категорий';
$L['cfg_blacktreecatsforums_hint'] = 'Коды категорий из структуры модуля Forums через запятую';



// forums structure localization title
if (isset($structure['forums']['rules']) && is_array($structure['forums']['rules'])) {
    $structure['forums']['rules']['title'] = 'Правила поведения пользователей на платформе.';
    $structure['forums']['rules']['tpath'] = 'Правила платформы';
    $structure['forums']['rules']['desc'] = 'Общие и специальные нормы поведения, а также меры к нарушителям. Форум поддержки пользователей!';
}


$L['forums_topiclocked'] = 'Тема заблокирована для новых сообщений. Но вы можете создать свою тему обсуждения в разделе форума, который соответствует вашей тематике.';

$L['forumtuning_messages'] = 'Сообщений';
$L['forumtuning_note'] = 'Выберите нужную категорию и введите ключевое слово для поиска. После каждого поискового запроса нужно нажать красную кнопку "сброс фильтра"';
$L['forumtuning_ReserFilter'] = 'Сброс фильтра';
$L['forumtuning_StartSearch'] = 'Начать поиск';
$L['forumtuning_ForumCategories'] = 'Категории форума'; 
$L['forumtuning_allcategories'] = 'Все категории';
