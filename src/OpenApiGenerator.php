<?php

namespace LaravelJsonApi\OpenApiSpec;

use Storage;
use Symfony\Component\Yaml\Yaml;

class OpenApiGenerator
{

    /**
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\ValidationException
     */
    public function generate(string $serverKey, string $format = 'yaml'): string
        {

          $generator = new Generator($serverKey);
          $openapi = $generator->generate();

          // TODO: расскоментить
          // $openapi->validate();

          if ($format === 'yaml') {
            $output = Yaml::dump($openapi->toArray());
            // Save to storage
            Storage::put($serverKey.'_openapi.yaml', $output);
          } elseif ($format === 'json') {
            $output = json_encode($openapi->toArray(), JSON_PRETTY_PRINT);
            /** TODO: добавить возможность настраивать драйвер и место сохранения */
            \Illuminate\Support\Facades\Storage::disk('docs')->put($serverKey . '_openapi.json', $output);
          }

          $openapi->validate();

          return $output;
        }
}
