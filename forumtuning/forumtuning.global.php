<?php
/**
 * [BEGIN_COT_EXT]
 * Hooks=global
 * [END_COT_EXT]
 */



defined('COT_CODE') or die('Wrong URL.');
require_once cot_incfile('forums', 'module');
require_once cot_langfile('forumtuning', 'plug');
require_once cot_incfile('forumtuning', 'plug');

list($auth_read, $auth_write, $auth_admin) = cot_auth('module', 'forums');
