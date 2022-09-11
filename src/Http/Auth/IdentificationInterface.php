<?php

namespace KuznetsovVladimir\BlogApi\Http\Auth;

use KuznetsovVladimir\BlogApi\Blog\User;
use KuznetsovVladimir\BlogApi\Http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}