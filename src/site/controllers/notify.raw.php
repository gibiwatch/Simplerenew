<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class SimplerenewControllerNotify extends SimplerenewControllerBase
{
    public function receive()
    {
        //$this->authenticate();

        $container    = SimplerenewFactory::getContainer();
        //$package      = file_get_contents('php://input');
        $notification = $container->getNotification();

        $newAccount = '<new_account_notification>
  <account>
    <account_code>J25_509</account_code>
    <username>fred</username>
    <email>fred@ostraining.xxx</email>
    <first_name>Fred</first_name>
    <last_name>Flintstone</last_name>
    <company_name nil="true"></company_name>
  </account>
</new_account_notification>';

        $billingUpdate = '<billing_info_updated_notification>
  <account>
    <account_code>J25_509</account_code>
    <username>fred</username>
    <email>fred@ostraining.xxx</email>
    <first_name>Fred</first_name>
    <last_name>Flintstone</last_name>
    <company_name nil="true"></company_name>
  </account>
</billing_info_updated_notification>';

        $newSub = '<?xml version="1.0" encoding="UTF-8"?>
<new_subscription_notification>
  <account>
    <account_code>J25_509</account_code>
    <username>fred</username>
    <email>fred@ostraining.xxx</email>
    <first_name>Fred</first_name>
    <last_name>Flintstone</last_name>
    <company_name nil="true"></company_name>
  </account>
  <subscription>
    <plan>
      <plan_code>plan-1</plan_code>
      <name>Personal (Bimonthly)</name>
    </plan>
    <uuid>28ee1fab492d5e733ab3354c7c92110b</uuid>
    <state>active</state>
    <quantity type="integer">1</quantity>
    <total_amount_in_cents type="integer">4700</total_amount_in_cents>
    <subscription_add_ons type="array"/>
    <activated_at type="datetime">2014-07-28T23:35:44Z</activated_at>
    <canceled_at type="datetime" nil="true"></canceled_at>
    <expires_at type="datetime" nil="true"></expires_at>
    <current_period_started_at type="datetime">2014-07-28T23:35:44Z</current_period_started_at>
    <current_period_ends_at type="datetime">2014-09-28T23:35:44Z</current_period_ends_at>
    <trial_started_at type="datetime" nil="true"></trial_started_at>
    <trial_ends_at type="datetime" nil="true"></trial_ends_at>
  </subscription>
</new_subscription_notification>';

        try {
            $notification->loadPackage($newSub);
        } catch (Exception $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    /**
     * Check credentials of caller
     *
     * @throws Exception
     */
    protected function authenticate()
    {
        $app       = SimplerenewFactory::getApplication();
        $container = SimplerenewFactory::getContainer();

        $username = $app->input->server->getUsername('PHP_AUTH_USER');
        $password = $app->input->server->getString('PHP_AUTH_PW');

        if (!$username) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        // Check the password
        try {
            $user = $container->getUser()->loadByUsername($username);
        } catch (Simplerenew\Exception\NotFound $e) {
            throw new Exception($e->getMessage(), 403);
        }
        $user->validate($password);

        // Check for proper access
        $jUser = SimplerenewFactory::getUser($user->id);
        if (!$jUser->authorise('core.manage', 'com_simplerenew')) {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
        }
    }
}
