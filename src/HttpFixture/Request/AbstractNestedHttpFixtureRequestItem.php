<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request;

abstract class AbstractNestedHttpFixtureRequestItem implements HttpFixtureRequestItemInterface
{
    /**
     * @var HttpFixtureRequestItemInterface[]
     */
    protected $items;

    /**
     * @param HttpFixtureRequestItemInterface ...$items
     */
    public function __construct(...$items)
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    private function addItem(HttpFixtureRequestItemInterface $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return HttpFixtureRequestItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function __invoke($value): bool
    {
        foreach ($this->items as $item) {
            if (!$item->__invoke($value)) {
                return false;
            }
        }

        return true;
    }
}
