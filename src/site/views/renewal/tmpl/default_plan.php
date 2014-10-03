<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Simplerenew\Api\Subscription;

defined('_JEXEC') or die();

/**
 * @var SimplerenewViewRenewal $this
 */

$container    = SimplerenewFactory::getContainer();
$subscription = $this->subscription;
$plan         = $container->getPlan()->load($subscription->plan);

$planId  = 'plan_code_' . $plan->code;
$classes = array(
    'ost-toggle',
    'ost-toggle-flat',
    'plan_code',
    $planId
);
$checked = ($subscription->status == Subscription::STATUS_ACTIVE);
?>
<div class="block1">
    <div class="ost-switch">
        <input<?php echo $checked ? ' checked' : ''; ?>
            type="checkbox"
            id="<?php echo $planId; ?>"
            name="ids[]"
            class="<?php echo join(' ', $classes); ?>"
            value="<?php echo $subscription->id; ?>"/>
        <label for="<?php echo $planId; ?>"></label>
    </div>
</div>
<div class="block11">
    <label><?php echo $plan->name; ?></label>
</div>
