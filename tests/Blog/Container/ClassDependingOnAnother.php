<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Blog\Container;

class ClassDependingOnAnother
{
    public function __construct(
        private SomeClassWithoutDependencies $one,
        private SomeClassWithParameter       $two,
    )
    {
    }
}