<?php
/**
 * @package   com_recurly
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class RecurlyApiAccount extends RecurlyApibase
{
    protected $classname = 'Recurly_Account';

    /**
     * @var string Prefix prepended to User IDs for creating account codes
     */
    protected static $prefix = 'OS';

    /**
     * @var RecurlyApiSubscription
     */
    protected $subscription = null;

    /**
     * See comments for $this->load()
     *
     * @param mixed $id
     */
    public function __construct($id = null)
    {
        if (($id != '') && is_numeric($id)) {
            $id = self::getAccountCode($id);
        }
        parent::__construct($id);
    }

    public function isValid()
    {
        if (parent::isValid()) {
            return ($this->account_code != '');
        }
    }
    /**
     * Accounts can be provided either as an int to
     * specify a user ID, a string that will be passed
     * through to Recurly as an account code or as a
     * Recurly_Account object. Use -1 to load current user
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function load($id)
    {
        if (is_numeric($id)) {
            $id = self::getAccountCode($id);
        }
        return parent::load($id);
    }

    /**
     * Create account code from user ID.
     *
     * @param int $userId
     *
     * @return null|string
     */
    public static function getAccountCode($userId = null)
    {
        $userId = (int)$userId;
        if ($userId <= 0) {
            $user   = JFactory::getUser();
            $userId = $user->id;
        }

        if ($userId > 0) {
            return self::$prefix . '_' . $userId;
        }
        return null;
    }

    /**
     * User ID for this account
     *
     * @return int|null
     */
    public function getUserId()
    {
        if ($this->recurly->account_code != '') {
            list($prefix, $id) = explode('_', $this->recurly->account_code);
            if ($prefix == self::$prefix && (int)$id > 0) {
                return (int)$id;
            }
        }
        return null;
    }

    /**
     * Set the account_code for a new account. Will not overwrite
     * An existing account_code
     *
     * @param int $userId
     *
     * @return string|null
     */
    public function setAccountCode($userId = null)
    {
        if (!$this->isValid() || ($this->recurly->account_code == '')) {
            $class = $this->classname;
            $this->recurly = new $class(self::getAccountCode($userId));
            return $this->recurly->account_code;
        }
        return null;
    }

    /**
     * Set the first_name/last_name fields from a fullname
     * We assume the fullname is whitespace separated words
     * with the last name being the last word in the string
     *
     * @param $fullName
     *
     * @return bool
     */
    public function setFullname($fullName)
    {
        if (parent::isValid()) {
            $name = preg_split('/\s/', $fullName);

            $this->recurly->last_name = array_pop($name);
            $this->recurly->first_name  = join(' ', $name);
            return true;
        }
        return false;
    }

    public function getSubscription()
    {
        if ($this->subscription === null && ($this->account_code != '')) {
            try {
                $subscriptions = iterator_to_array(Recurly_SubscriptionList::getForAccount($this->account_code));
                $this->subscription = array_shift($subscriptions);
            } catch (Exception $e) {}
        }
        return $this->subscription;
    }
}
