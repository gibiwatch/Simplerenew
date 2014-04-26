<?php
/**
 * @package   com_simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew\User;

defined('_JEXEC') or die();

class Joomla implements UserAdapter
{
    /**
     * @var array Joomla user name split into first/last
     */
    private $splitName = null;

    /**
     * @var \JUser
     */
    private $juser = null;

    /**
     * @param int $id
     *
     * @return UserAdapter
     * @throws \Exception
     */
    public function load($id=null)
    {
        $this->juser = \JFactory::getUser($id);
        if (!$this->juser || $this->juser->id <= 0) {
            throw new \Exception(__CLASS__ . ':: User ID not found - ' . $id);
        }

        return $this;
    }

    /**
     * @param string $username
     *
     * @return UserAdapter
     * @throws \Exception
     */
    public function loadByUsername($username)
    {
        if ($id = \JUserHelper::getUserId($username)) {
            $this->load($id);
            return $this;
        }

        throw new \Exception(__CLASS__ . ':: Username not found ' . $username);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        switch ($name) {
            case 'firstname':
                return $this->getFirstname();
                break;

            case 'lastname':
                return $this->getLastname();
                break;

            case 'fullname':
                return $this->juser->get('name');
                break;

            default:
                return $this->juser->get($name);
        }
    }

    /**
     * @param array $data
     *
     * @return UserAdapter
     * @throws \Exception
     */
    public function create(array $data)
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
            $newUser = (new self())->load($id);
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
            $name = preg_split('/\s/', $this->juser->name);

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
}

