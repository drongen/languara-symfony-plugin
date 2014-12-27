<?php

namespace Languara\SymfonyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Languara\SymfonyBundle\Library;

class LanguaraController extends Controller
{    
    /**
     * @Route("/pull", name="_languara_pull")
     * @Template()
     */
    public function pullAction()
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        
        $lib_languara = new Library\LanguaraWrapper($this->get('kernel')->getRootDir());
        
        // validate the request
        if (! $lib_languara->check_auth($this->get('request')->request->get('external_request_id'), $this->get('request')->request->get('signature')))
        {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            echo('Authentication failed, check your configuration and try again!');
            return $response;
        }
        
        try
        {
            $lib_languara->download_and_process();
        }
        catch(\Exception $e)
        {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setContent(($e->getMessage()));
            return $response;
        }
        
        echo 1;        
        return $response;
    }
    
    /**
     * @Route("/push", name="_languara_push")
     * @Template()
     */
    public function pushAction()
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        
        $lib_languara = new Library\LanguaraWrapper($this->get('kernel')->getRootDir());
        
        // validate the request
        if (! $lib_languara->check_auth($this->get('request')->request->get('external_request_id'), $this->get('request')->request->get('signature')))
        {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            echo('Authentication failed, check your configuration and try again!');
            return $response;
        }
        
        try
        {
            $lib_languara->upload_local_translations();
        }
        catch(\Exception $e)
        {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setContent(($e->getMessage()));
            return $response;
        }
        
        echo 1;
        return $response;
    }
}
