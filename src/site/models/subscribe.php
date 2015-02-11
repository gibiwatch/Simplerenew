<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2015 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

require_once __DIR__ . '/account.php';

class SimplerenewModelSubscribe extends SimplerenewModelAccount
{
    public function getPlans()
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('plans.*')
            ->from('#__simplerenew_plans plans')
            ->order('ordering ASC');
        $query->where('published = 1 AND group_id > 0');

        if ($available = $this->getAvailable()) {
            $available = array_map(array($db, 'quote'), $available);
            $query->where('code IN (' . join(',', $available) . ')');
        }

        $list = $db->setQuery($query)->loadObjectList('code');
        return $list;
    }

    /**
     * Get an array of plans being made available to the current user
     *
     * @return array
     */
    protected function getAvailable()
    {
        if ($plans = $this->getState('filter.plans')) {
            if (!empty($plans['*'])) {
                // Simple plan selection regardless of user
                $available = $plans['*'];

            } else {
                // Select the plans based on the user's user group
                $userId = $this->getState('user.id');
                $user   = SimplerenewFactory::getUser($userId);

                // Start by assuming unsubscribed
                $checkGroups = array(0);
                if ($user->id > 0) {
                    // See if the user is in a plan group
                    $planGroups = SimplerenewFactory::getDbo()
                        ->setQuery('Select group_id from #__simplerenew_plans group by group_id')
                        ->loadColumn();

                    $checkGroups = array_intersect($planGroups, $user->groups) ? : $checkGroups;
                }

                $available = array();
                foreach ($checkGroups as $group) {
                    if (isset($plans[$group])) {
                        if (is_array($plans[$group])) {
                            $available = array_filter(array_merge($available, $plans[$group]));
                        } elseif ($plans[$group] == '*') {
                            // This group makes all plans selected
                            $available = array();
                            break;
                        }
                    }
                }
            }
            return $available;
        }
    }

    protected function populateState()
    {
        parent::populateState();

        // We're only interested in current subscriptions
        $currentSubs = Subscription::STATUS_ACTIVE | Subscription::STATUS_CANCELED;
        $this->setState('status.subscription', $currentSubs);

        if ($params = $this->state->get('parameters.menu')) {
            $plans = new JRegistry($params->get('plans'));
            $this->setState('filter.plans', $plans->toArray());

            // Using the coupon URL var overrides couponAllow
            $input          = SimplerenewFactory::getApplication()->input;
            $couponOverride = $input->getCmd('coupon', 'null');
            $couponAllow    = (int)$params->get('couponAllow', 0);

            if ($couponOverride == 'null') {
                // No override from URL
                $couponOverride = '';
            } elseif ($couponOverride == 'false' || $couponOverride == '0') {
                // Force usage off
                $couponOverride = '';
                $couponAllow    = 0;
            } else {
                // Force usage on
                if ($couponOverride == 'true' || $couponOverride == '1') {
                    $couponOverride = '';
                }
                $couponAllow = -1;
            }
            $this->setState('coupon.allow', $couponAllow);

            $couponDefault = $couponOverride ? : $params->get('couponDefault', '');
            $this->setState('coupon.default', $couponDefault);
        }
    }
}
