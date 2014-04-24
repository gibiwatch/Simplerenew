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
    /**
     * @var array Joomla user name split into first/last
     */
    private $splitName = null;

    /**
     * Create a new Joomla! user.
     *
     * @param $data associative array passed by the parent User class
     *
     * @return User
     * @throws \Exception
     */
    public static function createUser($data)
    {
        // Reformat for Joomla
        $name = trim($data['firstname'] . ' ' . $data['lastname']);
        if (!$name) {
            $name = $data['username'];
        }

        $temp = array(
            'email1'    => $data['email'],
            'username'  => $data['username'],
            'name'      => $name,
            'password1' => $data['password']
        );

        \JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_users/models');

        /** @var \UsersModelRegistration $model */
        $model = \JModelLegacy::getInstance('Registration', 'UsersModel');

        $lang = \JFactory::getLanguage();
        $lang->load('com_users', JPATH_SITE);

        if ($id = $model->register($temp)) {
            $newUser = User::getInstance($id);
            return $newUser;
        }

        throw new \Exception(join('<br/>', $model->getErrors()));
    }

    /**
     * Get the first name as parsed from the Joomla User name field
     *
     * @return string
     */
    protected function getFirstname()
    {
        return $this->getName('firstname');
    }

    /**
     * Get the lastname as parsed from the Joomla User name field
     *
     * @return string
     */
    protected function getLastname()
    {
        return $this->getName('lastname');
    }

    /**
     * Override the default full name method since Joomla already has it
     *
     * @return string
     */
    protected function getFullname()
    {
        return $this->user->name;
    }

    /**
     * Parse and return the selected first/last name field from
     * the Joomla User name field
     *
     * @param $field ['firstname'|'lastname']
     *
     * @return string
     */
    protected function getName($field)
    {
        if ($this->splitName === null) {
            $name = preg_split('/\s/', $this->user->name);

            if (count($name) == 1) {
                $firstname = $name[0];
                $lastname  = '';
            } elseif (count($name) > 1) {
                $lastname  = array_pop($name);
                $firstname = join(' ', $name);
            } else {
                $firstname = '';
                $lastname  = '';
            }

            $this->splitName = array(
                'firstname' => $firstname,
                'lastname'  => $lastname
            );
        }

        if (!empty($this->splitName[$field])) {
            return $this->splitName[$field];
        }
        return '';
    }

    /**
     * @param int $id
     *
     * @return \JUser
     * @throws \Exception
     */
    protected function getSystemObject($id = null)
    {
        $user = \JFactory::getUser($id);
        if (!$user || $user->id <= 0) {
            throw new \Exception(__CLASS__ . ':: User ID not found - ' . (int)$id);
        }
        return $user;
    }
}

