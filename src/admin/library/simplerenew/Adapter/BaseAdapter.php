<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Adapter;

use Simplerenew\Configuration;
use Simplerenew\Object;

defined('_JEXEC') or die();

abstract class BaseAdapter extends Object
{
    /**
     * @var Configuration
     */
    protected $configuration = null;

    public function __construct(Configuration $config)
    {
        $this->configuration = $config;
    }
}
