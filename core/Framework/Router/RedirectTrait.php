<?php 

namespace Core\Framework\Router;

use GuzzleHttp\Psr7\Response;

// Le trait se fusionne avec un autre objet, il pourra acceder a toutes ces propriétés ( ici objet Response contient en paramtre Router )

trait RedirectTrait{

    public function redirect(string $name, array $params = [])
    {
        $path = $this->router-> generateUri($name, $params);
        return (new Response)
            ->withHeader('Location', $path);
    }
}