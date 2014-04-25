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
 * This is a complete punt until we figure out how to handle configurations
 * from a non-Joomla perspective
 * @TODO: Convert to something not Joomla-centric
 */
class Configuration extends \JRegistry
{
    public function __construct($data=null)
    {
        $data = array(
            'gateway' => array(
                'recurly' => array(
                    'test' => array(
                        'apikey' => '6d00ae5e11894d1581830bcc8deb8778',
                        'private' => '699d2b94ab364f9594e41a7d2e5ee1c7'
                    ),
                    'apikey' => '808896419fd94121ba4bbcb0f32f460b',
                    'private' => 'f284ad043e784180b97661881fb459da'
                )
            )
        );

        parent::__construct($data);
    }
}
