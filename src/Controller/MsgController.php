<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;



Class MsgController extends AbstractController{

    /**
     * @Route("/search", name="find_products", methods={"POST"})
     */
    public function find(Request $request): ?Response{
        dd($request);
        return $this->redirectToRoute('product_index');
    }
}