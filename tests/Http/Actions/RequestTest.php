<?php

namespace KuznetsovVladimir\BlogApi\Blog\UnitTests\Http\Actions;

use JsonException;
use KuznetsovVladimir\BlogApi\Blog\Exceptions\HttpException;
use KuznetsovVladimir\BlogApi\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testItReturnMethod()
    {
        $request = new Request([], ["REQUEST_METHOD" => "POST"], '');

        $value = $request->method();

        $this->assertEquals('POST', $value);
    }

    public function testItReturnExceptionIfMethodNotFound()
    {
        $request = new Request([], [], '');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Cannot get method from the request');

        $request->method();
    }

    public function testItReturnExceptionIfCannotDecode()
    {
        $request = new Request([], [], '');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("Cannot decode json body");

        $request->jsonBody();
    }

    public function testItReturnValueFromJson()
    {
        $request = new Request(
            [],
            [],
            '{
            "author_uuid": "38830eb6-d2cf-44f9-a7dd-5e7d634eac77"
            }'
        );

        $value = $request->jsonBodyField('author_uuid');

        $this->assertEquals('38830eb6-d2cf-44f9-a7dd-5e7d634eac77', $value);
    }

    public function testItReturnExceptionIfNoSuchField()
    {
        $request = new Request([], [], '{}');
        $field = 'author_uuid';

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("No such field: {$field}");

        $request->jsonBodyField($field);
    }

    public function testItReturnExceptionIfFieldEmpty()
    {
        $request = new Request([], [], '{"author_uuid": ""}');
        $field = 'author_uuid';

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("Empty field: $field");

        $request->jsonBodyField($field);
    }

    public function testItReturnPath()
    {
        $request = new Request([], ["REQUEST_URI" => "/posts/create"], '');

        $value = $request->path();

        $this->assertEquals('/posts/create', $value);
    }

    public function testItReturnExceptionIfPathNotFound()
    {
        $request = new Request([], [], '');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Cannot get path from the request');

        $request->path();
    }

//    public function testItReturnExceptionIfPathNotArray()
//    {
//        $request = new Request([], ["REQUEST_URI" =>""], '');
//
//        $this->expectException(HttpException::class);
//        $this->expectExceptionMessage('Cannot get path from the request');
//
//        $request->path();
//    }

    public function testItReturnValueFromParam()
    {
        $request = new Request(['username' => 'ivan'], [], '');

        $value = $request->query('username');

        $this->assertEquals('ivan', $value);
    }

    public function testItReturnExceptionIfNoSuchParam()
    {
        $request = new Request([], [], '');
        $param = 'username';

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("No such query param in the request: {$param}");

        $request->query($param);
    }

    public function testItReturnExceptionIParamEmpty()
    {
        $request = new Request(['username' => ''], [], '');
        $param = 'username';

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("Empty query param in the request: {$param}");

        $request->query($param);
    }


//    public function testItReturnExceptionIfDataNotArray()
//    {
//        $request = new Request([], [], '{}');
//
//        $this->expectException(HttpException::class);
//        $this->expectExceptionMessage("Not an array/object in json body");
//
//        $request->jsonBody();
//    }

}

