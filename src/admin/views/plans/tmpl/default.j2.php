<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDir = $this->escape($this->state->get('list.direction'));

?>
<script>
window.addEvent('domready', function() {
    $('clear_form').addEvent('click', function(e) {
        this.form.filter_search.value = '';
        this.form.submit();
    });

    $$('*[name^=filter_]').addEvent('change', function(e) {
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
                <th width="10%">
                    <?php
                    echo JHtml::_(
                        'grid.sort',
                        'COM_SIMPLERENEW_PLAN_CODE',
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
                        'COM_SIMPLERENEW_PLAN_NAME',
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
                        'COM_SIMPLERENEW_PLAN_GROUP',
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
                        'COM_SIMPLERENEW_PLAN_AMOUNT',
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
                        'COM_SIMPLERENEW_PLAN_SETUP',
                        'plan.setup',
                        $listDir,
                        $listOrder
                    );
                    ?>
                </th>
                <th>
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
                <th width="%5">
                    <?php
                    echo JHtml::_(
                        'grid.sort',
                        'COM_SIMPLERENEW_ID',
                        'plan.id',
                        $listDir,
                        $listOrder
                    );
                    ?>
                </th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
        </tfoot>

        <tbody>
            <?php
            foreach ($this->items as $i => $item):
                $link = 'index.php?option=com_simplerenew&task=plan.edit&id=' . $item->id;
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                    <td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                    <td><?php echo JHtml::_('link', $link, htmlspecialchars($item->code)); ?></td>
                    <td><?php echo JHtml::_('link', $link, htmlspecialchars($item->name)); ?></td>
                    <td><?php echo $item->usergroup; ?></td>
                    <td><?php echo '$' . number_format($item->amount, 2); ?></td>
                    <td><?php echo '$' . number_format($item->setup, 2); ?></td>
                    <td><?php JHtml::_('jgrid.published', $item->published, $i, 'simplerenew.', true) ?></td>
                    <td class="right"><?php echo $item->id; ?></td>
                </tr>
            <?php
            endforeach;
            ?>
        </tbody>
    </table>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDir; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
