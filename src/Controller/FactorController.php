<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class FactorController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/apiFactor', name: 'app_factor')]
    public function index(Request $request): JsonResponse
    {
        $base_path='https://api.stackexchange.com/2.3/questions?site=stackoverflow';

        $data=json_decode($request->getContent(),true);
        
        if(empty($data['tagged']) || !$data['tagged'] ){
            return new JsonResponse(['status'=>["tagged"=>"Etiqueta es obligatoria"]],Response::HTTP_BAD_REQUEST);
        }
        $base_path=$base_path.'&tagged='.$data['tagged'];
        
        if(isset($data['fromdate'])){
            $base_path=$base_path.'&fromdate='.strtotime($data['fromdate']);
        }
        if(isset($data['todate'])){
            $base_path=$base_path.'&todate='.strtotime($data['todate']);
        }
     
        
        $response = $this->client->request(
            'GET', $base_path
        );

        if($response->getStatusCode()==200){
            return new JsonResponse(json_decode($response->getContent(),true),Response::HTTP_OK);
        }else{
            return new JsonResponse(['status'=>'Error en carga de datos'],Response::HTTP_OK);
        }
    }
}