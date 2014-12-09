<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app = SimplerenewFactory::getApplication();
$user = SimplerenewFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDir = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'plan.ordering';

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_simplerenew&task=plans.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'planList', 'adminForm', strtolower($listDir), $saveOrderingUrl);
}

?>
<script type="text/javascript">
    Joomla.orderTable = function () {
        var dirn;

        var table = document.getElementById("sortTable");
        var direction = document.getElementById("directionTable");
        var order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        }
        else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<form
    action="<?php echo JRoute::_('index.php?option=com_simplerenew&view=plans'); ?>"
    method="post"
    name="adminForm"
    id="adminForm">

<?php
echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
if (empty($this->items)): ?>
    <div class="alert alert-no-items">
        <?php echo JText::_('COM_SIMPLERENEW_NO_MATCHING_RESULTS'); ?>
    </div>
<?php
else:
    ?>
    <table class="table table-striped" id="planList">
        <thead>
        <tr>
            <th width="1%" class="nowrap center hidden-phone">
                <?php
                echo JHtml::_(
                    'searchtools.sort',
                    '',
                    'plan.ordering',
                    $listDir,
                    $listOrder,
                    null,
                    'asc',
                    'COM_SIMPLERENEW_ORDERING',
                    'icon-menu-2'
                ); ?>
            </th>

            <th width="1%">
                <?php echo JHtml::_('grid.checkall'); ?>
            </th>

            <th width="1%" style="min-width:55px" class="nowrap center">
                <?php
                echo JHtml::_(
                    'searchtools.sort',
                    'COM_SIMPLERENEW_PUBLISHED',
                    'plan.published',
                    $listDir,
                    $listOrder
                );
                ?>
            </th>

            <th class="nowrap">
                <?php
                echo JHtml::_(
                    'searchtools.sort',
                    'COM_SIMPLERENEW_PLAN_CODE_LABEL',
                    'plan.code',
                    $listDir,
                    $listOrder
                );
                ?>
            </th>

            <th class="nowrap">
                <?php
                echo JHtml::_(
                    'searchtools.sort',
                    'COM_SIMPLERENEW_PLAN_NAME_LABEL',
                    'plan.name',
                    $listDir,
                    $listOrder
                );
                ?>
            </th>

            <th class="nowrap">
                <?php
                echo JHtml::_(
                    'searchtools.sort',
                    'COM_SIMPLERENEW_PLAN_GROUP_ID_LABEL',
                    'ug.title',
                    $listDir,
                    $listOrder
                );
                ?>
            </th>

            <th width="5%" class="nowrap">
                <?php
                echo JHtml::_(
                    'searchtools.sort',
                    'COM_SIMPLERENEW_PLAN_AMOUNT_LABEL',
                    'plan.amount',
                    $listDir,
                    $listOrder
                );
                ?>
            </th>

            <th width="5%" class="nowrap">
                <?php
                echo JHtml::_(
                    'searchtools.sort',
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

        <tbody>
        <?php
        foreach ($this->items as $i => $item):
            $ordering = ($listOrder == 'plan.ordering');
            $editLink = 'index.php?option=com_simplerenew&task=plan.edit&id=' . $item->id;
            $editTitle = JText::_('JACTION_EDIT');
            ?>
            <tr class="<?php echo 'row' . ($i % 2); ?>">
                <td class="order nowrap center hidden-phone">
                    <?php
                    $iconClass = '';
                    if (!$saveOrder) {
                        $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                    }
                    ?>
                    <span class="sortable-handler<?php echo $iconClass ?>">
								<i class="icon-menu"></i>
							</span>
                    <?php if ($saveOrder): ?>
                        <input type="text" style="display:none" name="order[]" size="5"
                               value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
                    <?php endif; ?>
                </td>

                <td class="center hidden-phone">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>

                <td class="center">
                    <div class="btn-group">
                        <?php
                        echo JHtml::_(
                            'jgrid.published',
                            $item->published,
                            $i,
                            'plans.'
                        );
                        ?>
                    </div>
                </td>

                <td class="has-context">
                    <?php
                    if ($item->checked_out) {
                        echo JHtml::_(
                            'jgrid.checkedout',
                            $i,
                            $item->editor,
                            $item->checked_out_time,
                            'plans.',
                            true
                        );
                    }
                    echo JHtml::_(
                        'link',
                        JRoute::_($editLink),
                        $this->escape($item->code),
                        array('title' => $editTitle)
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo JHtml::link(
                        JRoute::_($editLink),
                        $this->escape($item->name),
                        array('title' => $editTitle)
                    );
                    ?>
                </td>

                <td>
                    <?php echo $item->usergroup; ?>
                </td>

                <td style="text-align: right;">
                    <?php echo JHtml::_('currency.format', $item->amount, $item->currency); ?>
                </td>

                <td style="text-align: right;">
                    <?php echo JHtml::_('currency.format', $item->setup_cost, $item->currency); ?>
                </td>

                <td>
                    <?php echo JHtml::_('plan.length', $item) ? : JText::_('COM_SIMPLERENEW_PLAN_LENGTH_NONE'); ?>
                </td>

                <td>
                    <?php echo JHtml::_('plan.trial', $item) ? : JText::_('COM_SIMPLERENEW_PLAN_TRIAL_NONE'); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php
endif;
echo $this->pagination->getListFooter();
?>

<input type="hidden" name="task" value=""/>
<input type="hidden" name="boxchecked" value="0"/>
<?php echo JHtml::_('form.token'); ?>
</form>
