<?php

namespace KuznetsovVladimir\BlogApi\Blog\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends AppException implements NotFoundExceptionInterface
{

}