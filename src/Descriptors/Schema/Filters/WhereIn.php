<?php


namespace LaravelJsonApi\OpenApiSpec\Descriptors\Schema\Filters;


use GoldSpecDigital\ObjectOrientedOAS\Objects\Example;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use LaravelJsonApi\Core\Support\Str;
use LaravelJsonApi\Eloquent\Filters\WhereNotIn;


class WhereIn extends FilterDescriptor
{

    /**
     * @todo Pay attention to delimiter
     */
    public function filter(): array {
        $key = $this->filter->key();

        /**
         * @see WhereIn::guessColumn() 
         * "myMetals" to "my_metal"
         */
        $column = Str::underscore(
            Str::singular($key)
        );

        $examples = collect($this->generator->resources()
          ->resources($this->route->schema()::model()))
          ->pluck($column)
          ->map(function ($f) {
              // @todo Watch out for ids?
              if ($f === null) {
                  $f = 'todoValueIsNull';
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
            ->schema(Schema::array()->items(Schema::string())->default(Example::create('empty')->value([])))
            ->examples(...$examples)
            ->style('form')
            ->explode(false),
        ];
    }

    protected function description(): string
    {
        return $this->filter instanceof WhereNotIn ? "A list of {$this->filter->key()}s to exclude by." : "A list of {$this->filter->key()}s to filter by.";
    }

}
