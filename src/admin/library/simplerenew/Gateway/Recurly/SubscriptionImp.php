<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Account;
use Simplerenew\Api\Plan;
use Simplerenew\Api\Subscription;
use Simplerenew\Exception;
use Simplerenew\Exception\NotFound;
use Simplerenew\Gateway\SubscriptionInterface;
use Simplerenew\Object;

defined('_JEXEC') or die();

class SubscriptionImp extends AbstractRecurlyBase implements SubscriptionInterface
{
    protected $fieldMap = array(
        'id'           => 'uuid',
        'status'       => array(
            'state' => array(
                'active'              => Subscription::STATUS_ACTIVE,
                'canceled'            => Subscription::STATUS_CANCELED,
                'expired'             => Subscription::STATUS_EXPIRED,
                Object::MAP_UNDEFINED => Subscription::MAP_UNDEFINED
            )
        ),
        'enrolled'     => 'activated_at',
        'canceled'     => 'canceled_at',
        'expires'      => 'expires_at',
        'period_start' => 'current_period_started_at',
        'period_end'   => 'current_period_ends_at',
        'trial_start'  => 'trial_started_at',
        'trial_end'    => 'trial_ends_at'
    );

    /**
     * Create a new subscription for the selected account in the selected plan
     *
     * @param Subscription $parent
     * @param Account      $account
     * @param Plan         $plan
     *
     * @return void
     * @throws Exception
     */
    public function create(Subscription $parent, Account $account, Plan $plan)
    {
        try {
            $subscription = new \Recurly_Subscription(null, $this->client);

            $subscription->account   = \Recurly_Account::get($account->code, $this->client);
            $subscription->plan_code = $plan->code;
            $subscription->currency  = $this->currency;

            $subscription->create();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        $this->bindToSubscription($subscription, $parent);
    }

    /**
     * @param Subscription $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Subscription $parent)
    {
        $subscription = $this->getSubscription($parent->id);

        $this->bindToSubscription($subscription, $parent);
    }

    /**
     * Set the API object properties from the native Recurly object
     *
     * @param object       $subscription
     * @param Subscription $target
     *
     * @return void
     */
    protected function bindToSubscription($subscription, Subscription $target)
    {
        $pending = array(
            'plan'   => null,
            'amount' => null
        );
        if (isset($subscription->pending_subscription)) {
            $pending = array(
                'plan'   => $subscription->pending_subscription->plan->plan_code,
                'amount' => $subscription->pending_subscription->unit_amount_in_cents / 100
            );
        }
        $target->setProperties($subscription, $this->fieldMap);
        $target->setProperties(
            array(
                'plan'           => $subscription->plan->plan_code,
                'amount'         => $subscription->unit_amount_in_cents / 100,
                'pending_plan'   => $pending['plan'],
                'pending_amount' => $pending['amount']
            )
        );
    }

    /**
     * @param string $id
     *
     * @return \Recurly_Subscription
     * @throws Exception
     */
    protected function getSubscription($id)
    {
        if (!$id) {
            throw new Exception('No subscription selected');
        }

        try {
            $subscription = \Recurly_Subscription::get($id, $this->client);

        } catch (\Recurly_NotFoundError $e) {
            throw new NotFound($e->getMessage(), $e->getCode(), $e);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $subscription;
    }

    /**
     * Get all subscriptions for the selected account. The Recurly state
     * field will contain
     *
     * @param Subscription $template
     * @param Account      $account
     *
     * @return array
     */
    public function getList(Subscription $template, Account $account)
    {
        $subs = array();

        \Recurly_Client::$apiKey = $this->client->apiKey();
        $account                 = \Recurly_Account::get('OS_699');
        $x = \Recurly_SubscriptionList::get(array('state' => 'past_due', 'per_page' => 10), $this->client);
        //$x = \Recurly_SubscriptionList::getForAccount($account->account_code);
        foreach ($x as $y) {
            $this->bindToSubscription($y, $template);

            echo '<pre>';
            print_r($template->getProperties());
            print_r($y);
            echo '</pre>';
            return;
        }
    }

    /**
     * Load the currently active subscription
     *
     * @param Subscription $parent
     * @param Account      $account
     *
     * @return void
     * @throws Exception
     */
    public function loadActive(Subscription $parent, Account $account)
    {
        $rawList = iterator_to_array(
            \Recurly_SubscriptionList::getForAccount(
                $account->code,
                null,
                $this->client
            )
        );

        $current = array_shift($rawList);
        if ($this->translateState($current->state) != Subscription::STATUS_ACTIVE) {
            throw new Exception('No active subscription found for ' . $account->code);
        }

        $this->bindToSubscription($current, $parent);
    }

    protected function translateState($state)
    {
        if (isset($this->fieldMap['status']['state'][$state])) {
            return $this->fieldMap['status']['state'][$state];
        }
        return null;
    }
}
