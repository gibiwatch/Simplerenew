<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewInvoices $this
 */

?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-invoices'); ?>">
    <?php
    if ($heading = $this->getHeading('COM_SIMPLERENEW_HEADING_INVOICES')):
        ?>
        <div class="page-header">
            <h1><?php echo $heading; ?></h1>
        </div>
    <?php
    endif;

    if (!$this->invoices):
        echo $this->loadTemplate('noinvoices');
    else:
        ?>

        <div class="simplerenew-invoices-table">

            <div class="ost-section ost-row-two ost-invoice-heading b-bottom">
                <div class="block4">Number</div>
                <div class="block4">Total</div>
                <div class="block4">Date issued</div>
            </div>
            <!-- /ost-section -->

            <?php
            $i = 1;
            foreach ($this->invoices as $invoice):
                $rowClass = $i++ % 2 ? 'ost-row-one' : 'ost-row-two';
                ?>
                <div class="ost-section <?php echo $rowClass; ?>">
                    <div class="block4">
                        <?php echo JHtml::_('invoice.pdflink', $invoice->number); ?>
                    </div>
                    <div class="block4">
                        <?php
                        echo JHtml::_(
                            'currency.format',
                            $invoice->total,
                            $invoice->currency
                        );
                        ?>
                    </div>
                    <div class="block4"><?php echo $invoice->date->format('M j, Y'); ?></div>
                </div>
                <!-- /ost-section -->
            <?php
            endforeach;
            ?>

        </div>
        <!-- .simplerenew-invoices-table -->

    <?php endif; ?>
</div>
