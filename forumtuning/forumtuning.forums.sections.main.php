<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=forums.sections.main
  [END_COT_EXT]
  ==================== */
/**
 * forumtuning code for hook forums.sections.main in modules/forums/inc/forums.sections.php
 * Файл: plugins/forumtuning/forumtuning.forums.sections.main.php
 *
 * Date: May 6Th, 2026
 * @package forumtuning
 * @version 2.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/cot-forumtuning
 * @license BSD
 */
 
defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('forums', 'module');
require_once cot_langfile('forumtuning', 'plug');
require_once cot_incfile('forumtuning', 'plug');
$s = cot_import('s', 'G', 'TXT');
$c = cot_import('c', 'G', 'TXT');
$sq = cot_import('sq', 'G', 'TXT');


// Проверяем, существует ли ключ в массиве структуры форума, чтобы избежать ошибки Undefined array key
$c = $c ?? ''; // Если переменная $c не задана, присваиваем ей пустое значение

// Проверяем, существует ли указанный ключ в структуре форума, чтобы избежать ошибки доступа к null
$tpl_name = isset(Cot::$structure['forums'][$c]['tpl']) ? Cot::$structure['forums'][$c]['tpl'] : '';

// Генерируем путь к шаблону, используя безопасное извлечение значения
$mskin = cot_tplfile(array('forums', 'sections', $tpl_name));


//$mskin = cot_tplfile(array('forums' ,'sections', Cot::$structure['forums'][$c]['tpl']));
// Определяем шаблон для основного контента
$t = new XTemplate($mskin);




// Форма поиска
$list_url_path = array('c' => $c, 'sq' => $sq);
$search_action_url = cot_url('forums', $list_url_path, '', true);
$t->assign(array(
    'SEARCH_ACTION_URL' => $search_action_url,
    'SEARCH_SQ' => cot_inputbox('text', 'sq', !empty($sq) ? htmlspecialchars($sq) : '', 'class="schstring"'),
    'SEARCH_CAT' => cot_forums_selectcat_select2($c, 'c'),
));

// Обработка топиков
$page = cot_import('page', 'G', 'INT') ?: 1;
$items_per_page = 10;

$where = [];
/* if (!empty($c)) {
    $catsub = cot_structure_children('forums', $c);
    $where['cat'] = "t.ft_cat IN ('" . implode("','", $catsub) . "')";
} */
if (!empty($c) && isset(Cot::$structure['forums'][$c])) {
    $catsub = cot_structure_children('forums', $c);
    $where['cat'] = "t.ft_cat IN ('" . implode("','", $catsub) . "')";
}
if (!empty($sq)) {
    $words = explode(' ', preg_replace("'\s+'", " ", trim($sq)));
    $sqlsearch = '%' . implode('%', $words) . '%';
    $where['search'] = "(t.ft_title LIKE " . Cot::$db->quote($sqlsearch) . " OR p.fp_text LIKE " . Cot::$db->quote($sqlsearch) . ")";
}
$where['state'] = "t.ft_state=0";

$where_clause = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$count_sql = "SELECT COUNT(DISTINCT t.ft_id) AS total 
              FROM $db_forum_topics AS t 
              LEFT JOIN $db_forum_posts AS p ON t.ft_id = p.fp_topicid 
              $where_clause";
$count_res = Cot::$db->query($count_sql);
$total_topics = (int)$count_res->fetchColumn();

$max_pages = max(1, ceil($total_topics / $items_per_page));
if ($page > $max_pages) {
    cot_redirect(cot_url('forums', ['c' => $c, 'sq' => $sq, 'page' => $max_pages]));
}
$d = ($page - 1) * $items_per_page;
if ($d < 0) {
    $d = 0;
}

$sql = "SELECT DISTINCT t.ft_id, t.ft_title, t.ft_creationdate, t.ft_firstpostername, t.ft_postcount
        FROM $db_forum_topics AS t
        LEFT JOIN $db_forum_posts AS p ON t.ft_id = p.fp_topicid
        $where_clause
        ORDER BY t.ft_creationdate DESC
        LIMIT " . (int)$items_per_page . " OFFSET " . (int)$d;

$res = Cot::$db->query($sql);
$latest_topics = [];
foreach ($res->fetchAll() as $row) {
    $topic_url = cot_url('forums', 'm=posts&q=' . $row['ft_id']);
    $latest_topics[] = [
        'URL' => $topic_url,
        'TITLE' => htmlspecialchars($row['ft_title']),
        'DATE' => cot_date('d.m.Y H:i', (int)$row['ft_creationdate']),
        'AUTHOR' => htmlspecialchars($row['ft_firstpostername']),
        'COUNT' => (int)$row['ft_postcount'],
    ];
}

$pagenav = cot_pagenav('forums', $list_url_path, $d, $total_topics, $items_per_page, 'page');

if (!empty($latest_topics)) {
    foreach ($latest_topics as $topic) {
        $t->assign($topic);
        $t->parse('MAIN.LATEST_TOPICS.LATEST_TOPICS_ROW');
    }
    $t->assign([
        'PREVIOUS_PAGE' => !empty($pagenav['prev']) ? $pagenav['prev'] : '',
        'PAGINATION' => !empty($pagenav['main']) ? $pagenav['main'] : '',
        'NEXT_PAGE' => !empty($pagenav['next']) ? $pagenav['next'] : '',
    ]);
    $t->parse('MAIN.LATEST_TOPICS');
}
// Последние 10 сообщений форума
$sql = "SELECT p.fp_id, p.fp_topicid, p.fp_text, p.fp_postername, p.fp_creation,
               t.ft_title
        FROM $db_forum_posts AS p
        LEFT JOIN $db_forum_topics AS t ON t.ft_id = p.fp_topicid
        WHERE t.ft_state = 0
        ORDER BY p.fp_creation DESC
        LIMIT 7";
// cot_url('forums', 'm=posts&q=' . $stat['fs_lt_id'] . '&n=last', '#bottom')
$res = Cot::$db->query($sql);
$latest_posts = [];
foreach ($res->fetchAll() as $row) {
    $post_url = cot_url('forums', 'm=posts&id=' . $row['fp_id']);
    $latest_posts[] = [
        'POST_URL' => $post_url,
        'POST_TEXT_SHORT' => cot_cutstring(strip_tags($row['fp_text']), 100),
        'POST_AUTHOR' => htmlspecialchars($row['fp_postername']),
        'POST_DATE' => cot_date('d.m.Y H:i', (int)$row['fp_creation']),
        'POST_TOPIC_TITLE' => cot_cutstring(htmlspecialchars($row['ft_title']), 45),
    ];
}

if (!empty($latest_posts)) {
    foreach ($latest_posts as $post) {
        $t->assign($post);
        $t->parse('MAIN.LATEST_POSTS.LATEST_POSTS_ROW');
    }
    $t->parse('MAIN.LATEST_POSTS');
}

