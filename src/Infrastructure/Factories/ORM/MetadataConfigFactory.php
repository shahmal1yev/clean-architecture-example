<?php

namespace Onion\Infrastructure\Factories\ORM;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\ORMSetup;

readonly class MetadataConfigFactory
{
    public function __construct(
        private array $paths = [__DIR__ . "/../../Entities"],
        private bool $isDevMode = false
    )
    {
    }

    public function create(): Configuration
    {
        $config = ORMSetup::createAttributeMetadataConfig($this->paths, $this->isDevMode);
        $config->enableNativeLazyObjects(true);
        return $config;
    }
}
