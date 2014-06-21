<?php
/**
 * @package   Simplerenew
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2014 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

namespace Simplerenew;

use Simplerenew\Exception;

defined('_JEXEC') or die();

class Cache
{
    /**
     * @var string
     */
    protected $path = 'cache/';

    /**
     * @var string
     */
    protected $extension = '.cache';

    /**
     * @var int
     */
    protected $expiration = 1800;

    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (!empty($config['path'])) {
            $this->setPath($config['path']);
        }
        $this->checkPath();

        if (!empty($config['extension'])) {
            $this->setExtension($config['extension']);
        }
        if (!empty($config['expiration'])) {
            $this->setExpiration($config['expiration']);
        }
    }

    /**
     * Store some data under the desired key
     *
     * @param string $key
     * @param mixed $data
     *
     * @return bool
     */
    public function store($key, $data)
    {
        $path = $this->getDataPath($key);
        try {
            file_put_contents($path, json_encode($data));
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Try to get a previously loaded cache key
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function retrieve($key)
    {
        if ($this->expiration && !$this->isExpired($key)) {
            $path = $this->getDataPath($key);
            return json_decode(file_get_contents($path));
        }
        return null;
    }

    /**
     * Test if the keyed data is expired or not. If not cached at all
     * will return true
     *
     * @param $key
     *
     * @return bool
     */
    public function isExpired($key)
    {
        $expired = true;
        $path = $this->getDataPath($key);

        if (file_exists($path)) {
            $timestamp = filemtime($path);
            $expired = ((time() - $timestamp) > $this->expiration);
            if ($expired) {
                $this->delete($key);
            }
        }
        return $expired;
    }

    /**
     * Delete the cached data under the selected key. If not previously
     * cached, returns true
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        $path = $this->getDataPath($key);
        if (file_exists($path)) {
            return (bool)@unlink($path);
        }
        return true;
    }

    /**
     * Purge all cached items
     *
     * @param bool $all [optional] false (default) - keep unexpired items
     * @param null $path [optional] used for internal recursion
     *
     * @return int
     */
    public function purge($all = false, $path = null)
    {
        $countErased = 0;

        $path = $path ?: $this->path;

        $dir = dir($path);
        while ($name = $dir->read()) {
            $file = $path . $name;
            if (is_dir($file)) {
                if (!in_array($name, array('.', '..'))) {
                    $countErased += $this->eraseAll($all, $file . '/');
                    rmdir($file);
                }
            } elseif (preg_match('/' . $this->extension . '$/', $name)) {
                $key = substr($name, 0, 0-strlen($this->extension));
                if ($all || $this->isExpired($key)) {
                    if (@unlink($file)) {
                        $countErased += 1;
                    }
                }
            }
        }

        return $countErased;
    }

    /**
     * Check for a valid path for storing the cache files
     *
     * @return bool
     * @throws Exception
     */
    protected function checkPath()
    {
        if (!is_dir($this->path)) {
            if (!mkdir($this->path, 0664, true)) {
                throw new Exception('Unable to create cache directory ' . $this->path);
            }
            $htaccess = array(
                '<Files "*">',
                '    order deny,allow',
                '    deny from all',
                '</Files>'
            );
            file_put_contents($this->path . '.htaccess', join("\n", $htaccess));

        } elseif (!is_readable($this->path) || !is_writable($this->path)) {
            if (!chmod($this->path, 0664)) {
                throw new Exception($this->path . ' must be readable and writable');
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Cache
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     *
     * @return Cache
     */
    public function setExtension($extension)
    {
        $this->extension = '.' . trim($extension, ' .');
        return $this;
    }

    /**
     * @return int
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @param int $expiration
     *
     * @return Cache
     */
    public function setExpiration($expiration)
    {
        $this->expiration = (int)$expiration;
        return $this;
    }

    /**
     * Get the path to a specific cached data item
     *
     * @param $key
     *
     * @return string
     */
    protected function getDataPath($key)
    {
        return $this->path . $key . $this->getExtension();
    }
}
