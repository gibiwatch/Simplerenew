<?php
/**
 * @package   mod_srmyaccount
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class ModSrmyaccountHelper
{
    /**
     * @var JRegistry
     */
    protected $params = null;

    /**
     * @var string
     */
    protected $gravatar = 'http://www.gravatar.com/avatar/';

    /**
     * @var string
     */
    protected $gravatarSecure = 'https://secure.gravatar.com/avatar/';

    public function __construct(JRegistry $params)
    {
        $this->params = $params;

        if (!defined('COM_RECURLY_ADMIN')) {
            require_once JPATH_ADMINISTRATOR . '/components/com_recurly/helpers/initialize.php';
        }

        JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/helpers/html');
    }

    /**
     * Create <img> tag for logged in user's Gravatar image
     *
     * @param mixed $attribs
     *
     * @return string
     */
    public function getAvatar($attribs = null)
    {
        $user = JFactory::getUser();
        if ($user->email) {
            $scheme    = JFactory::getURI()->getScheme();
            $emailHash = md5(strtolower(trim($user->email)));
            $baseUrl   = ($scheme == 'https') ? $this->gravatarSecure : $this->gravatar;
            $size      = $this->params->get('size', 80);
            $default   = $this->params->get('default_image');

            $queryVars = array(
                's' => $size
            );
            if ($default) {
                $queryVars['d'] = JFactory::getURI()->root() . $default;
            }

            // Get any attributes that may have been passed
            if ($attribs && is_string($attribs)) {
                $attribs = JUtility::parseAttributes($attribs);
            } elseif (!is_array($attribs)) {
                $attribs = array();
            }

            /*
             * Incorporate any additional requested styles
             * We'll assume the caller didn't use max-width/max-height because
             * it's too much trouble to deal with properly and we're lazy
             */
            $style = sprintf('max-width: %1$spx; max-height: %1$spx;', $size);
            if (!empty($attribs['style'])) {
                $style .= ' ' . $attribs['style'];
            }

            $attribs = array_merge(
                $attribs,
                array(
                    'src'   => $baseUrl . $emailHash . '?' . http_build_query($queryVars),
                    'style' => $style
                )
            );
            return '<img ' . JArrayHelper::toString($attribs) . '/>';
        }

        return '';
    }

    /**
     * Get available Recurly account information.
     *
     * @return RecurlyApiAccount
     */
    public function getAccount()
    {
        $account = new RecurlyApiAccount();
        if (!$account->isValid()) {
            $user = JFactory::getUser();

            $account->email    = $user->email;
            $account->username = $user->username;

            $name = $user->guest ? 'Guest' : $user->name;
            $account->setFullname($name);
        }
        return $account;
    }
}
