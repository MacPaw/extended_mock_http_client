<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\DataTransformer;

use ExtendedMockHttpClient\HttpFixture\Request\AbstractNestedHttpFixtureRequestItem;

abstract class AbstractNestedDataTransformerItem extends AbstractNestedHttpFixtureRequestItem implements
    DataTransformerInterface
{
}
