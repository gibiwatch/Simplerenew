<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewPlan $this
 * @var JFormField          $field
 */

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');

$app = SimplerenewFactory::getApplication();
$input = $app->input;
$fieldSets = $this->form->getFieldsets();

$style = <<<CSS
div.inline label {
    clear: none;
    min-width: 0px;
}
CSS;
SimplerenewFactory::getDocument()->addStyleDeclaration($style);


require_once __DIR__ . '/edit_form.php';
