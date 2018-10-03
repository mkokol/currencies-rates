<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Annotations;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Annotations\Route(
 *     service="App\Controller\HomeController"
 * )
 */
class HomeController extends Controller
{
    /**
     * @Annotations\Route(
     *     "/",
     *     name="home"
     * )
     * @Annotations\Method("GET")
     */
    public function getAction()
    {
        return $this->render('index.html');
    }
}