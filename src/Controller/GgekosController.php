<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class GgekosController extends AbstractController
{
    /**
     * @Route("/", name="ggekos")
     */
    public function index(Request $request, KernelInterface $kernel)
    {
        return $this->render('ggekos/index.html.twig',array_merge(
            ["locale" => $this->detectLanguage($request)],
            Yaml::parseFile($kernel->getProjectDir().'/public/ggekos_'.$this->detectLanguage($request).'.yml')
        ),
        );
    }

    /**
     * @Route("/blog", name="blogList")
     */
    public function list(Request $request, KernelInterface $kernel)
    {
        $finder = new Finder();
        $finder->files()->in($kernel->getProjectDir().'/public/blog/');

        foreach ($finder as $file) {
            $fileNameExplode = explode('.', $file->getFileName());

            if (2 == count($fileNameExplode) && 'yml' == $fileNameExplode[1]) {
                $list[] = Yaml::parseFile($file->getPathName());
            }
        }
        
        return $this->render('ggekos/list.html.twig', array_merge([
            'list' => $list
            ],
            Yaml::parseFile($kernel->getProjectDir().'/public/ggekos_'.$this->detectLanguage($request).'.yml'))
        );
    }

    /**
     * @Route("/blog/rss", name="blogRss")
     */
    public function rss(KernelInterface $kernel, Filesystem $filesystem) 
    {
        $finder = new Finder();
        $finder->files()->in($kernel->getProjectDir().'/public/blog/');

        $channel = [
                'title' => 'colella.fr',
                'link' => 'https://colella.fr',
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
    public function single(Request $request, KernelInterface $kernel, Filesystem $filesystem, $slug)
    {
        $path = $kernel->getProjectDir().'/public/blog/'.$slug.'.yml';

        if (!$filesystem->exists($path)) {
            //404
        }

        return $this->render('ggekos/single.html.twig', 
            array_merge(
                Yaml::parseFile($path),
                Yaml::parseFile($kernel->getProjectDir().'/public/ggekos_'.$this->detectLanguage($request).'.yml')
                )
        );
    }


    /** 
     * 
    */
    private function detectLanguage(Request $request)
    {
        $availableLocales = ["fr", "en"];

        if (in_array($locale = $request->query->get('locale'), $availableLocales)) {
            return $locale;
        }

        return $request->getPreferredLanguage($availableLocales);
    }
}
