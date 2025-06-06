<?php 
/**
 * Plugin Name: Custom Variables (Matomo Plugin)
 * Plugin URI: http://plugins.matomo.org/CustomVariables
 * Description: Categorise your visitors and actions with custom name-value pairs. Segment by these values and get more insights to draw the right conclusions.
 * Author: Matomo
 * Author URI: https://matomo.org
 * Version: 5.0.4
 */
?><?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\CustomVariables;

use Piwik\Tracker\Cache;

 
if (defined( 'ABSPATH')
&& function_exists('add_action')) {
    $path = '/matomo/app/core/Plugin.php';
    if (defined('WP_PLUGIN_DIR') && WP_PLUGIN_DIR && file_exists(WP_PLUGIN_DIR . $path)) {
        require_once WP_PLUGIN_DIR . $path;
    } elseif (defined('WPMU_PLUGIN_DIR') && WPMU_PLUGIN_DIR && file_exists(WPMU_PLUGIN_DIR . $path)) {
        require_once WPMU_PLUGIN_DIR . $path;
    } else {
        return;
    }
    add_action('plugins_loaded', function () {
        if (function_exists('matomo_add_plugin')) {
            matomo_add_plugin(__DIR__, __FILE__, true);
        }
    });
}

class CustomVariables extends \Piwik\Plugin
{
    public const MAX_NUM_CUSTOMVARS_CACHEKEY = 'CustomVariables.MaxNumCustomVariables';

    /**
     * @see \Piwik\Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array(
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
            'AssetManager.getStylesheetFiles'  => 'getStylesheetFiles',
            'Dimension.addDimensions' => 'addDimensions',
            'Actions.getCustomActionDimensionFieldsAndJoins' => 'provideActionDimensionFields',
            'Tracker.setTrackerCacheGeneral' => 'getCacheGeneral',
            'Tracker.getVisitFieldsToPersist' => 'getVisitFieldsToPersist'
        );
    }

    public function install()
    {
        Model::install();
    }

    public function uninstall()
    {
        Model::uninstall();
    }

    public function addDimensions(&$instances)
    {
        foreach (Model::getScopes() as $scope) {
            $model = new Model($scope);
            try {
                $highestIndex = $model->getHighestCustomVarIndex();
            } catch (\Exception $e) {
                continue; // ignore error for tests to work as this might be executed before Piwik tables are installed
            }

            foreach (range(1, $highestIndex) as $index) {
                $custom = new CustomDimension();
                $custom->initCustomDimension($index, $model);
                $instances[] = $custom;
            }
        }
    }

    /**
     * There are also some hardcoded places in JavaScript
     * @return int
     */
    public static function getMaxLengthCustomVariables()
    {
        return 200;
    }

    /**
     * Returns the number of available custom variables that can be used.
     *
     * "Can be used" is identifed by the minimum number of available custom variables across all relevant tables. Eg
     * if there are 6 custom variables installed in log_visit but only 5 in log_conversion, we consider only 5 custom
     * variables as usable.
     * @return int
     */
    public static function getNumUsableCustomVariables()
    {
        $cache    = Cache::getCacheGeneral();
        $cacheKey = self::MAX_NUM_CUSTOMVARS_CACHEKEY;

        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        return 0;
    }

    public function getCacheGeneral(&$cacheContent)
    {
        $cacheContent[self::MAX_NUM_CUSTOMVARS_CACHEKEY] = self::fetchNumMaxCustomVariables();
    }

    private static function fetchNumMaxCustomVariables()
    {
        $minCustomVar = null;

        foreach (Model::getScopes() as $scope) {
            $model = new Model($scope);
            $highestIndex = $model->getHighestCustomVarIndex();

            if (!isset($minCustomVar)) {
                $minCustomVar = $highestIndex;
            }

            if ($highestIndex < $minCustomVar) {
                $minCustomVar = $highestIndex;
            }
        }

        if (!isset($minCustomVar)) {
            $minCustomVar = 0;
        }

        return $minCustomVar;
    }

    public function getClientSideTranslationKeys(&$translationKeys)
    {
        $translationKeys[] = 'CustomVariables_CustomVariables';
        $translationKeys[] = 'CustomVariables_ManageDescription';
        $translationKeys[] = 'CustomVariables_ScopeX';
        $translationKeys[] = 'CustomVariables_Index';
        $translationKeys[] = 'CustomVariables_Usages';
        $translationKeys[] = 'CustomVariables_Unused';
        $translationKeys[] = 'CustomVariables_CreateNewSlot';
        $translationKeys[] = 'CustomVariables_UsageDetails';
        $translationKeys[] = 'CustomVariables_CurrentAvailableCustomVariables';
        $translationKeys[] = 'CustomVariables_ToCreateCustomVarExecute';
        $translationKeys[] = 'CustomVariables_CreatingCustomVariableTakesTime';
        $translationKeys[] = 'CustomVariables_SlotsReportIsGeneratedOverTime';
        $translationKeys[] = 'General_Loading';
        $translationKeys[] = 'General_TrackingScopeVisit';
        $translationKeys[] = 'General_TrackingScopePage';
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = "plugins/CustomVariables/vue/src/ManageCustomVars/ManageCustomVars.less";
    }

    public function provideActionDimensionFields(&$fields, &$joins)
    {
        $maxCustomVariables = CustomVariables::getNumUsableCustomVariables();

        for ($i = 1; $i <= $maxCustomVariables; $i++) {
            $fields[] = 'custom_var_k' . $i;
            $fields[] = 'custom_var_v' . $i;
        }
    }

    public function getVisitFieldsToPersist(&$fields)
    {
        for ($index = 1; $index <= CustomVariables::getNumUsableCustomVariables(); $index++) {
            $fields[] = 'custom_var_k' . $index;
            $fields[] = 'custom_var_v' . $index;
        }
    }
}
