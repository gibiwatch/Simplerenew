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
        /** @var SimplerenewModelGateway $model */
        $model = SimplerenewModel::getInstance('Gateway');
        $container = SimplerenewFactory::getContainer();


        $user = $container->getUser()->loadByUsername('fred');
        $account = $container->getAccount()->load($user);

        
        $subscription = $container->getSubscription()->load('2862ff2e16174984b46f6a4ecd8e09cf');

        echo '<pre>';
        print_r($subscription->getProperties());
        echo '</pre>';
        return;


        $user = $container->getUser()->loadByUsername('fred');
        $account = $container->getAccount()->load($user);
        $plan = $container->getPlan()->load('monthly-3for1');


        try {
            $subscription = $model->createSubscription($account, $plan);
            echo '<pre>';
            print_r($subscription->getProperties());
            echo '</pre>';

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        echo '<br/><br/>done';
    }
}
