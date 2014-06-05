<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Api\Plan;
use Simplerenew\Exception;

defined('_JEXEC') or die();

interface PlanInterface
{
    /**
     * Retrieve all subscription plan information
     *
     * @param Plan $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Plan $parent);

    /**
     * Get a list of defined plans from the gateway
     *
     * @param Plan $template
     *
     * @return array
     */
    public function getList(Plan $template);
}
