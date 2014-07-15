<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Primitive;

defined('_JEXEC') or die();

class PayPal extends AbstractPayment
{
    /**
     * @var string
     */
    public $agreementId = null;

    /**
     * Determine if the payment type exists
     *
     * @return bool
     */
    public function exists()
    {
        return (bool)($this->agreementId != '');
    }
}
