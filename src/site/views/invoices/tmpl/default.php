<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewInvoices $this
 */

?>
<div class="<?php echo $this->getPageClass('ost-container simplerenew-invoices'); ?>">
    <?php
    if ($this->getParams()->get('show_page_heading', true)):
        ?>
        <div class="page-header">
            <h1><?php echo $this->getHeading('COM_SIMPLERENEW_HEADING_INVOICES'); ?></h1>
        </div>
    <?php
    endif;

    if (!$this->invoices):
        echo $this->loadTemplate('noinvoices');
    else:
        ?>

        <div class="simplerenew-invoices-table">
            <div class="invoice_heading">
                <div class="invoice_col invoice_col1"><strong>Number</strong></div>
                <div class="invoice_col invoice_col2"><strong>Total</strong></div>
                <div class="invoice_col invoice_col3"><strong>Date issued</strong></div>
                <div class="clr"></div>
            </div>
            <?php
            $i = 0;
            foreach ($this->invoices as $invoice):
                $rowClass = 'invoice_row invoice_row_' . ($i % 2);
                ?>
                <div class="<?php echo $rowClass; ?>">
                    <div class="invoice_col invoice_col1">
                        <?php echo JHtml::link(
                            '#',
                            $invoice->number,
                            'onclick="return alert(\'under construction\');return false;"'
                        ); ?>
                    </div>
                    <div class="invoice_col invoice_col2"><?php echo JHtml::_(
                            'currency.format',
                            $invoice->total,
                            $invoice->currency
                        ); ?></div>
                    <div class="invoice_col invoice_col3"><?php echo $invoice->date->format('M j, Y'); ?></div>
                    <div class="clr"></div>
                </div>
            <?php
            endforeach;
            ?>

        </div>
    <?php endif; ?>
</div>
