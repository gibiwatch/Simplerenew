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
     * @var string
     */
    protected $domain = 'simplerenew';

    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $path = empty($config['path']) ? $this->path : $config['path'];
        $this->setPath($path);
        $this->checkPath();

        if (!empty($config['extension'])) {
            $this->setExtension($config['extension']);
        }
        if (!empty($config['expiration'])) {
            $this->setExpiration($config['expiration']);
        }
        if (!empty($config['domain'])) {
            $this->setDomain($config['domain']);
        }
    }

    public function __clone()
    {
        $this->domain = 'simplerenew';
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
            file_put_contents($path, serialize($data));
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
            return unserialize(file_get_contents($path));
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
            if (!mkdir($this->path, 0755, true)) {
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
            if (!chmod($this->path, 0755)) {
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
     * @param bool   $relative $path is relative to the Cache class path
     *
     * @return Cache
     */
    public function setPath($path, $relative = true)
    {
        if (is_string($path)) {
            $path = ($relative ? __DIR__ . '/' : '') . $path;
            $this->path = $path;
        }
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
        if (preg_match('/^[a-zA-Z0-9\.]+$/', $extension)) {
            $this->extension = '.' . str_replace('.', '', trim(strtolower($extension)));
        }
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
        if (is_numeric($expiration)) {
            $this->expiration = (int)$expiration;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return Cache
     */
    public function setDomain($domain)
    {
        if (preg_match('/^[a-zA-Z0-9\.\\\]+$/', $domain)) {
            $this->domain = strtolower(str_replace('\\', '.', $domain));
        }
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
        $path = $this->path . $this->domain . '.' . $key . $this->getExtension();
        return $path;
    }
}
