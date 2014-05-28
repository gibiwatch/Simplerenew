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
     * Create/Update using current subscription plan settings
     *
     * @param Plan $parent
     *
     * @return void
     * @throws Exception
     */
    public function save(Plan $parent);

    /**
     * Delete the current plan if possible
     *
     * @param Plan $parent
     *
     * @return void
     * @throws Exception
     */
    public function delete(Plan $parent);
}