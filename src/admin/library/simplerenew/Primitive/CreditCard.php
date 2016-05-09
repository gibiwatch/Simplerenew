<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014-2016 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Primitive;

defined('_JEXEC') or die();

class CreditCard extends AbstractPayment
{
    /**
     * @var string
     */
    public $type = null;

    /**
     * @var string
     */
    public $number = null;

    /**
     * @var string
     */
    public $month = null;

    /**
     * @var string
     */
    public $year = null;

    /**
     * @var string
     */
    public $cvv = null;

    /**
     * @var string
     */
    public $lastFour = null;

    public function __construct($data = null)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (empty($data['year']) || empty($data['month'])) {
            $ccDate        = getdate();
            $ccDate        = getdate(mktime(0, 0, 0, $ccDate['mon'] + 1, 1, $ccDate['year']));
            $data['year']  = $ccDate['year'];
            $data['month'] = $ccDate['mon'];
        }
        parent::__construct($data);
    }

    /**
     * Determine if the payment type exists
     *
     * @return bool
     */
    public function exists()
    {
        return (bool)($this->lastFour != '');
    }
}
