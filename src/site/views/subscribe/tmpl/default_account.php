<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewSubscribe $this
 */

$loggedIn    = ($this->user->id > 0);
$stepHeading = JText::_('COM_SIMPLERENEW_HEADING_BASICINFORMATION');

if (!$loggedIn) {
    $defaultForm = $this->getParams()->get('accountFormDefault', 'register');
    $buttonText  = array(
        'register' => JText::_('COM_SIMPLERENEW_SUBSCRIBE_LOGIN_BUTTON'),
        'login'    => JText::_('COM_SIMPLERENEW_SUBSCRIBE_REGISTER_BUTTON')
    );

    JHtml::_('sr.toggles', '#simplerenew-toggle-login', '#simplerenew-form-' . $defaultForm);

    $stepHeading = <<<BUTTONHTML
{$stepHeading}
<a
    id="simplerenew-toggle-login"
    data-panels=".simplerenew-toggle-item">
    <i class="fa fa-user"></i> {$buttonText[$defaultForm]}
</a>
BUTTONHTML;
}

echo $this->stepHeading($stepHeading);
?>
<div class="simplerenew-subscribe-user p-bottom b-bottom">
    <div id="simplerenew-form-register" class="simplerenew-toggle-item">
        <?php echo $this->loadTemplate('register'); ?>
    </div>
    <?php
    if (!$loggedIn) :
        ?>
        <div id="simplerenew-form-login" class="simplerenew-toggle-item">
            <?php echo $this->loadTemplate('login'); ?>
        </div>
        <?php
    endif;
    ?>
</div>
