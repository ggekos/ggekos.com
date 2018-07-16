<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\HttpFoundation\Response;

class GgekosController extends Controller
{
    /**
     * @Route("/", name="ggekos")
     */
    public function index(KernelInterface $kernel)
    {
        return $this->render('ggekos/index.html.twig', 
            Yaml::parseFile($kernel->getProjectDir().'/public/ggekos.yml'));
    }

    /**
     * @Route("/blog", name="blogList")
     */
    public function list(KernelInterface $kernel)
    {
        $finder = new Finder();
        $finder->files()->in($kernel->getProjectDir().'/public/blog/');

        foreach ($finder as $file) {
            $list[] = explode('.', $file->getFileName())[0];
        }
        
        return $this->render('ggekos/list.html.twig', [
            'list' => $list
        ]);
    }

    /**
     * @Route("/blog/rss", name="blogRss")
     */
    public function rss(KernelInterface $kernel, Filesystem $filesystem) 
    {
        $finder = new Finder();
        $finder->files()->in($kernel->getProjectDir().'/public/blog/');

        $channel = [
                'title' => 'ggekos.com',
                'link' => 'https://ggekos.com',
                'description' => 'description'
            ];

        foreach ($finder as $file) {
            $channel['item'][] = Yaml::parseFile($file->getRealPath());
        }

        $encoder = new XmlEncoder();

        $context = [
            'xml_encoding' => 'iso-8859-1',
            'xml_version' => '1.0',
            'xml_root_node_name' => 'channel'];

        $response = new Response($encoder->encode($channel, 'xml', $context));
        $response->headers->set('Content-Type', 'xml');

        return $response;
    }

    /**
     * @Route("/blog/{slug}", name="blogSingle")
     */
    public function single(KernelInterface $kernel, Filesystem $filesystem, $slug)
    {
        $path = $kernel->getProjectDir().'/public/blog/'.$slug.'.yml';

        if (!$filesystem->exists($path)) {
            //404
        }

        return $this->render('ggekos/single.html.twig', Yaml::parseFile($path));
    }

}
