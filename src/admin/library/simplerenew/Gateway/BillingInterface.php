<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Api\Billing;
use Simplerenew\Exception;

defined('_JEXEC') or die();

interface BillingInterface
{
    /**
     * @param Billing $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Billing $parent);

    /**
     * @param Billing $parent
     *
     * @return void
     * @throws Exception
     */
    public function save(Billing $parent);

    /**
     * @param Billing $parent
     *
     * @return void
     * @throws Exception
     */
    public function delete(Billing $parent);
}
