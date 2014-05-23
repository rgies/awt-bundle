<?php

namespace RGies\AwtBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * PlantUml controller.
 *
 */
class PlantUmlController extends Controller
{
    /**
     * @Route("/uml-editor/", name="plantUml")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

}
