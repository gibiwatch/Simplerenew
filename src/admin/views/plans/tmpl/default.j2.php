<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$user      = SimplerenewFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDir   = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'plan.ordering';

?>
<script>
    window.addEvent('domready', function () {
        $('clear_form').addEvent('click', function (e) {
            this.form.filter_search.value = '';
            this.form.submit();
        });

        $$('*[name^=filter_]').addEvent('change', function (e) {
            this.form.submit();
        });
    });
</script>
<form action="index.php?option=com_simplerenew&view=plans" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search">
                <?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
            </label>

            <input
                type="text"
                name="filter_search"
                id="filter_search"
                value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                title="<?php echo JText::_('COM_SIMPLERENEW_FILTER_SEARCH_DESC'); ?>"/>

            <button type="submit" class="btn">
                <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
            </button>
            <button type="button" id="clear_form">
                <?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
            </button>
        </div>
        <div class="filter-select fltrt"></div>
    </fieldset>
    <div class="clr"></div>

    <table class="adminlist">
        <thead>
        <tr>
            <th width="1%">&nbsp;</th>

            <th width="1%"><input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)"/></th>

            <th width="5%">
                <?php
                echo JHtml::_(
                    'grid.sort',
                    'COM_SIMPLERENEW_PUBLISHED',
                    'plan.published',
                    $listDir,
                    $listOrder
                );
                ?>
            </th>

            <th width="10%">
                <?php
                echo
                JHtml::_(
                    'grid.sort',
                    'COM_SIMPLERENEW_ORDERING',
                    'plan.ordering',
                    $listDir,
                    $listOrder
                );
                if ($saveOrder) {
                    echo JHtml::_(
                        'grid.order',
                        $this->items,
                        'filesave.png',
                        'plans.saveorder'
                    );
                }
                ?>
            </th>

            <th width="10%">
                <?php
                echo JHtml::_(
                    'grid.sort',
                    'COM_SIMPLERENEW_PLAN_CODE_LABEL',
                    'plan.code',
                    $listDir,
                    $listOrder
                );
                ?>
            </th>

            <th>
                <?php
                echo JHtml::_(
                    'grid.sort',
                    'COM_SIMPLERENEW_PLAN_NAME_LABEL',
                    'plan.name',
                    $listDir,
                    $listOrder
                );
                ?>
            </th>

            <th>
                <?php
                echo JHtml::_(
                    'grid.sort',
                    'COM_SIMPLERENEW_PLAN_GROUP_ID_LABEL',
                    'ug.title',
                    $listDir,
                    $listOrder
                )
                ?>
            </th>

            <th>
                <?php
                echo JHtml::_(
                    'grid.sort',
                    'COM_SIMPLERENEW_PLAN_AMOUNT_LABEL',
                    'plan.amount',
                    $listDir,
                    $listOrder
                );
                ?>
            </th>

            <th>
                <?php
                echo JHtml::_(
                    'grid.sort',
                    'COM_SIMPLERENEW_PLAN_SETUP_COST_LABEL',
                    'plan.setup_cost',
                    $listDir,
                    $listOrder
                );
                ?>
            </th>

            <th class="nowrap">
                <?php echo JText::_('COM_SIMPLERENEW_PLAN_LENGTH_LABEL'); ?>
            </th>

            <th class="nowrap">
                <?php echo JText::_('COM_SIMPLERENEW_PLAN_TRIAL_PERIOD_LABEL'); ?>
            </th>
        </tr>
        </thead>

        <tfoot>
        <tr>
            <td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
        </tfoot>

        <tbody>
        <?php
        if (empty($this->items)):
            ?>
            <tr>
                <td colspan="11"><?php echo JText::_('COM_SIMPLERENEW_NO_MATCHING_RESULTS'); ?></td>
            </tr>
        <?php
        else:
            foreach ($this->items as $i => $item):
                $ordering = ($listOrder == 'plan.ordering');
                $link     = 'index.php?option=com_simplerenew&task=plan.edit&id=' . $item->id;
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                <td>
                    <?php echo $this->pagination->getRowOffset($i); ?>
                </td>

                <td class="center">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>

                <td class="center">
                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'plans.', true) ?>
                </td>

                <td class="order">
                <?php
                        if ($saveOrder):
                            if ($listDir == 'asc'):
                                ?>
                            <span><?php
                                echo $this->pagination->orderUpIcon(
                                    $i,
                                    true,
                                    'plans.orderup',
                                    'JLIB_HTML_MOVE_UP',
                                    $ordering
                                ); ?></span>
                            <span><?php
                                echo $this->pagination->orderDownIcon(
                                    $i,
                                    $this->pagination->total,
                                    true,
                                    'plans.orderdown',
                                    'JLIB_HTML_MOVE_DOWN',
                                    $ordering
                                ); ?></span>
                        <?php
                        elseif ($listDir == 'desc'): ?>
                            <span><?php
                                echo $this->pagination->orderUpIcon(
                                    $i,
                                    true,
                                    'plans.orderdown',
                                    'JLIB_HTML_MOVE_UP',
                                    $ordering
                                ); ?></span>
                            <span><?php
                                echo $this->pagination->orderDownIcon(
                                    $i,
                                    $this->pagination->total,
                                    true,
                                    'plans.orderup',
                                    'JLIB_HTML_MOVE_DOWN',
                                    $ordering
                                ); ?></span>
                        <?php
                        endif;

                        $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
                        <input type="text" name="order[]" size="5"
                               value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="text-area-order"/>
                    <?php
                    else:
                        echo $item->ordering;
                    endif;
                    ?>
                </td>

                <td>
                    <?php echo JHtml::_('link', $link, htmlspecialchars($item->code)); ?>
                </td>

                <td>
                    <?php echo JHtml::_('link', $link, htmlspecialchars($item->name)); ?>
                </td>

                <td>
                    <?php echo $item->usergroup; ?>
                </td>

                <td class="right">
                    <?php echo JHtml::_('currency.format', $item->amount, $item->currency); ?>
                </td>

                <td class="right">
                    <?php echo JHtml::_('currency.format', $item->setup_cost, $item->currency); ?>
                </td>

                <td>
                    <?php echo JHtml::_('plan.length', $item) ?: JText::_('COM_SIMPLERENEW_PLAN_LENGTH_NONE'); ?>
                </td>

                <td>
                    <?php echo JHtml::_('plan.trial', $item) ?: JText::_('COM_SIMPLERENEW_PLAN_TRIAL_NONE'); ?>
                </td>
            </tr>
        <?php
        endforeach;
        endif;
        ?>
        </tbody>
    </table>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDir; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
