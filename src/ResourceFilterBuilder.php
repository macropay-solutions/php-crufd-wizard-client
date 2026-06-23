<?php

namespace MacropaySolutions\CrufdWizardClient;

class ResourceFilterBuilder
{
    /**
     * the resource name defined in resource's model
     */
    private string $resource = '';
    /**
     * the resource's filters
     */
    private array $filters = [];

    public function __construct(string $relation = '')
    {
        $this->resource = $relation;
    }

    public function equals(string $column, $is): self
    {
        $this->filters[$column] = $is;

        return $this;
    }

    public function from(string $column, string $from): self
    {
        $this->filters[$column] = \array_merge(
            $this->filters[$column] ?? [],
            ['from' => $from]
        );

        return $this;
    }

    public function to(string $column, string $to): self
    {
        $this->filters[$column] = \array_merge(
            $this->filters[$column] ?? [],
            ['to' => $to]
        );

        return $this;
    }

    public function between(string $column, string $from, string $to): self
    {
        return $this->from($column, $from)->to($column, $to);
    }

    public function toArray(): array
    {
        return [$this->resource => $this->filters];
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getResource(): string
    {
        return $this->resource;
    }
}
