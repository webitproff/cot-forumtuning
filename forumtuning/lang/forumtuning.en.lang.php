<?php
/**
 * English Language File for ForumTuning
 *
 * @package RecentItemsMetaDescription
 * @copyright (c) Cotonti
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Info
 */
$L['info_name'] = 'ForumTuning';
$L['info_desc'] = 'Various enhancements for forum functionality';

/**
 * Plugin Config
 */
$L['cfg_blacktreecatsforums'] = 'Category Blacklist';
$L['cfg_blacktreecatsforums_hint'] = 'Category codes from the Forums module structure, separated by commas';

// Forums structure localization title
if (isset($structure['forums']['rules']) && is_array($structure['forums']['rules'])) {
    $structure['forums']['rules']['title'] = 'User Conduct Rules on the Platform';
    $structure['forums']['rules']['tpath'] = 'Platform Rules';
    $structure['forums']['rules']['desc'] = 'General and specific conduct guidelines, as well as measures for violators. User support forum!';
}

$L['forums_topiclocked'] = 'The topic is locked for new posts. However, you can create your own discussion topic in the forum section that matches your theme.';

$L['forumtuning_messages'] = 'Messages';
$L['forumtuning_note'] = 'Select the appropriate category and enter the keyword to search for. After each search query, you need to click the red "reset filter" button';
$L['forumtuning_ReserFilter'] = 'Reset Filter';
$L['forumtuning_StartSearch'] = 'Start Search';
?>