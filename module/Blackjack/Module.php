<?php

namespace Blackjack;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    /**
     * @inheritdoc
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__.'/src/'.__NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap()
    {
        $this->initSession(
            array(
                'remember_me_seconds' => 180,
                'use_cookies'         => true,
                'cookie_httponly'     => true,
            )
        );
    }

    private function initSession(array $config)
    {
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($config);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->start();
        Container::setDefaultManager($sessionManager);
    }
}
