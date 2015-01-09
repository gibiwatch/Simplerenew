<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/** @var SimplerenewViewAccount $this */

$fields = SimplerenewRender::addressDisplay($this->billing->address);

$html        = array();
$lastSection = 0;
foreach ($fields as $i => $field) {
    $class  = array(
        'ost-section',
        'ost-row-' . (($i % 2) ? 'one' : 'two')
    );
    $html = array_merge(
        $html,
        array(
            '<div class="' . join(' ', $class) . '">',
            '   <div class="block3">',
            '      <label>' . $field->label . '</label>',
            '   </div>',
            '   <div class="block9">',
            '   ' . $field->value,
            '   </div>',
            '</div>'
        )
    );
}
echo join("\n", $html);
