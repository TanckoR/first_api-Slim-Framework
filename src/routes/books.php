<?php
/**
 * Created by PhpStorm.
 * User: ermolaev
 * Date: 15.12.2020
 * Time: 15:51
 */

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//grouping routes version v1
$app->group('/v1', function() use ($app){
//get list of books
    $app->get('/books', function (Request $request, Response $response) {
        $db = new DB();

        try {
            $db = $db->connect();

            $books = $db->query("Select * from book")->fetchAll(PDO::FETCH_OBJ);


            return $response->withStatus(200)->withHeader("Content-Type", 'application/json')->withJson($books);



        }catch (PDOException $e){
            return $response->withJson(
                array(
                    "error" => array(
                        "text" => $e->getMessage(),
                        "code" =>$e->getCode(),
                    )
                )
            );
        }
        $db = null;
    });

//get book
    $app->get('/book/{id}', function (Request $request, Response $response) {

        $id = $request->getAttribute('id');

        $db = new DB();
        try {
            $db = $db->connect();

            $books = $db->query("Select * from book where id = $id")->fetch(PDO::FETCH_OBJ);


            return $response->withStatus(200)->withHeader("Content-Type", 'application/json')->withJson($books);

        }catch (PDOException $e){
            return $response->withJson(
                array(
                    "error" => array(
                        "text" => $e->getMessage(),
                        "code" =>$e->getCode(),
                    )
                )
            );
        }
        $db = null;
    });

//adding new book
    $app->post('/book/add', function (Request $request, Response $response) {

        $bookName = $request->getParam('name');

        $db = new DB();
        try {
            $db = $db->connect();

            $statement = "INSERT INTO book (name) VALUE (:bookName)";

            $prepeare = $db->prepare($statement);

            $prepeare->bindParam('bookName',$bookName);

            $book = $prepeare->execute();

            if($book){
                return $response->withStatus(200)->withHeader("Content-Type", 'application/json')->withJson(
                    array(
                        "text" => "Book was successfully added",
                    )
                );
            }else{
                return $response->withStatus(500)->withHeader("Content-Type", 'application/json')->withJson(array(
                    "error" => array(
                        "text" => "Find some errors when script was running",
                    )
                ));
            }





        }catch (PDOException $e){
            return $response->withJson(
                array(
                    "error" => array(
                        "text" => $e->getMessage(),
                        "code" =>$e->getCode(),
                    )
                )
            );
        }
        $db = null;
    });

//update book
    $app->put('/book/update/{id}', function (Request $request, Response $response) {

        $id = $request->getAttribute('id');

        if($id){
            $bookName = $request->getParam('name');

            $db = new DB();
            try {
                $db = $db->connect();

                $statement = "UPDATE book Set name = :bookName WHERE id=$id";

                $prepeare = $db->prepare($statement);

                $prepeare->bindParam('bookName',$bookName);

                $book = $prepeare->execute();

                if($book){
                    return $response->withStatus(200)->withHeader("Content-Type", 'application/json')->withJson(
                        array(
                            "text" => "Book with id=$id was successfully updated",
                        )
                    );
                }else{
                    return $response->withStatus(500)->withHeader("Content-Type", 'application/json')->withJson(array(
                        "error" => array(
                            "text" => "Find some errors when script was running",
                        )
                    ));
                }
            }catch (PDOException $e){
                return $response->withJson(
                    array(
                        "error" => array(
                            "text" => $e->getMessage(),
                            "code" =>$e->getCode(),
                        )
                    )
                );
            }


            $db = null;
        }else{

            return $response->withStatus(500)->withHeader("Content-Type", 'application/json')->withJson(array(
                "error" => array(
                    "text" => "Id param was missed",
                )
            ));

        }










    });

//delete book
    $app->delete('/book/{id}', function (Request $request, Response $response) {

        $id = $request->getAttribute('id');


        $db = new DB();
        try {
            $db = $db->connect();

            $statement = "Delete from book WHERE id=:id";

            $prepeare = $db->prepare($statement);
            $prepeare->bindParam('id',$id);
            $book = $prepeare->execute();

            if($book){
                return $response->withStatus(200)->withHeader("Content-Type", 'application/json')->withJson(
                    array(
                        "text" => "Book with id=$id was successfully deleted",
                    )
                );
            }else{
                return $response->withStatus(500)->withHeader("Content-Type", 'application/json')->withJson(array(
                    "error" => array(
                        "text" => "Find some errors when script was running",
                    )
                ));
            }
        }catch (PDOException $e){
            return $response->withJson(
                array(
                    "error" => array(
                        "text" => $e->getMessage(),
                        "code" =>$e->getCode(),
                    )
                )
            );
        }


        $db = null;

    });
});

