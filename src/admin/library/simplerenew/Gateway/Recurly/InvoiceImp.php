<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Gateway\Recurly;

use Simplerenew\Api\Invoice;
use Simplerenew\Exception;
use Simplerenew\Gateway\InvoiceInterface;

defined('_JEXEC') or die();

class InvoiceImp extends AbstractRecurlyBase implements InvoiceInterface
{

    /**
     * @param Invoice $parent
     *
     * @return void
     * @throws Exception
     */
    public function load(Invoice $parent)
    {
        // TODO: Implement load() method.
    }
}
