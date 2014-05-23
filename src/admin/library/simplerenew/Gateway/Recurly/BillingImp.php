<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Billing;
use Simplerenew\Exception;
use Simplerenew\Gateway\BillingInterface;

defined('_JEXEC') or die();

class BillingImp extends AbstractRecurlyBase implements BillingInterface
{
    protected $fieldMap = array(
        'firstname' => 'first_name',
        'lastname'  => 'last_name',
        'region'    => 'state',
        'postal'    => 'zip',
        'ipaddress' => 'ip_address'
    );

    /**
     * @var array Associative array of \Recurly_BillingInfo objects already loaded
     */
    protected $accountsLoaded = array();

    /**
     * @param Billing $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Billing $parent)
    {
        $billing = $this->getBilling($parent->account->code);
        $parent->setProperties($billing, $this->fieldMap);

        if ($parent->address instanceof Address) {
            $parent->address->setProperties(
                $billing->address,
                array(
                    'region' => 'state',
                    'postal' => 'zip'
                )
            );
        }
    }

    /**
     * @param $accountCode
     *
     * @return \Recurly_BillingInfo
     * @throws \Simplerenew\Exception
     */
    protected function getBilling($accountCode)
    {
        try {
            if (empty($this->accountsLoaded[$accountCode])) {
                $this->accountsLoaded[$accountCode] = \Recurly_BillingInfo::get($accountCode, $this->client);
            }

        } catch (\Recurly_NotFoundError $e) {
            return new \Recurly_BillingInfo();

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $this->accountsLoaded[$accountCode];
    }
}
