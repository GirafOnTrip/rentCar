<?php

namespace App\Admin\Action;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdminAction
{
    private RendererInterface $renderer;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container){

        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
    }

    public function home(ServerRequestInterface $request){

        return $this->renderer->render('@admin/home');
    }
}