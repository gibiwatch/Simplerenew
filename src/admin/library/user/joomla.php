<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User;

defined('_JEXEC') or die();

class Joomla extends User
{
    private $firstname = null;
    private $lastname = null;

    public static function create(
        $email,
        $username,
        $password,
        $firstname = null,
        $lastname = null,
        $groups = array()
    ) {
        // @TODO: Write this!
    }

    protected function getFirstname()
    {
        if ($this->firstname === null) {
            $this->loadName();
        }
        return $this->firstname;
    }

    protected function getLastname()
    {
        if ($this->lastname === null) {
            $this->loadName();
        }
        return $this->lastname;
    }

    protected function loadName()
    {
        $name = preg_split('/\s/', $this->user->name);

        if (count($name) == 1) {
            $this->firstname = $name[0];
            $this->lastname  = '';
        } elseif (count($name) > 1) {
            $this->lastname  = array_pop($name);
            $this->firstname = join(' ', $name);
        } else {
            $this->firstname = '';
            $this->lastname  = '';
        }
    }

    /**
     * @param int $id
     *
     * @return \JUser
     */
    protected function getSystemObject($id = null)
    {
        if ($id < 0) {
            $user = new \JUser();
        } else {
            $user = \JFactory::getUser($id);
        }
        return $user;
    }
}

