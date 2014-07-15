<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/** @var SimplerenewViewSubscribe $this */
?>
<div class="ost-section">
    <div class="block6">
        <label for="billing_address1"><?php echo JText::_('COM_SIMPLERENEW_BILLING_ADDRESS1'); ?></label>
        <input
            id="billing_address1"
            name="billing[address1]"
            type="text"
            value="<?php echo $this->billing->address->address1; ?>"
            required="true"/>
    </div>
    <div class="block6">
        <label for="billing_address2"><?php echo JText::_('COM_SIMPLERENEW_BILLING_ADDRESS2'); ?></label>
        <input
            id="billing_address2"
            name="billing[address2]"
            type="text"
            value="<?php echo $this->billing->address2; ?>"
            required="true"/>
    </div>
</div>
<!-- /.ost-section -->

<div class="ost-section">
    <div class="block6">
        <label for="billing_city"><?php echo JText::_('COM_SIMPLERENEW_BILLING_CITY'); ?></label>
        <input
            id="billing_city"
            name="billing[city]"
            type="text"
            value="<?php echo $this->billing->address->city; ?>"
            required="true"/>
    </div>
    <div class="block6">
        <label for="billing_region"><?php echo JText::_('COM_SIMPLERENEW_BILLING_REGION'); ?></label>
        <input
            id="billing_region"
            name="billing[region]"
            type="text"
            value="<?php echo $this->billing->address->region; ?>"
            required="true"/>
    </div>
</div>
<!-- /.ost-section -->

<div class="ost-section">
    <div class="block6">
        <label for="billing_postal"><?php echo JText::_('COM_SIMPLERENEW_BILLING_POSTAL'); ?></label>
        <input
            id="billing_postal"
            name="billing[postal]"
            type="text"
            value="<?php echo $this->billing->address->postal; ?>"
            required="true"/>
    </div>
    <div class="block6">
        <label for="billing_country"><?php echo JText::_('COM_SIMPLERENEW_BILLING_COUNTRY'); ?></label>
        <?php
        echo JHtml::_(
            'srselect.country',
            'billing[country]',
            'required="true"',
            $this->billing->address->country,
            'billing_country'
        );
        ?>
    </div>
</div>
<!-- /.ost-section -->
