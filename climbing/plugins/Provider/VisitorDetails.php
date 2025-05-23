<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\Provider;

use Piwik\Piwik;
use Piwik\Plugins\Live\VisitorDetailsAbstract;
use Piwik\View;
use Piwik\Plugin;

/**
 * @see plugins/Provider/functions.php
 */

class VisitorDetails extends VisitorDetailsAbstract
{
    public function __construct()
    {
        $dir = Plugin\Manager::getPluginDirectory('Provider');
        require_once $dir . '/functions.php';
    }

    public function extendVisitorDetails(&$visitor)
    {
        $visitor['provider']     = $this->details['location_provider'];
        $visitor['providerName'] = $this->getProviderName();
        $visitor['providerUrl']  = $this->getProviderUrl();
    }

    public function renderVisitorDetails($visitorDetails)
    {
        if (empty($visitorDetails['provider'])) {
            return [];
        }

        $view            = new View('@Provider/_visitorDetails.twig');
        $view->visitInfo = $visitorDetails;
        return [[20, $view->render()]];
    }

    protected function getProvider()
    {
        if (isset($this->details['location_provider'])) {
            return $this->details['location_provider'];
        }
        return Piwik::translate('General_Unknown');
    }

    protected function getProviderName()
    {
        return getPrettyProviderName($this->getProvider());
    }

    protected function getProviderUrl()
    {
        return getHostnameUrl(@$this->details['location_provider']);
    }
}
