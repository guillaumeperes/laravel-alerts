<?php

namespace GuillaumePeres\Alerts;

use Illuminate\Session\Store as Session;
use Illuminate\Config\Repository as Config;
use \BadMethodCallException;
use \InvalidArgumentException;

class Alerts 
{
    /**
    * @var \Illuminate\Session\Store
    */
    protected $session;

    /**
    * @var \Illuminate\Config\Repository
    */
    protected $config;

    /**
    * Constructor.
    *
    * @param \Illuminate\Session\Store $session
    * @param \Illuminate\Config\Repository $config
    * @param array $messages
    * @return \GuillaumePeres\Alerts\Alerts
    */
    public function __construct(Session $session, Config $config, array $messages = array())
    {
        $this->config = $config;
        $this->session = $session;
    }

    /**
    * Stores the given alert message into the session.
    *
    * @param string
    * @param string
    */
    public function add(string $level, string $alert)
    {
        if (!in_array($level, $this->getLevels())) {
            throw new InvalidArgumentException("Unknown level [$level].");
        }

        $key = $this->getSessionKey();
        $alerts = $this->session->pull($key, array());
        $alerts[$level][] = $alert;
        $this->session->put($key, $alerts);
    }

    public function pull(string $level)
    {
        if (!in_array($level, $this->getLevels())) {
            throw new InvalidArgumentException("Unknown level [$level].");
        }

        $output = array();
        $key = $this->getSessionKey();
        $alerts = $this->session->pull($key, array());

        if (isset($alerts[$level]) && is_array($alerts[$level])) {
            $output = $alerts[$level];
            unset($alerts[$level]);
        }

        $this->session->put($key, $alerts);

        return $output;
    }

    /**
    * Deletes all messages.
    */
    public function flush()
    {
        $this->session->forget($this->getSessionKey());
    }

    /**
    * Checks to see if any messages exist.
    *
    * @param null $key A specific level you wish to check for.
    *
    * @return bool
    */
    public function has(string $level = null)
    {
        $alerts = $this->session->get($this->getSessionKey(), null);

        if (is_null($level) && is_array($alerts) && count($alerts) > 0) {
            return true;
        }
        if (!is_null($level) && is_array($alerts) && isset($alerts[$level]) && count($alerts[$level]) > 0) {
            return true;
        }

        return false;
    }

    /**
    * Get the number of messages.
    *
    * @param null $level A specific level name you wish to count.
    *
    * @return int
    */
    public function count(string $level = null)
    {
        $alerts = $this->session->get($this->getSessionKey(), null);

        if (is_null($alerts) || !is_array($alerts)) {
            return 0;
        }
        if (is_null($level)) {
            $total = 0;
            foreach ($alerts as $type => $group) {
                if (is_array($group)) {
                    $total += count($group);
                }
            }
            return $total;
        } else {
            if (isset($alerts[$level]) && is_array($alerts[$level])) {
                return count($alerts[$level]);
            }
        }

        return 0;
    }

    /**
    * Returns the alert levels from the config.
    *
    * @return array
    */
    public function getLevels()
    {
        return (array)$this->config->get('guillaumeperes.alerts.levels');
    }

    /**
    * Returns the Illuminate Config Repository.
    *
    * @return \Illuminate\Config\Repository
    */
    public function getConfig()
    {
        return $this->config;
    }

    /**
    * Returns the Illuminate Session Store.
    *
    * @return \Illuminate\Session\Store
    */
    public function getSession()
    {
        return $this->session;
    }

    /**
    * Returns the session key from the config.
    *
    * @return string
    */
    protected function getSessionKey()
    {
        return $this->config->get('guillaumeperes.alerts.session_key');
    }

    /**
    * Dynamically handle alert additions.
    *
    * @param string $method
    * @param array $parameters
    * @return mixed
    * @throws \BadMethodCallException
    */
    public function __call($method, $arguments)
    {
        if (in_array($method, $this->getLevels())) {
            $this->add($method, $arguments[0]);
            return;
        }

        throw new BadMethodCallException("Method [$method] does not exist.");
    }
}
