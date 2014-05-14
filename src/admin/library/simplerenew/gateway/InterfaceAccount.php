<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

defined('_JEXEC') or die();

interface InterfaceAccount
{
    public function __construct(array $config = array());

    public function load($accountCode);
}
