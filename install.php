<?php
/**
 * Install script
 *
 * @package Install
 */

const COT_CODE = true;
const COT_INSTALL = true;

if (file_exists('./datas/config.php')) {
	require_once './datas/config.php';
} else {
	require_once './datas/config-sample.php';
}

if (empty($cfg['modules_dir'])) $cfg['modules_dir'] = './modules';
if (empty($cfg['lang_dir'])) $cfg['lang_dir'] = './lang';

// Force config options
$cfg['display_errors'] = true;
$cfg['debug_mode'] = true;
$cfg['customfuncs'] = false;
$cfg['cache'] = false;
$cfg['xtpl_cache'] = false;

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

require_once $cfg['system_dir'] . '/functions.php';
require_once './lib/vendor/autoload.php';
require_once './system/debug.php';

date_default_timezone_set('UTC');
$sys['now'] = time();

$env['location'] = 'install';
$env['ext'] = 'install';

if (isset($cfg['new_install']) && $cfg['new_install']) {
    error_reporting(E_ALL ^ E_NOTICE);
    session_start();

    Cot::init();

    $url = parse_url($cfg['mainurl']);
    $sys['secure'] = $url['scheme'] === 'https';
    $sys['scheme'] = $url['scheme'];
    $sys['site_uri'] = rtrim($url['path'] ?? '', '/') . '/';
    $sys['host'] = $url['host'];
    $sys['domain'] = preg_replace('#^www\.#', '', $url['host']);
    $sys['port'] = empty($url['port']) ? '' : ':' . $url['port'];
    $sys['abs_url'] = $url['scheme'] . '://' . $sys['host'] . $sys['port'] . $sys['site_uri'];
    $sys['site_id'] = 'install';

    if (empty($_SESSION['cot_inst_lang'])) {
        $lang = cot_import('lang', 'P', 'ALP');
        if (empty($lang)) {
            $lang = cot_lang_determine();
        }
    } else {
        $lang = $_SESSION['cot_inst_lang'];
    }

    require_once cot_langfile('main', 'core');
    require_once $cfg['system_dir'] . '/resources.rc.php';
} else {
    $branch = 'siena';
    $prev_branch = 'genoa';

    try {
        $db = new CotDB([
            'host' => $cfg['mysqlhost'],
            'port' => $cfg['mysqlport'] ?? null,
            'tablePrefix' => $db_x,
            'user' => $cfg['mysqluser'],
            'password' => $cfg['mysqlpassword'],
            'dbName' => $cfg['mysqldb'],
            'charset' => $cfg['mysqlcharset'] ?? null,
            'collate' => $cfg['mysqlcollate'] ?? null,
        ]);

        Cot::init();

        if (!$db->tableExists(Cot::$db->updates)) {
            define('COT_UPGRADE', true);
            $cfg['defaulttheme'] = 'nemesis';
            $cfg['defaultscheme'] = 'default';
        }
    } catch (Exception $e) {
        cot_diefatal('Database error: ' . $e->getMessage());
    }

    require_once $cfg['system_dir'] . '/common.php';
}

require_once cot_incfile('forms');
require_once cot_incfile('extensions');
require_once cot_incfile('install', 'module');
require_once cot_langfile('install', 'module');
require_once cot_langfile('users', 'core');
require_once cot_langfile('admin', 'core');

require_once cot_incfile('install', 'module', 'resources');

$theme = $cfg['defaulttheme'];
$scheme = $cfg['defaultscheme'];
$out['meta_lastmod'] = gmdate('D, d M Y H:i:s');
$file['config'] = './datas/config.php';
$file['config_sample'] = './datas/config-sample.php';
$file['sql'] = './setup/install.sql';

$processFile = cot_installProcessFile();
$processFileDir = dirname($processFile);
$anotherProcessRunning = false;

if (is_writable($processFileDir)) {
    if (file_exists($processFile)) {
        $anotherProcessStarted = (int)file_get_contents($processFile);
        if (time() - $anotherProcessStarted < 30) {
            cot_die_message(
                101,
                true,
                $L['install_another_process'],
                sprintf($L['install_another_process2'], date('Y-m-d H:i:s', $anotherProcessStarted))
            );
            exit;
        }
    }
    file_put_contents($processFile, $sys['now']);
}

$processError = '';
try {
    if (!$cfg['new_install']) {
        include cot_incfile('install', 'module', 'update');
    } else {
        include cot_incfile('install', 'module', 'install');
    }
} catch (Exception $e) {
    $processError .= $e->getMessage();
}

if (file_exists($processFile)) {
    unlink($processFile);
}
if ($processError) {
    cot_diefatal('Installation error: ' . $processError);
} else {
    header('Location: /');
    exit;
}
