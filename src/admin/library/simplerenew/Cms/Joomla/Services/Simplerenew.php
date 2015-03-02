<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Cms\Joomla\Services;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Coupon;
use Simplerenew\Api\Invoice;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Configuration;
use Simplerenew\Notify\Notify;
use Simplerenew\Plugin\Events;
use Simplerenew\User\User;

defined('_JEXEC') or die();

class Simplerenew implements ServiceProviderInterface
{
    /**
     * @var array
     */
    protected $config = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Registers services on the given container.
     * Note that this instance expects to be registered
     * with Simplerenew\Container, an overloaded version
     * of Pimple\Container
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A Container instance
     * @param array     $config Configuration array
     */
    public function register(Container $pimple, array $config = array())
    {
        // Parameters
        $pimple['configData']       = $this->config;
        $pimple['cmsNamespace']     = 'Simplerenew\Cms\Joomla';
        $pimple['gatewayNamespace'] = 'Simplerenew\Gateway\Recurly';

        // Services
        $pimple['configuration'] = function (\Simplerenew\Container $c) {
            return new Configuration($c['configData']);
        };

        // User classes
        $pimple['userAdapter'] = function (\Simplerenew\Container $c) {
            $adapter = $c['cmsNamespace'] . '\User\UserAdapter';
            return new $adapter();
        };

        $pimple['user'] = $pimple->factory(function (\Simplerenew\Container $c) {
            return new User($c['configuration'], $c['userAdapter']);
        });

        // Events class
        $pimple['events'] = function (\Simplerenew\Container $c) {
            return new Events($c['configuration']);
        };

        // Gateway classes
        $pimple['account'] = $pimple->factory(function (\Simplerenew\Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\AccountImp');
            return new Account($c['configuration'], $imp, $c['user']);
        });

        $pimple['billing'] = $pimple->factory(function (\Simplerenew\Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\BillingImp');
            return new Billing($c['configuration'], $imp);
        });

        $pimple['coupon'] = $pimple->factory(function (\Simplerenew\Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\CouponImp');
            return new Coupon($c['configuration'], $imp);
        });

        $pimple['invoice'] = $pimple->factory(function (\Simplerenew\Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\InvoiceImp');
            return new Invoice($c['configuration'], $imp);
        });

        $pimple['notify'] = $pimple->factory(function (\Simplerenew\Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\NotifyImp');
            return new Notify($c, $imp);
        });

        $pimple['plan'] = $pimple->factory(function (\Simplerenew\Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\PlanImp');
            return new Plan($c['configuration'], $imp);
        });

        $pimple['subscription'] = $pimple->factory(function (\Simplerenew\Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\SubscriptionImp');
            return new Subscription($c['configuration'], $imp, $c['events']);
        });
    }
}
