<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!defined('SIMPLERENEW_LOADED')) {
    require_once JPATH_ADMINISTRATOR . '/components/com_simplerenew/include.php';
}

JFormHelper::loadFieldClass('Checkboxes');

class JFormFieldPlans extends JFormFieldCheckboxes
{
    public function __construct($form = null)
    {
        parent::__construct($form);

        if (!SimplerenewFactory::getApplication()->isSite()) {
            $lang = SimplerenewFactory::getLanguage();
            $lang->load('com_simplerenew', SIMPLERENEW_SITE);
        }
    }

    public function getOptions()
    {
        SimplerenewFactory::getDocument()
            ->addStyleDeclaration('fieldset.checkboxes input { margin-right: 5px; }');

        $options = parent::getOptions();

        $db = SimplerenewFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('code, name, amount, trial_length, trial_unit')
            ->from('#__simplerenew_plans')
            ->order('code');

        $list = $db->setQuery($query)->loadObjectList();
        foreach ($list as $plan) {
            $option = JHtml::_('select.option', $plan->code, JHtml::_('plan.name', $plan));
            $option->checked = false;
            $options[] = $option;
        }
        return $options;
    }
}
