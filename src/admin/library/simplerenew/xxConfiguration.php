<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

defined('_JEXEC') or die();

/**
 * Class Configuration
 * @package Simplerenew
 *
 */
class Configuration
{
    protected $data = null;

    public function __construct($data = null)
    {
        $this->load($data);
    }

    public function load($data)
    {
        if (is_string($data)) {
            $data = json_decode($data);
        }

        if (is_object($data)) {
            $this->data = $data;
        }

        return $this;
    }

    public function get($path, $default = null)
    {
        if (!strpos($path, '.')) {
            if (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '') {
                return $this->data->$path;
            } else {
                return $default;
            }
        }

        $result = $default;

        // Explode the path into an array
        $nodes = explode('.', $path);

        // Initialize the current node to be the registry root.
        $node  = $this->data;
        $found = false;

        // Traverse the registry to find the correct node for the result.
        foreach ($nodes as $n) {
            if (isset($node->$n)) {
                $node  = $node->$n;
                $found = true;
            } else {
                $found = false;
                break;
            }
        }

        if ($found && $node !== null && $node !== '') {
            $result = $node;
        }

        return $result;
    }
}
