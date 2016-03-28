<?php

namespace Blackjack\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\View;

/**
 * Class DefaultController
 */
class DefaultController extends AbstractActionController
{
    /**
     * @return View
     */
    public function indexAction()
    {
        return new View();
    }
}
