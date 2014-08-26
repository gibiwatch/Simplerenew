<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/** @var SimplerenewViewAccount $this */

$address = $this->billing ? $this->billing->address : new Simplerenew\Primitive\Address();
$fields  = SimplerenewRender::addressEdit('billing', $address, null, true);

if ($fields) {
    $html        = array();
    $lastSection = 0;
    foreach ($fields as $i => $field) {
        if (!($i % 2)) {
            if ($i != $lastSection) {
                $html[] = '</div>';
            }
            $html[] = '<div class="ost-section">';
        }
        $html[] = '<div class="block6">';
        $html[] = $field;
        $html[] = '</div>';
    }
    $html[] = '</div>';

    echo join("\n", $html);
}
