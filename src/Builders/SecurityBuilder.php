<?php

namespace LaravelJsonApi\OpenApiSpec\Builders;

use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityRequirement;
use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityScheme;
use Illuminate\Support\Collection;
use LaravelJsonApi\OpenApiSpec\Descriptors\Server;

class SecurityBuilder extends Builder
{
    protected Collection $securitySchemes;

    protected Collection $securityRequirements;

    /**
     * @return array|\Illuminate\Support\Collection
     */
    public function build(): Collection
    {
        $this->securitySchemes = collect();
        $securityConfig = collect(config("openapi.security"));

        if ($securityConfig->contains('bearer')) {
            $schema = SecurityScheme::create('BearerAuth')->type(SecurityScheme::TYPE_HTTP)->scheme('bearer');
            $this->securitySchemes->add($schema);
        }

        $this->securityRequirements = $this->securitySchemes
            ->map(function ($securityScheme, $key) {
                return SecurityRequirement::create()->securityScheme($securityScheme);
            });

        return $this->securityRequirements;
    }

    public function getSecuritySchemes(): Collection
    {
        return $this->securitySchemes;
    }
}
