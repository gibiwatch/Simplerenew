<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Configuration;
use Simplerenew\Object;

defined('_JEXEC') or die();

abstract class AbstractApiBase extends Object
{
    /**
     * @var Configuration
     */
    protected $config = null;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Don't fail on unknown properties. We expect subclasses to
     * handle properties relevant to their case.
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return null;
    }
}
