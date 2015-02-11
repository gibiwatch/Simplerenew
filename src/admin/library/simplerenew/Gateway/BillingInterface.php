<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
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
     * Get the javascript assets needed for processing
     * sensitive financial data on a web form.
     *
     * If values in returned array begin with:
     *
     * http : treated as external script
     * /    : treated as local script
     *
     * Anything else will be added as an inline script
     *
     * @return array
     */
    public function getJSAssets();

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
