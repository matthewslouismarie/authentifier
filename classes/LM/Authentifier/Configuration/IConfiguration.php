<?php

namespace LM\Authentifier\Configuration;

use Twig_Function;

/**
 * @todo Rename to IExternalConfiguration or ExternalEnvironment or… ?
 * Or even better, UserConfiguration.
 */
interface IConfiguration
{
    public function getAssetUri(string $assetId): string;
}