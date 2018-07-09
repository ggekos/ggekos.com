<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\KernelInterface;

class GgekosController extends Controller
{
    /**
     * @Route("/", name="ggekos")
     */
    public function index(KernelInterface $kernel)
    {
        $values = Yaml::parseFile($kernel->getProjectDir().'/public/ggekos.yml');

        return $this->render('ggekos/index.html.twig', [
            'values' => $values,
        ]);
    }
}
