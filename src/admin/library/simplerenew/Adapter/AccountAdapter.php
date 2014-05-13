<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\Adapter;

defined('_JEXEC') or die();

class AccountAdapter extends BaseAdapter
{
    public $code = null;
    public $status = null;
    public $username = null;
    public $email = null;
    public $firstname = null;
    public $lastname = null;
    public $company = null;
    public $address = null;
}
