<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

defined('_JEXEC') or die();

class Factory
{
    protected $namespace = null;

    protected $configuration = null;

    public function __construct($namespace, array $config)
    {
        if (strpos($namespace, '\\') === false) {
            $this->namespace = '\\Simplerenew\\Gateway\\' . ucfirst(strtolower($namespace));
        } else {
            $this->namespace = $namespace;
        }
        $this->configuration = $config;

    }
}
