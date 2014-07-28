<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Api;

use Simplerenew\Exception;
use Simplerenew\Gateway\CouponInterface;

defined('_JEXEC') or die();

class Coupon extends AbstractApiBase
{
    // percent, dollars
    const TYPE_PERCENT = 1;
    const TYPE_AMOUNT  = 2;

    const STATUS_ACTIVE  = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_MAX     = 3;
    const STATUS_UNKNOWN = 0;

    public $code = null;
    public $name = null;
    public $status = null;
    public $type = null;
    public $currency = null;
    public $amount = null;
    public $expires = null;
    public $max_uses = null;
    public $plans = null;
    public $created = null;

    /**
     * @var CouponInterface
     */
    private $imp = null;

    public function __construct(CouponInterface $imp, array $config = array())
    {
        $this->imp = $imp;
    }

    /**
     * @param $code
     *
     * @return Coupon
     * @throws Exception
     */
    public function load($code)
    {
        $this->clearProperties();
        $this->code = $code;
        $this->imp->load($this);

        return $this;
    }
}
