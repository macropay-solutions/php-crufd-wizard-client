<?php

namespace MacropaySolutions\CrufdWizardClient;

/**
 * @see ../README.md
 */
class QueryStringBuilder
{
    public bool $sqlDebug = false;
    public bool $inHeaderQuery = true;
    private ?int $simplePaginate = null;
    private ?string $cursor = null;
    private int $page = 1;
    private int $limit = 10;
    private array $sort = [];
    private array $withRelations = [];
    private array $countRelations = [];
    private array $existRelations = [];
    private ResourceFilterBuilder $resourceFilters;
    private array $rawRequest = [];

    public function __construct()
    {
        $this->resourceFilters = new ResourceFilterBuilder();
    }

    /**
     * by using this, all other conditions set by using other methods will be ignored
     */
    public function setRawRequest(array $request): self
    {
        $this->inHeaderQuery = false;
        $this->rawRequest = $request;

        return $this;
    }

    public function page(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function sort(string $by, string $dir = 'DESC'): self
    {
        $this->sort[] = ['by' => $by, 'dir' => $dir];

        return $this;
    }

    public function equals(string $column, $is): self
    {
        $this->resourceFilters->equals($column, $is);

        return $this;
    }

    public function from(string $column, string $from): self
    {
        $this->resourceFilters->from($column, $from);

        return $this;
    }

    public function to(string $column, string $to): self
    {
        $this->resourceFilters->to($column, $to);

        return $this;
    }

    public function between(string $column, string $from, string $to): self
    {
        $this->resourceFilters->between($column, $from, $to);
        return $this;
    }

    /**
     * @param string|null $cursor use '1' for 1st page, null for non cursor pagination
     */
    public function simplePaginate(?string $cursor = null): self
    {
        $this->simplePaginate = 1;
        $this->cursor = $cursor;

        return $this;
    }

    public function withRelation(string $relation): self
    {
        $this->withRelations[] = $relation;

        return $this;
    }

    public function withRelations(array $relations): self
    {
        $this->withRelations = \array_merge(\array_values($relations), $this->withRelations);

        return $this;
    }

    public function addCountRelation(string $countRelation): self
    {
        $this->countRelations[] = $countRelation;

        return $this;
    }

    public function addCountRelations(array $countRelations): self
    {
        $this->countRelations = \array_merge(\array_values($countRelations), $this->countRelations);

        return $this;
    }

    public function addExistRelation(string $existRelation): self
    {
        $this->existRelations[] = $existRelation;

        return $this;
    }

    public function addExistRelations(array $existRelations): self
    {
        $this->existRelations = \array_merge(\array_values($existRelations), $this->existRelations);

        return $this;
    }

    public function getUrlQueryString(array $overrides = []): string
    {
        return \http_build_query(\array_merge(
            $this->getAllFilters(),
            $overrides
        ), '', '&', PHP_QUERY_RFC3986);
    }

    public function getAllFilters(): array
    {
        if ($this->rawRequest !== []) {
            return $this->rawRequest;
        }

        return \array_merge(
            $this->getFilters(),
            $this->sqlDebug ? ['sqlDebug' => 1] : [],
            $this->simplePaginate !== null ? ['simplePaginate' => $this->simplePaginate] : [],
            $this->cursor !== null ? ['cursor' => $this->cursor] : [],
        );
    }

    private function getFilters(): array
    {
        return \array_merge($this->sqlDebug ? ['sqlDebug' => 1] : [], [
            'page' => $this->page,
            'limit' => $this->limit,
            'sort' => $this->sort,
            'withRelations' => \array_values(\array_unique($this->withRelations)),
            'withRelationsCount' => \array_values(\array_unique($this->countRelations)),
            'withRelationsExistence' => \array_values(\array_unique($this->existRelations)),
        ], $this->resourceFilters->getFilters());
    }
}
