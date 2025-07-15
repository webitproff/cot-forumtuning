# ForumTuning Plugin for Cotonti Siena

**ForumTuning** is a plugin for Cotonti Siena v.0.9.26 that enhances the forums module with improved category management, advanced search functionality, and customizable category tree rendering. The plugin is designed to give administrators greater control over forum category visibility and improve the user experience through intuitive navigation and search capabilities.

## Plugin Demo
[Live Demo](https://abuyfile.com/forums)

<img src="https://raw.githubusercontent.com/webitproff/cot-forumtuning/refs/heads/main/forumtuning.jpg" alt="ForumTuning is a plugin for Cotonti Siena v.0.9.26" title="ForumTuning is a plugin for Cotonti Siena v.0.9.26" />


<img src="https://raw.githubusercontent.com/webitproff/cot-forumtuning/refs/heads/main/forumtuning-02.jpg" alt="ForumTuning is a plugin for Cotonti Siena v.0.9.26" title="ForumTuning is a plugin for Cotonti Siena v.0.9.26" />

## Features

- **Category Blacklist**: Exclude specific forum categories from display using a configurable blacklist (`blacktreecatsforums`), applied to both the category tree and search dropdown.
- **Enhanced Category Tree**: Generates a hierarchical HTML category tree with support for:
  - Recursive rendering of subcategories.
  - Blacklist filtering to hide specified categories at all levels.
  - Customizable output via XTemplate and Bootstrap-based templates.
  - Support for extra fields to display category metadata.
  - Extensibility through Cotonti hooks (`forums.tree.first`, `forums.tree.main`, `forums.tree.loop`).
- **Improved Search and Sections Display**:
  - Search form with a category dropdown (using Select2) and a text input for keyword searches.
  - Paginated list of the latest topics, filtered by category and search query.
  - Safe handling of category-specific templates to prevent errors.
- **Custom Root Section Templates**:
  - You can create and use custom templates for main categories and customize them as needed. For example: `forums.sections.cotonti.tpl`, `forums.sections.mycatcode.tpl`, etc., where "cotonti" and "mycatcode" are the codes of your forum category structure.
- **Multilingual Support**: Includes Russian (`forumtuning.ru.lang.php`) and English (`forumtuning.en.lang.php`) language files:
  - Allows overriding localization strings from the "Forums" module or adding custom ones for use in your templates.
  - Category titles and descriptions can be customized for each site localization. See the `rules` category as an example.
- **Bootstrap Integration**: Uses Bootstrap classes and Font Awesome icons for a modern, responsive category tree interface.
- **Security and Compatibility**: Integrates with Cotonti's permission system to control category visibility and supports PHP 8.4.
- **Site Theme Integration**: Developed and tested with the [2-Way Deal](https://github.com/webitproff/cot_2waydeal_build/tree/master/public_html/themes) theme, compatible with the current [Cotonti Siena v.0.9.26](https://github.com/Cotonti/Cotonti) codebase as of 15.07.2025. If using a different theme, you need to include the [select2](https://github.com/webitproff/cot_2waydeal_build/tree/master/public_html/themes/2waydeal/assets/select2) assets for proper plugin functionality.

## Requirements

- **Cotonti Siena**: Version 0.9.26
- **PHP**: 8.4 or higher
- **Modules**: Forums module
- **Dependencies**: [Cotonti Siena v.0.9.26](https://github.com/Cotonti/Cotonti), [2-Way Deal](https://github.com/webitproff/cot_2waydeal_build/tree/master/public_html/themes), [select2](https://github.com/webitproff/cot_2waydeal_build/tree/master/public_html/themes/2waydeal/assets/select2)

## Installation

1. Download the plugin files from the [GitHub repository](https://github.com/webitproff/cot-forumtuning).
2. Extract the `forumtuning` folder to the `plugins/` directory of your Cotonti installation.
3. Log in to the Cotonti admin panel.
4. Navigate to **Administration > Extensions > Plugins** and install the **ForumTuning** plugin.
5. Configure the plugin settings in **Administration > Configuration > Plugins > ForumTuning**:
   - Enter a comma-separated list of category codes in the `blacktreecatsforums` field to exclude (e.g., `cat1,cat2,cat3`).
6. Open your [forums.sections.tpl](https://github.com/webitproff/cot_2waydeal_build/blob/master/public_html/themes/2waydeal/modules/forums/forums.sections.tpl) template and add the following code in the desired location:
```
	<!-- IF {PHP|cot_plugin_active('forumtuning')} -->
      <div class="p-3 mb-4 rounded-2" style="border: 5px var(--bs-sidebar-border) solid">
        <form action="{SEARCH_ACTION_URL}" method="get" class="d-flex flex-column gap-3">
          <input type="hidden" name="e" value="forums" />
          <input type="hidden" name="l" value="{PHP.lang}" />
          <div class="row align-items-center">
            <label class="col-12 col-sm-3 mb-2 mb-sm-0">{PHP.L.Category}:</label>
            <div class="col-12 col-sm-9">{SEARCH_CAT}</div>
          </div>
          <div class="row align-items-center">
            <label class="col-12 col-sm-3 mb-2 mb-sm-0">{PHP.L.Search}:</label>
            <div class="col-12 col-sm-9">{SEARCH_SQ} <span class="small">{PHP.L.forumtuning_note}</span>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-sm-3 d-none d-sm-block"></div>
            <div class="col-12 col-sm-9">
              <div class="row g-3 justify-content-md-end justify-content-center">
                <div class="col-md-6 col-12 text-center">
                  <input type="submit" name="search" class="w-100 w-md-auto btn btn-primary" value="{PHP.L.forumtuning_StartSearch}" />
                </div>
                <div class="col-md-6 col-12 text-center">
                  <a class="btn btn-danger w-100" href="{PHP|cot_url('forums')}">{PHP.L.forumtuning_ReserFilter}</a>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div id="latest-topics">
            <!-- BEGIN: LATEST_TOPICS -->
            <div class="list-group list-group-striped list-group-flush mb-4">
              <!-- BEGIN: LATEST_TOPICS_ROW -->
              <li class="list-group-item list-group-item-action {FORUMS_ROW_ODDEVEN}">
                <div class="row g-3">
                  <div class="col-12 col-md-8">
                    <h5 class="mb-0 fs-6 fw-semibold">
                      <a class="text-reset" href="{URL}">{TITLE}</a>
                    </h5>
                    <small class="text-body-secondary">({COUNT} {PHP.L.forumtuning_messages})</small>
                  </div>
                  <div class="col-12 col-md-4 text-center">
                    <div>
                      <small class="text-body-secondary">{PHP.L.Author} {AUTHOR}</small>
                    </div>
                    <small class="text-body-secondary">{DATE}</small>
                  </div>
                </div>
              </li>
              <!-- END: LATEST_TOPICS_ROW -->
            </div>
            <div class="pagination">{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}</div>
            <!-- END: LATEST_TOPICS -->
          </div>
        </div>
        <div class="col-md-4">{PHP|cot_build_structure_forums_tree('', '')}</div>
      </div>
	  <!-- ENDIF -->
```

> **Attention!** Upon completion of the installation, the conditions outlined in points №4 and №5 of the installation instructions for the [treecatspage](https://github.com/webitproff/cot-treecatspage) plugin must be fulfilled.

## System Requirements

- **Cotonti Siena**: v.0.9.26
- **PHP**: 8.4 or higher

## Plugin File Structure

```
plugins/
└── forumtuning/
    ├── forumtuning.setup.php                # Plugin metadata and configuration settings
    ├── forumtuning.global.php               # Global initialization and setup for the plugin
    ├── forumtuning.forums.sections.main.php # Enhances the forums sections page with search and topic listing
    ├── inc/
    │   └── forumtuning.functions.php        # Core functions for category tree and search dropdown
    ├── lang/
    │   ├── forumtuning.en.lang.php          # English language file with translations and localization
    │   └── forumtuning.ru.lang.php          # Russian language file with translations and localization
    └── tpl/
        └── forumtuning.forums.tree.tpl      # Template for rendering the forum category tree
```

### File Descriptions

1. **forumtuning.setup.php**
   - **Description**: Defines the plugin's metadata (name, version, author) and configuration settings.
   - **Functionality**:
     - Specifies dependency on the `forums` module and sets read-only access for guests and members.
     - Configures the `blacktreecatsforums` setting to exclude categories from display.
   - **Significance**: Serves as the entry point for plugin installation and blacklist configuration.

2. **forumtuning.global.php**
   - **Description**: Initializes global dependencies and permissions for the plugin.
   - **Functionality**:
     - Loads the `forums` module, plugin language file, and core functions.
     - Checks forum access permissions using `cot_auth`.
     - Hooks into the `global` event for plugin-wide loading.
   - **Significance**: Ensures dependencies are available and permissions are checked globally.

3. **forumtuning.forums.sections.main.php**
   - **Description**: Enhances the forums sections page with a search form and paginated topic listing.
   - **Functionality**:
     - Safely handles category and search query inputs.
     - Loads category-specific templates for the sections page.
     - Provides a search form with a category dropdown and text input.
     - Displays the latest topics with pagination, filtered by category and keywords.
   - **Significance**: Improves user interaction through advanced search and navigation.

4. **inc/forumtuning.functions.php**
   - **Description**: Contains core functions for building the category tree and search dropdown.
   - **Functionality**:
     - Function `cot_build_structure_forums_tree`: Creates a hierarchical category tree with blacklist filtering, extra field support, and hooks.
     - Function `cot_forums_selectcat_select2`: Generates a category dropdown for search forms, respecting permissions and blacklist.
   - **Significance**: Provides the core logic for category management and search functionality.

5. **lang/forumtuning.en.lang.php**
   - **Description**: English language file with translations and localization.
   - **Functionality**:
     - Defines plugin metadata and configuration labels in English.
     - Includes a message for locked topics.
     - Localizes the `rules` category with title, path, and description.
   - **Significance**: Supports English-speaking users and administrators.

6. **lang/forumtuning.ru.lang.php**
   - **Description**: Russian language file with translations and localization.
   - **Functionality**:
     - Similar to the English file, but with Russian translations.
     - Localizes the `rules` category for Russian-speaking users.
   - **Significance**: Ensures a consistent experience for Russian-speaking users.

7. **tpl/forumtuning.forums.tree.tpl**
   - **Description**: Template for rendering the forum category tree.
   - **Functionality**:
     - Uses Bootstrap and Font Awesome for a responsive, collapsible navigation menu.
     - Displays categories, subcategories, topic counts, and active states.
     - Includes a root-level "All" category with total topic count.
   - **Significance**: Provides a customizable, user-friendly interface for category navigation.

## Configuration

- **Category Blacklist (`blacktreecatsforums`)**:
  - Enter a comma-separated list of forum category codes to exclude from the category tree and search dropdown.
  - Example: `rules,private,archive`
  - Leave empty to include all categories.

## Usage

- **Category Tree**: The plugin automatically enhances the forum category tree, excluding blacklisted categories, and renders it using the `forumtuning.forums.tree.tpl` template.
- **Search Form**: Use the search form on the forums sections page to filter topics by category and keywords.
- **Localization**: Ensure the appropriate language file (`forumtuning.en.lang.php` or `forumtuning.ru.lang.php`) is used based on your site's language settings.

## Template Customization

The `forumtuning.forums.tree.tpl` file can be customized to adjust the appearance of the category tree. It uses Bootstrap classes and Font Awesome icons for styling. Modify the template in the `plugins/forumtuning/tpl/` directory to suit your design needs.

## License

This plugin is licensed under the [Cotonti License](https://github.com/Cotonti/Cotonti/blob/master/License.txt).

## Author

- **Author**: Webitproff
- **GitHub**: [https://github.com/webitproff](https://github.com/webitproff)
- **Date**: 15 July 2025

## Contributing

Contributions are welcome! Please submit pull requests or issues to the [GitHub repository](https://github.com/webitproff/cot-forumtuning).

## Changelog

### Version 1.0.0 (15 July 2025)
- Initial release with category blacklist, enhanced category tree, and improved search functionality.

---

For support or feature requests, please open an issue in the [GitHub repository](https://github.com/webitproff/cot-forumtuning).
If you need help or have questions, you can also post in Russian or English on the [forum](https://abuyfile.com/en/forums/cotonti/custom/plugs).

---
RU
---
# Плагин ForumTuning для Cotonti Siena

**ForumTuning** — это плагин для Cotonti Siena v.0.9.26, который расширяет функциональность модуля форумов за счёт улучшенного управления категориями, усовершенствованного поиска и настраиваемого отображения дерева категорий. Плагин разработан для предоставления администраторам большего контроля над видимостью категорий форума и улучшения пользовательского опыта благодаря интуитивной навигации и возможностям поиска.

## Демонстрация работы плагина
[Живое демо](https://abuyfile.com/forums)

## Возможности

- **Чёрный список категорий**: Исключение определённых категорий форума из отображения с помощью настраиваемого чёрного списка (`blacktreecatsforums`), применяемого к дереву категорий и выпадающему списку поиска.
- **Улучшенное дерево категорий**: Генерация иерархического HTML-дерева категорий с поддержкой:
  - Рекурсивного отображения подкатегорий.
  - Фильтрации по чёрному списку для скрытия указанных категорий на всех уровнях.
  - Настраиваемого вывода через систему XTemplate и шаблоны на основе Bootstrap.
  - Поддержки дополнительных полей для отображения метаданных категорий.
  - Расширяемости через хуки Cotonti (`forums.tree.first`, `forums.tree.main`, `forums.tree.loop`).
- **Улучшенный поиск и отображение разделов**:
  - Форма поиска с выпадающим списком категорий (используется Select2) и текстовым полем для поиска по ключевым словам.
  - Пагинированный список последних тем, отфильтрованных по категории и поисковому запросу.
  - Безопасная обработка шаблонов для конкретных категорий, предотвращающая ошибки.
- **Пользовательские шаблоны корневых разделов**:
  - При желании вы можете создавать и использовать собственные шаблоны главных категорий и кастомизировать их как угодно. Например: forums.sections.cotonti.tpl, forums.sections.mycatcode.tpl и т. д., где "cotonti", "mycatcode" - это коды вашей структуры категорий модуля форумов.
- **Поддержка мультиязычности**: Включает языковые файлы на русском (`forumtuning.ru.lang.php`) и английском (`forumtuning.en.lang.php`) языках:
  - Здесь можете переопределять строки локализации из модуля "Forums" или добавлять свои для использования в своих шаблонах модуля.
  - Названия категорий и описание. Для каждой локализации сайта можете задавать собственные заголовки и описания для категорий форумов. Смотрите по примеру с `rules`.
- **Интеграция с Bootstrap**: Использует классы Bootstrap и иконки Font Awesome для современного, адаптивного интерфейса дерева категорий.
- **Безопасность и совместимость**: Интегрируется с системой разрешений Cotonti для контроля видимости категорий и поддерживает PHP 8.4.
- **Интеграция с темами сайта**: Разрабатывалось и тестировалось при использовании [2-Way Deal](https://github.com/webitproff/cot_2waydeal_build/tree/master/public_html/themes), которая совместима с актуальным исходным кодом  [Cotonti Siena v.0.9.26](https://github.com/Cotonti/Cotonti) по состоянию на 15.07.2025 г. Если у вас другая тема, - то вам для корректной работы плагина необходимо подключить файлы из папки [select2](https://github.com/webitproff/cot_2waydeal_build/tree/master/public_html/themes/2waydeal/assets/select2)

## Требования

- **Cotonti Siena**: Версия 0.9.26
- **PHP**: 8.4 или выше
- **Модули**: Модуль "Forums"
- **Зависимости**: [Cotonti Siena v.0.9.26](https://github.com/Cotonti/Cotonti), [2-Way Deal](https://github.com/webitproff/cot_2waydeal_build/tree/master/public_html/themes), [select2](https://github.com/webitproff/cot_2waydeal_build/tree/master/public_html/themes/2waydeal/assets/select2)

## Установка

1. Скачайте файлы плагина из [репозитория GitHub](https://github.com/webitproff/cot-forumtuning).
2. Распакуйте папку `forumtuning` в директорию `plugins/` вашей установки Cotonti.
3. Войдите в административную панель Cotonti.
4. Перейдите в **Администрирование > Расширения > Плагины** и установите плагин **ForumTuning**.
5. Настройте параметры плагина в **Администрирование > Конфигурация > Плагины > ForumTuning**:
   - Укажите в поле `blacktreecatsforums` список кодов категорий, разделённых запятыми, для исключения (например, `cat1,cat2,cat3`).
6. Откройте свой шаблон [forums.sections.tpl](https://github.com/webitproff/cot_2waydeal_build/blob/master/public_html/themes/2waydeal/modules/forums/forums.sections.tpl) и в нужном месте добавьте код:
```
	<!-- IF {PHP|cot_plugin_active('forumtuning')} -->
      <div class="p-3 mb-4 rounded-2" style="border: 5px var(--bs-sidebar-border) solid">
        <form action="{SEARCH_ACTION_URL}" method="get" class="d-flex flex-column gap-3">
          <input type="hidden" name="e" value="forums" />
          <input type="hidden" name="l" value="{PHP.lang}" />
          <div class="row align-items-center">
            <label class="col-12 col-sm-3 mb-2 mb-sm-0">{PHP.L.Category}:</label>
            <div class="col-12 col-sm-9">{SEARCH_CAT}</div>
          </div>
          <div class="row align-items-center">
            <label class="col-12 col-sm-3 mb-2 mb-sm-0">{PHP.L.Search}:</label>
            <div class="col-12 col-sm-9">{SEARCH_SQ} <span class="small">{PHP.L.forumtuning_note}</span>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-sm-3 d-none d-sm-block"></div>
            <div class="col-12 col-sm-9">
              <div class="row g-3 justify-content-md-end justify-content-center">
                <div class="col-md-6 col-12 text-center">
                  <input type="submit" name="search" class="w-100 w-md-auto btn btn-primary" value="{PHP.L.forumtuning_StartSearch}" />
                </div>
                <div class="col-md-6 col-12 text-center">
                  <a class="btn btn-danger w-100 " href="{PHP|cot_url('forums')}">{PHP.L.forumtuning_ReserFilter}</a>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div id="latest-topics">
            <!-- BEGIN: LATEST_TOPICS -->
            <div class="list-group list-group-striped list-group-flush mb-4">
              <!-- BEGIN: LATEST_TOPICS_ROW -->
              <li class="list-group-item list-group-item-action {FORUMS_ROW_ODDEVEN}">
                <div class="row g-3">
                  <div class="col-12 col-md-8">
                    <h5 class="mb-0 fs-6 fw-semibold">
                      <a class="text-reset" href="{URL}">{TITLE}</a>
                    </h5>
                    <small class="text-body-secondary">({COUNT} {PHP.L.forumtuning_messages})</small>
                  </div>
                  <div class="col-12 col-md-4 text-center">
                    <div>
                      <small class="text-body-secondary">{PHP.L.Author} {AUTHOR}</small>
                    </div>
                    <small class="text-body-secondary">{DATE}</small>
                  </div>
                </div>
              </li>
              <!-- END: LATEST_TOPICS_ROW -->
            </div>
            <div class="pagination">{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}</div>
            <!-- END: LATEST_TOPICS -->
          </div>
        </div>
        <div class="col-md-4">{PHP|cot_build_structure_forums_tree('', '')}</div>
      </div>
	  <!-- ENDIF -->
```
> **Внимание!** По окончанию установки должны быть выполнены условия, изложенные в пунктах №4 и №5 инструкции по установке плагина [treecatspage](https://github.com/webitproff/cot-treecatspage).


## Системные требования

- **Cotonti Siena**: v.0.9.26
- **PHP**: 8.4 или выше

## Структура файлов плагина

```
plugins/
└── forumtuning/
    ├── forumtuning.setup.php                # Метаданные плагина и настройки конфигурации
    ├── forumtuning.global.php               # Глобальная инициализация и настройка плагина
    ├── forumtuning.forums.sections.main.php # Улучшение страницы разделов форума с поиском и списком тем
    ├── inc/
    │   └── forumtuning.functions.php        # Основные функции для дерева категорий и выпадающего списка поиска
    ├── lang/
    │   ├── forumtuning.en.lang.php          # Файл английского языка с переводами и локализацией
    │   └── forumtuning.ru.lang.php          # Файл русского языка с переводами и локализацией
    └── tpl/
        └── forumtuning.forums.tree.tpl      # Шаблон для отображения дерева категорий форума
```

### Описание файлов

1. **forumtuning.setup.php**
   - **Описание**: Определяет метаданные плагина (название, версия, автор) и конфигурацию.
   - **Функциональность**:
     - Указывает зависимость от модуля `forums` и задаёт права доступа (только чтение для гостей и пользователей).
     - Настраивает параметр `blacktreecatsforums` для исключения категорий из отображения.
   - **Значимость**: Служит точкой входа для установки плагина и настройки чёрного списка.

2. **forumtuning.global.php**
   - **Описание**: Инициализирует глобальные зависимости и разрешения для плагина.
   - **Функциональность**:
     - Загружает модуль `forums`, языковой файл плагина и основные функции.
     - Проверяет права доступа к форуму через `cot_auth`.
     - Подключается к событию `global` для глобальной загрузки плагина.
   - **Значимость**: Обеспечивает доступность зависимостей и проверку прав для всех компонентов плагина.

3. **forumtuning.forums.sections.main.php**
   - **Описание**: Улучшает страницу разделов форума с формой поиска и пагинированным списком тем.
   - **Функциональность**:
     - Безопасно обрабатывает входные параметры категории и поискового запроса.
     - Загружает шаблоны для разделов с учётом категории.
     - Предоставляет форму поиска с выпадающим списком категорий и текстовым полем.
     - Отображает последние темы с пагинацией, фильтруя по категории и ключевым словам.
   - **Значимость**: Улучшает взаимодействие пользователей с форумом через удобный поиск и навигацию.

4. **inc/forumtuning.functions.php**
   - **Описание**: Содержит основные функции для построения дерева категорий и выпадающего списка поиска.
   - **Функциональность**:
     - Функция `cot_build_structure_forums_tree`: создаёт иерархическое дерево категорий с фильтрацией по чёрному списку, поддержкой дополнительных полей и хуков.
     - Функция `cot_forums_selectcat_select2`: формирует выпадающий список категорий для формы поиска с учётом прав доступа и чёрного списка.
   - **Значимость**: Обеспечивает основную логику управления категориями и поиска.

5. **lang/forumtuning.en.lang.php**
   - **Описание**: Файл английского языка с переводами и локализацией.
   - **Функциональность**:
     - Определяет метаданные плагина и метки конфигурации на английском.
     - Содержит сообщение для заблокированных тем.
     - Локализует категорию `rules` с заголовком, путём и описанием.
   - **Значимость**: Обеспечивает поддержку английского языка для пользователей и администраторов.

6. **lang/forumtuning.ru.lang.php**
   - **Описание**: Файл русского языка с переводами и локализацией.
   - **Функциональность**:
     - Аналогичен английскому файлу, но с переводами на русский язык.
     - Локализует категорию `rules` для русскоязычной аудитории.
   - **Значимость**: Поддерживает русскоязычных пользователей, обеспечивая единообразие интерфейса.

7. **tpl/forumtuning.forums.tree.tpl**
   - **Описание**: Шаблон для отображения дерева категорий форума.
   - **Функциональность**:
     - Использует Bootstrap и Font Awesome для создания адаптивного, сворачиваемого меню.
     - Отображает категории, подкатегории, количество тем и активное состояние.
     - Включает корневую категорию "Все" с общим количеством тем.
   - **Значимость**: Обеспечивает удобный и настраиваемый интерфейс для навигации по категориям.

## Конфигурация

- **Чёрный список категорий (`blacktreecatsforums`)**:
  - Введите список кодов категорий форума, разделённых запятыми, которые нужно исключить из дерева категорий и выпадающего списка поиска.
  - Пример: `rules,private,archive`
  - Оставьте поле пустым, чтобы включить все категории.

## Использование

- **Дерево категорий**: Плагин автоматически улучшает дерево категорий форума, исключая категории из чёрного списка, и отображает его с использованием шаблона `forumtuning.forums.tree.tpl`.
- **Форма поиска**: Используйте форму поиска на странице разделов форума для фильтрации тем по категориям и ключевым словам.
- **Локализация**: Убедитесь, что используется соответствующий языковой файл (`forumtuning.en.lang.php` или `forumtuning.ru.lang.php`) в зависимости от языковых настроек сайта.

## Настройка шаблона

Файл `forumtuning.forums.tree.tpl` можно настроить для изменения внешнего вида дерева категорий. Он использует классы Bootstrap и иконки Font Awesome для стилизации. Измените шаблон в директории `plugins/forumtuning/tpl/` в соответствии с вашими дизайнерскими потребностями.

## Лицензия

Плагин распространяется под [лицензией Cotonti](https://github.com/Cotonti/Cotonti/blob/master/License.txt).

## Автор

- **Автор**: Webitproff
- **GitHub**: [https://github.com/webitproff](https://github.com/webitproff)
- **Дата**: 15 июля 2025

## Вклад

Приветствуется любой вклад! Пожалуйста, отправляйте pull requests или создавайте issues в [репозитории GitHub](https://github.com/webitproff/cot-forumtuning).

## Журнал изменений

### Версия 1.0.0 (15 июля 2025)
- Первоначальный релиз с поддержкой чёрного списка категорий, улучшенного дерева категорий и усовершенствованной функциональности поиска.

---

Для поддержки или запросов новых функций, пожалуйста, создайте issue в [репозитории GitHub](https://github.com/webitproff/cot-forumtuning).
Если нужна помощь или есть вопросы, также можете писать на русском или английском на [форуме](https://abuyfile.com/ru/forums/cotonti/custom/plugs)
