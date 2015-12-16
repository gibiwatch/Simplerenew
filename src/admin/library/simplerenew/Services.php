<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

use Pimple\Container AS Pimple;
use Pimple\ServiceProviderInterface;
use Simplerenew\Api\Account;
use Simplerenew\Api\Billing;
use Simplerenew\Api\Coupon;
use Simplerenew\Api\Invoice;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Api\Transaction;
use Simplerenew\Notify\Notify;
use Simplerenew\Plugin\Events;
use Simplerenew\User\User;

defined('_JEXEC') or die();

/**
 * Class Services
 *
 * Pimple services for Simplerenew. The container must be instantiated with
 * at least the following values:
 *
 * new \Simplerenew\Container(
 *    array(
 *       'cmsNamespace'  => [The namespace for CMS specific adapters],
 *       'gateway'       => [index to the default gateway in configuration],
 *       'configuration' => new Configuration($config)
 *    )
 * )
 *
 * @package Simplerenew
 */
class Services implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Pimple $pimple An Container instance
     */
    public function register(Pimple $pimple)
    {
        // Services
        $pimple['gatewayNamespace'] = function (Container $c) {
            return '\Simplerenew\Gateway\\' . $c['gateway'];
        };

        $pimple['logger'] = function (Container $c) {
            $className = $c['cmsNamespace'] . '\Logger';
            return new $className($c['debug']);
        };

        // User classes
        $pimple['userAdapter'] = function (Container $c) {
            $adapter = $c['cmsNamespace'] . '\User\UserAdapter';
            return new $adapter();
        };

        $pimple['user'] = $pimple->factory(function (Container $c) {
            return new User($c['configuration'], $c['userAdapter']);
        });

        // Events class
        $pimple['events'] = function (Container $c) {
            $cmsDispatcher = null;
            $classname     = $c['cmsNamespace'] . '\Events';
            if (class_exists($classname)) {
                $cmsDispatcher = new $classname();
            }
            return new Events($c['configuration'], $cmsDispatcher);
        };

        // API/Gateway classes
        $pimple['account'] = $pimple->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\AccountImp');
            return new Account($c['configuration'], $imp, $c['events'], $c['user']);
        });

        $pimple['billing'] = $pimple->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\BillingImp');
            return new Billing($c['configuration'], $imp);
        });

        $pimple['coupon'] = $pimple->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\CouponImp');
            return new Coupon($c['configuration'], $imp);
        });

        $pimple['invoice'] = $pimple->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\InvoiceImp');
            return new Invoice($c['configuration'], $imp);
        });

        $pimple['notify'] = $pimple->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\NotifyImp');
            return new Notify($c, $imp);
        });

        $pimple['plan'] = $pimple->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\PlanImp');
            return new Plan($c['configuration'], $imp);
        });

        $pimple['subscription'] = $pimple->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\SubscriptionImp');
            return new Subscription($c['configuration'], $imp, $c['events']);
        });

        $pimple['transaction'] = $pimple->factory(function (Container $c) {
            $imp = $c->getInstance($c['gatewayNamespace'] . '\TransactionImp');
            return new Transaction($c['configuration'], $imp);
        });
    }
}
