<?php
/**
 * Forums API
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL.');

// Requirements
require_once cot_langfile('forums', 'module');
require_once cot_incfile('forums', 'module');
require_once cot_incfile('forums', 'module', 'functions');
require_once cot_incfile('forums', 'module', 'resources');
require_once cot_incfile('extrafields');

$c = cot_import('c', 'G', 'TXT'); // cat code
$s = cot_import('s', 'G', 'TXT');

/**
 * Формирует дерево категорий для модуля 'forums'. blacklist cats filter
 *
 * @param string $parent Код родительской категории
 * @param string|array $selected Код(ы) выбранной категории
 * @param int $level Текущий уровень вложенности
 * @param string $template Код шаблона
 * @return string Отрендеренный HTML-код дерева категорий
 */
function cot_build_structure_forums_tree($parent = '', $selected = '', $level = 0, $template = '')
{
    // Объявление глобальных переменных для доступа к структуре, конфигурации и базе данных
    global $structure, $cfg, $db, $cot_extrafields, $db_structure, $db_forum_topics, $db_forum_posts;

    // Получение конфигурации черного списка категорий для исключения
    $blacklist_cfg = $cfg['plugin']['forumtuning']['blacktreecatsforums'] ?? '';
    // Преобразование конфигурации черного списка в массив
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));
	
    // Подсчёт общего количества топиков по всему форуму с учётом чёрного списка
    $where = [];
    $where['state'] = "t.ft_state=0"; // Только публичные темы
    if (!empty($blacklist)) {
        $where['blacklist'] = "t.ft_cat NOT IN ('" . implode("','", $blacklist) . "')";
    }
    $where_clause = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    $count_sql = "SELECT COUNT(DISTINCT t.ft_id) AS total 
                  FROM $db_forum_topics AS t 
                  LEFT JOIN $db_forum_posts AS p ON t.ft_id = p.fp_topicid 
                  $where_clause";
    $count_res = Cot::$db->query($count_sql);
    $total_topics = (int)$count_res->fetchColumn();
	
    // Выполнение хуков для события forums.tree.first
    /* === Hook === */
    foreach (cot_getextplugins('forums.tree.first') as $pl) {
        // Подключение файла плагина
        include $pl;
    }
    /* ===== */

    // Определение дочерних категорий
    if (empty($parent)) {
        // Инициализация пустого массива для дочерних категорий
        $children = [];
        // Получение всех категорий модуля форумов
        $allcat = cot_structure_children('forums', '');
        // Перебор всех категорий
        foreach ($allcat as $x) {
            // Проверка, что категория находится на корневом уровне и не в черном списке
            if (
                mb_substr_count($structure['forums'][$x]['path'], ".") == 0 &&
                !in_array($x, $blacklist)
            ) {
                // Добавление категории в массив дочерних
                $children[] = $x;
            }
        }
    } else {
        // Получение подкатегорий для указанной родительской категории с фильтрацией по черному списку
        $children = isset($structure['forums'][$parent]['subcats']) 
            ? array_filter($structure['forums'][$parent]['subcats'], function($cat) use ($blacklist) {
                // Возвращает true для подкатегорий, не входящих в черный список
                return !in_array($cat, $blacklist);
            }) 
            : [];
    }

    // Инициализация шаблона с использованием XTemplate
    $t1 = new XTemplate(cot_tplfile('forumtuning.forums.tree', 'plug'));

    // Выполнение хуков для события forums.tree.main
    /* === Hook === */
    foreach (cot_getextplugins('forums.tree.main') as $pl) {
        // Подключение файла плагина
        include $pl;
    }
    /* ===== */

    // Проверка, есть ли дочерние категории
    if (count($children) == 0) {
        // Возврат пустой строки, если дочерних категорий нет
        return '';
    }

    // Инициализация переменной заголовка
    $title = '';
    // Инициализация переменной описания
    $desc = '';
    // Инициализация переменной счетчика
    $count = 0;
    // Инициализация переменной иконки
    $icon = '';
    // Проверка, задана ли родительская категория и существует ли она
    if (!empty($parent) && isset($structure['forums'][$parent])) {
        // Установка заголовка родительской категории
        $title = $structure['forums'][$parent]['title'];
        // Установка описания родительской категории
        $desc = $structure['forums'][$parent]['desc'];
        // Установка счетчика родительской категории
        $count = $structure['forums'][$parent]['count'];
        // Установка иконки родительской категории
        $icon = $structure['forums'][$parent]['icon'];
    }
    // Назначение переменных шаблона для родительской категории
    $t1->assign([
        // Экранирование заголовка для безопасного вывода
        "TITLE" => htmlspecialchars($title),
        // Назначение описания
        "DESC" => $desc,
        // Назначение счетчика
        "COUNT" => $count,
        // Назначение иконки
        "ICON" => $icon,
        // Генерация URL для родительской категории
        "HREF" => cot_url('forums', ['c' => $parent]),
        // Назначение текущего уровня вложенности
        "LEVEL" => $level,
        // Назначение общего количества топиков по всему форуму
        "TOTAL_COUNT" => $total_topics,
    ]);

    // Инициализация счетчика цикла
    $jj = 0;

    // Получение плагинов для события forums.tree.loop
    /* === Hook - Part1 : Set === */
    $extp = cot_getextplugins('forums.tree.loop');
    /* ===== */

    // Перебор дочерних категорий
    foreach ($children as $row) {
        // Пропуск категорий, находящихся в черном списке
        if (in_array($row, $blacklist)) {
            continue;
        }

        // Инкремент счетчика цикла
        $jj++;
        // Очистка родительской категории от завершающего слэша
        $parent_clean = rtrim($parent, '/');
        // Очистка дочерней категории от начального слэша
        $row_clean = ltrim($row, '/');
        // Формирование параметров URL для дочерней категории
        $urlparams = ['c' => $parent_clean . ($parent_clean && $row_clean ? '/' : '') . $row_clean];
        // Генерация URL для категории
        $href = cot_url('forums', $urlparams);
        // Декодирование URL для корректного отображения
        $href = urldecode($href);

        // Получение подкатегорий для текущей категории с фильтрацией по черному списку
        $subcats = isset($structure['forums'][$row]['subcats']) 
            ? array_filter($structure['forums'][$row]['subcats'], function($cat) use ($blacklist) {
                // Возвращает true для подкатегорий, не входящих в черный список
                return !in_array($cat, $blacklist);
            }) 
            : [];

        // Инициализация суммы топиков
        $total_subcat_count = 0;
        // Подсчёт топиков для текущей категории
        $where = ['state' => "t.ft_state=0"];
        if (!empty($blacklist)) {
            $where['blacklist'] = "t.ft_cat NOT IN ('" . implode("','", $blacklist) . "')";
        }
        $where['cat'] = "t.ft_cat = " . Cot::$db->quote($row);
        $where_clause = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $count_sql = "SELECT COUNT(DISTINCT t.ft_id) AS total 
                      FROM $db_forum_topics AS t 
                      $where_clause";
        $count_res = Cot::$db->query($count_sql);
        $category_count = (int)$count_res->fetchColumn();
        // Подсчёт топиков подкатегорий
        foreach ($subcats as $subcat) {
            $total_subcat_count += isset($structure['forums'][$subcat]['count']) ? (int)$structure['forums'][$subcat]['count'] : 0;
        }
        // Суммируем топики текущей категории и подкатегорий
        $total_count = $category_count + $total_subcat_count;

        // Назначение переменных шаблона для категории
        $t1->assign([
            // Назначение идентификатора категории
            "ROW_ID" => $row,
            // Экранирование заголовка категории
            "ROW_TITLE" => htmlspecialchars($structure['forums'][$row]['title']),
            // Назначение описания категории
            "ROW_DESC" => $structure['forums'][$row]['desc'],
            // Назначение суммы топиков
            "ROW_COUNT" => $total_count,
            // Назначение иконки категории
            "ROW_ICON" => $structure['forums'][$row]['icon'],
            // Назначение URL категории
            "ROW_HREF" => $href,
            // Проверка, выбрана ли категория
            "ROW_SELECTED" => ((is_array($selected) && in_array($row, $selected)) || (!is_array($selected) && $row == $selected)) ? 1 : 0,
            // Назначение текущего уровня вложенности
            "ROW_LEVEL" => $level,
            // Генерация класса для чётности/нечётности строки
            "ROW_ODDEVEN" => cot_build_oddeven($jj),
            // Назначение счетчика цикла
            "ROW_JJ" => $jj,
            // Проверка наличия подкатегорий
            "ROW_HAS_SUBCATS" => !empty($subcats) ? 1 : 0,
            // Рекурсивное построение дерева подкатегорий
            "ROW_SUBCAT" => !empty($subcats) ? cot_build_structure_forums_tree($row, $selected, $level + 1) : '',
        ]);

        // Инициализация счетчика подкатегорий
        $kk = 0;
        // Перебор подкатегорий
        foreach ($subcats as $subcat) {
            // Пропуск подкатегорий, находящихся в черном списке
            if (in_array($subcat, $blacklist)) {
                continue;
            }

            // Инкремент счетчика подкатегорий
            $kk++;
            // Очистка подкатегории от начального слэша
            $subcat_clean = ltrim($subcat, '/');
            // Формирование параметров URL для подкатегории
            $subcat_urlparams = ['c' => $row_clean . ($row_clean && $subcat_clean ? '/' : '') . $subcat_clean];
            // Генерация URL для подкатегории
            $subcat_href = cot_url('forums', $subcat_urlparams);
            // Декодирование URL для корректного отображения
            $subcat_href = urldecode($subcat_href);

            // Назначение переменных шаблона для подкатегории
            $t1->assign([
                // Экранирование заголовка подкатегории
                "SUBCAT_TITLE" => htmlspecialchars($structure['forums'][$subcat]['title']),
                // Назначение описания подкатегории
                "SUBCAT_DESC" => $structure['forums'][$subcat]['desc'],
                // Назначение количества топиков подкатегории
                "SUBCAT_COUNT" => isset($structure['forums'][$subcat]['count']) ? $structure['forums'][$subcat]['count'] : 0,
                // Назначение иконки подкатегории
                "SUBCAT_ICON" => $structure['forums'][$subcat]['icon'],
                // Назначение URL подкатегории
                "SUBCAT_HREF" => $subcat_href,
                // Назначение уровня вложенности подкатегории
                "SUBCAT_LEVEL" => $level + 1,
                // Назначение счетчика подкатегорий
                "SUBCAT_JJ" => $kk
            ]);

            // Обработка дополнительных полей для подкатегорий
            if (isset($cot_extrafields[$db_structure])) {
                // Перебор дополнительных полей
                foreach ($cot_extrafields[$db_structure] as $exfld) {
                    // Преобразование имени поля в верхний регистр
                    $uname = strtoupper($exfld['field_name']);
                    // Назначение переменных шаблона для дополнительных полей подкатегории
                    $t1->assign([
                        // Назначение заголовка дополнительного поля
                        'SUBCAT_' . $uname . '_TITLE' => isset($L['structure_' . $exfld['field_name'] . '_title']) ? $L['structure_' . $exfld['field_name'] . '_title'] : $exfld['field_description'],
                        // Формирование данных дополнительного поля
                        'SUBCAT_' . $uname => cot_build_extrafields_data('structure', $exfld, $structure['forums'][$subcat][$exfld['field_name']]),
                        // Назначение сырого значения дополнительного поля
                        'SUBCAT_' . $uname . '_VALUE' => $structure['forums'][$subcat][$exfld['field_name']],
                    ]);
                }
            }

            // Парсинг блока подкатегорий в шаблоне
            $t1->parse("MAIN.CATS.SUBCATS");
        }

        // Обработка дополнительных полей для структуры
        if (isset($cot_extrafields[$db_structure])) {
            // Перебор дополнительных полей
            foreach ($cot_extrafields[$db_structure] as $exfld) {
                // Преобразование имени поля в верхний регистр
                $uname = strtoupper($exfld['field_name']);
                // Назначение переменных шаблона для дополнительных полей категории
                $t1->assign([
                    // Назначение заголовка дополнительного поля
                    'ROW_' . $uname . '_TITLE' => isset($L['structure_' . $exfld['field_name'] . '_title']) ? $L['structure_' . $exfld['field_name'] . '_title'] : $exfld['field_description'],
                    // Формирование данных дополнительного поля
                    'ROW_' . $uname => cot_build_extrafields_data('structure', $exfld, $structure['forums'][$row][$exfld['field_name']]),
                    // Назначение сырого значения дополнительного поля
                    'ROW_' . $uname . '_VALUE' => $structure['forums'][$row][$exfld['field_name']],
                ]);
            }
        }

        // Выполнение хуков для события forums.tree.loop
        /* === Hook - Part2 : Include === */
        foreach ($extp as $pl) {
            // Подключение файла плагина
            include $pl;
        }
        /* ===== */

        // Парсинг блока категорий в шаблоне
        $t1->parse("MAIN.CATS");
    }

    // Проверка, были ли обработаны категории
    if ($jj == 0) {
        // Возврат пустой строки, если категории не обработаны
        return '';
    }

    // Парсинг главного блока шаблона
    $t1->parse("MAIN");
    // Возврат отрендеренного текста шаблона
    return $t1->text("MAIN");
}



/**
 * Select forums cat for search form. Используется с Select2 (https://select2.org/)
 *
 * @global array $structure
 * @param string $check Selected category code
 * @param string $name Name of the select input
 * @param string $subcat Parent category code for filtering subcategories
 * @param bool $hideprivate Hide private categories
 * @return string
 */
function cot_forums_selectcat_select2($check, $name, $subcat = '', $hideprivate = true)
{
    // Доступ к глобальным переменным структуры и конфигурации
    global $structure, $cfg;

    // Проверяем, что массив категорий существует, иначе инициализируем пустым
    $structure['forums'] = is_array($structure['forums']) ? $structure['forums'] : [];

    // Получение конфигурации черного списка категорий для исключения
    $blacklist_cfg = $cfg['plugin']['forumtuning']['blacktreecatsforums'] ?? '';
    // Преобразование конфигурации черного списка в массив
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    // Переменная для накопления всех option'ов
    $options = '';

    // Перебираем все категории в разделе 'forums'
    foreach ($structure['forums'] as $i => $x) {
        // Пропускаем категории, находящиеся в черном списке
        if (in_array($i, $blacklist)) {
            continue;
        }

        // Проверяем, разрешён ли просмотр категории (если нужно скрывать приватные)
        $display = $hideprivate ? cot_auth('forums', $i, 'R') : true;

        // Если нужно фильтровать подкатегории, проверяем, входит ли текущая категория в фильтр
        if ($display && !empty($subcat) && isset($structure['forums'][$subcat])) {
            // Формируем строку пути родительской категории с точкой на конце
            $mtch = $structure['forums'][$subcat]['path'] . ".";
            // Длина этого пути
            $mtchlen = mb_strlen($mtch);
            // Проверяем, что путь текущей категории начинается с пути родителя или совпадает с ним
            $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
        }

        // Если есть права на чтение категории, она не "all" и подходит по фильтру
        if (cot_auth('forums', $i, 'R') && $i !== 'all' && $display) {
            // Считаем глубину категории — количество точек в пути
            $depth = substr_count($x['path'], '.');

            // Определяем, выбрана ли эта категория в данный момент
            $selected = ($i == $check) ? ' selected' : '';

            // Формируем тег option с value, data-depth и текстом
            $options .= '<option value="' . htmlspecialchars($i) . '" data-depth="' . $depth . '"' . $selected . '>' .
                        htmlspecialchars($x['title']) . '</option>';
        }
    }

    // Возвращаем полный select с классом Bootstrap
    return '<select name="' . htmlspecialchars($name) . '" class="form-select">' . $options . '</select>';
}
