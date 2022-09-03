<?php

namespace KuznetsovVladimir\BlogApi\Http\Actions;

use KuznetsovVladimir\BlogApi\Http\Request;
use KuznetsovVladimir\BlogApi\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}