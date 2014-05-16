<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway;

use Simplerenew\Configuration;

defined('_JEXEC') or die();

abstract class AbstractGatewayBase
{
    /**
     * @var Configuration
     */
    protected $config = null;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }
}
