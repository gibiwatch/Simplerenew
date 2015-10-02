<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

defined('_JEXEC') or die();

class DateTime extends \DateTime
{
    public function addFromUserInput($specifier)
    {
        if ($specifier[0] != 'P') {
            // Accept a more relaxed syntax from users for dates only
            if (preg_match_all('/(\d+)\s*([YMWD])/', strtoupper($specifier), $limit)) {
                $fixed = 'P' . str_replace(' ', '', join('', $limit[0]));

                $diff = new \DateInterval($fixed);
                $this->add($diff);
            }

        }

    }
}
