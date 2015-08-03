<?php
/**
 * @package   com_simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
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
     * @param string  $token
     *
     * @return void
     * @throws Exception
     */
    public function save(Billing $parent, $token = null);

    /**
     * @param Billing $parent
     *
     * @return void
     * @throws Exception
     */
    public function delete(Billing $parent);

    /**
     * Map raw data from the Gateway to SR fields
     *
     * @param Billing $parent
     * @param mixed   $data
     *
     * @return void
     */
    public function bindSource(Billing $parent, $data);

}
