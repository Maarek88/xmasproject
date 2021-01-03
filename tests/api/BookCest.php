<?php

namespace App\Tests;

use App\Entity\Book;
use Codeception\Util\HttpCode;

class BookCest
{
    private static $addBookParameters = [
        "title" => "book1",
        "summary" => "some summary 1",
        "releaseDate" => "2011-06-05",
        "category" =>
            [
                "name" => "category1"
            ],
        "tags" =>
            [
                [
                    "name" => "tag1"
                ],
                [
                    "name" => "tag2"
                ]
            ]
    ];

    public function _before(ApiTester $I)
    {
    }

    public function addBook(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendPost('/book', self::$addBookParameters);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(["title" => "book1"]);
        $I->seeInRepository(Book::class, ["title" => "book1"]);
    }

    public function addBookEmptyParams(ApiTester $I)
    {
        $params = [];

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendPost('/book', $params);
        $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
        $I->seeResponseIsJson();
    }

    public function getAllBooks(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendGet('/book');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function getOneBook(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendPost('/book', self::$addBookParameters);
        list($uuid) = $I->grabDataFromResponseByJsonPath('$.uuid');

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendGet('/book/'.$uuid);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function getOneBookWithIncorrectUuid(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendGet('/book/incorrectuuid');
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }
}
