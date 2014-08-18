<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

class SimplerenewControllerTest extends SimplerenewControllerBase
{
    public function display($cachable = false, $urlparams = array())
    {
        echo 'Simplerenew test area';
    }

    /**
     * Create all test accounts
     */
    public function create()
    {
        // Super Admins only
        $user = SimplerenewFactory::getUser();
        if (!$user->authorise('core.admin')) {
            $this->setRedirect('index.php?option=com_simplerenew', JText::_('JERROR_ALERTNOAUTHOR'), 'notice');
            return;
        }

        $accounts = array(
            'Active',
            'Canceled',
            'Expired',
            'Pending',
            'NoBilling',
            'NoSubs',
            'NoAccount'
        );

        $container = SimplerenewFactory::getContainer();
        $plans = $container->getPlan()->getList();
        $company = SimplerenewFactory::getConfig()->get('sitename');

        foreach ($accounts as $account) {
            $username = 'demo-' . strtolower($account);
            $email    = $username . '@ostraining.xxx';

            $properties = array(
                'firstname' => $account,
                'lastname'  => 'Demo',
                'username'  => $username,
                'email'     => $email,
                'company'   => $company,
                'password'  => 'test',
                'password2' => 'test',
                'cc' => array(
                    'number' => '4111111111111111',
                    'cvv' => '123',
                    'year' => date('Y')+5,
                    'month' => 12
                )
            );

            if ($account == 'NoSubs') {
                $this->createAccount($properties);
            } elseif ($account != 'NoAccount') {
                $plan = array_shift($plans);
                $subscription = $this->createAccount($properties, $plan);
            } else {
                $user = SimplerenewFactory::getContainer()->getUser();
                $user->setProperties($properties)->create();

                $juser = SimplerenewFactory::getUser($user->id);
                $juser->activation = '';
                $juser->block = 0;
                $juser->save(true);
            }
        }

    }

    /**
     * @param array $properties
     * @param Plan $plan
     *
     * @return Subscription
     */
    protected function createAccount(array $properties, Plan $plan = null)
    {
        $container = SimplerenewFactory::getContainer();

        $user = $container->getUser();
        $user->setProperties($properties)->create();

        $juser = SimplerenewFactory::getUser($user->id);
        $juser->activation = '';
        $juser->block = 0;
        $juser->save(true);

        $account = $container->getAccount();
        $account
            ->setProperties($properties)
            ->setUser($user)
            ->save();

        $billing = $container->getBilling();
        $billing
            ->setProperties($properties)
            ->setAccount($account)
            ->save();

        if ($plan) {
            $subscription = $container->getSubscription();
            $subscription->create($account, $plan);
            return $subscription;
        }

        return null;
    }
}
