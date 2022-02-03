<?php


namespace LaravelJsonApi\OpenApiSpec\Descriptors\Schema\Filters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Example;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema as OASchema;
use LaravelJsonApi\Core\Support\Str;


class Where extends FilterDescriptor
{

    /**
     *
     * @todo Pay attention to isSingular
     */
    public function filter(): array
    {
        $key = $this->filter->key();
        $examples = collect($this->generator->resources()
          ->resources($this->route->schema()::model()))
          ->pluck(Str::underscore($key))
          ->map(function ($f, $k) {
              // @todo Watch out for ids?
              
              if ($f === null) {
                  /** TODO: Attribute by filter not found (custom filter nameILike)  */
                  
                  return Example::create('todo ' . $k)->value('todo ' . $k);
              }
              
              return Example::create($f)->value($f);
          })
          ->toArray();

        return [
          Parameter::query()
            ->name("filter[{$this->filter->key()}]")
            ->description($this->description())
            ->required(false)
            ->allowEmptyValue(false)
            ->schema(OASchema::string()->default(''))
            ->examples(...$examples)
        ];
    }

    protected function description(): string
    {
        return 'Filters the records';
    }

}
