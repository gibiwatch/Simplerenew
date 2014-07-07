<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewController extends SimplerenewControllerBase
{
    protected $default_view = 'subscribe';

    public function test()
    {
        $params = SimplerenewComponentHelper::getParams();
        $params->set('gateway.recurly.mode', 'live');
        $params->set('gateway.recurly.liveApikey', '808896419fd94121ba4bbcb0f32f460b');
        $params->set('gateway.recurly.livePrivate', 'f284ad043e784180b97661881fb459da');
        $params->set('gateway.recurly.liveSubdomain', 'ostraining');

        $container = SimplerenewFactory::getContainer($params);

        $account = $container->getAccount();
        $sub = $container->getSubscription();

        $sub->getList($account);
    }
}
