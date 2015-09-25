<?php
/**
 * @package   Simplerenew
 * @contact   www.simplerenew.com, support@simplerenew.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 */

$loggedIn     = ($this->user->id > 0);
$stepHeading  = JText::_('COM_SIMPLERENEW_HEADING_BASICINFORMATION');

if (!$loggedIn) {
    JHtml::_('sr.toggles', '#simplerenew-toggle-login');

    $stepHeading .= ' <button type="button" id="simplerenew-toggle-login"'
        . ' class="btn-main btn-small"'
        . ' data-panels=".simplerenew-toggle-item">'
        . JText::_('COM_SIMPLERENEW_SUBSCRIBE_LOGIN_BUTTON')
        . '</button>';
}

echo $this->stepHeading($stepHeading);
?>
<div class="simplerenew-subscribe-user p-bottom b-bottom">
    <div class="simplerenew-toggle-item">
        <?php echo $this->loadTemplate('register'); ?>
    </div>
    <?php
    if (!$loggedIn) :
        ?>
        <div class="simplerenew-toggle-item">
            <?php echo $this->loadTemplate('login'); ?>
        </div>
        <?php
    endif;
    ?>
</div>
