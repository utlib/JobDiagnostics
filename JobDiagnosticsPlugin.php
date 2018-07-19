<?php
/**
 * The main plugin.
 * @package JobDiagnostics
 */
class JobDiagnosticsPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * Hooks used by this plugin.
     * @var array
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'define_routes',
    );

    /**
     * Filters used by this plugin.
     * @var array
     */
    protected $_filters = array(
        'admin_navigation_main',
    );

    /**
     * HOOK: Install
     * Add tables to the database.
     */
    public function hookInstall()
    {
        $db = get_db();
        $db->query(
            "CREATE TABLE IF NOT EXISTS `{$db->prefix}job_diagnostics_tests` (
            `id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `dispatch_type` VARCHAR(64) NOT NULL,
            `started` TIMESTAMP DEFAULT NOW(),
            `finished` TIMESTAMP NULL,
            `error` TEXT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
        );
    }

    /**
     * HOOK: Uninstall
     * Remove tables from the database
     */
    public function hookUninstall()
    {
        $db = get_db();
        $db->query("DROP TABLE IF EXISTS `{$db->prefix}job_diagnostics_tests`;");
    }

    /**
     * HOOK: Upgrade
     * Follow migrations in sequence from oldest to newest.
     */
    public function hookUpgrade($args)
    {
        // Capture arguments
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];
        $doMigrate = false;
        // Load in all migrations
        $versions = array();
        foreach (glob(dirname(__FILE__) . '/libraries/IiifItems/Migration/*.php') as $migrationFile) {
            $className = 'IiifItems_Migration_' . basename($migrationFile, '.php');
            include $migrationFile;
            $versions[$className::$version] = new $className();
        }
        // Sort in version order
        uksort($versions, 'version_compare');
        // Run from oldest to newest, starting with the first one later than the old version
        foreach ($versions as $version => $migration) {
            if (version_compare($version, $oldVersion, '>')) {
                $doMigrate = true;
            }
            if ($doMigrate) {
                $migration->up();
                if (version_compare($version, $newVersion, '>')) {
                    break;
                }
            }
        }
    }

    /**
     * HOOK: Setting up routes
     * Add routes to the admin side only.
     *
     * @param array $args
     */
    public function hookDefineRoutes($args)
    {
        if (is_admin_theme()) {
            $user = current_user();
            if (!empty($user) && ($user->role || 'superuser' || $user->role == 'admin')) {
                $args['router']->addConfig(new Zend_Config_Ini(dirname(__FILE__) . '/routes.ini', 'routes'));
            }
        }
    }

    /**
     * FILTER: Add entry to admin navigation menu.
     *
     * @param  array $nav
     * @return array
     */
    public function filterAdminNavigationMain($nav)
    {
        $user = current_user();
        if ($user->role || 'superuser' || $user->role == 'admin') {
            $nav[] = array(
                'label' => __('Job Diagnostics'),
                'uri' => url(array(), 'job_diagnostics_root'),
            );
        }
        return $nav;
    }
}
