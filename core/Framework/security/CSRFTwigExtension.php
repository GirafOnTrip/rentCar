<?php

namespace Core\Framework\Security;

use Twig\TwigFunction;
use Core\Framework\Security\CSRF;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;

class CSRFTwigExtension extends AbstractExtension
{

    private CSRF $csrf;

    public function __construct(ContainerInterface $container)
    {
        $this->csrf = $container->get(CSRF::class);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf', [$this, 'generateToken'], ['is_safe' => ['html']])
        ];
    }

    public function generateToken(): string
    {
        return $this->csrf->generateToken();
    }
}
