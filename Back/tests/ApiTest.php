<?php

namespace App\Tests;

use App\Entity\Product;
use Doctrine\ORM\Mapping\Id;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertIsArray;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotEmpty;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    private $product;

    public function testApiIndex(): void
    {
        $client = static::createClient();

        // Request a specific page
        $client->jsonRequest('GET', '/api/');
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "Hello world"], $responseData);
    }
    public function testProductSaved(): void
    {
       
        $this->product = new Product();
        $this->product->setName('Rick');
        $this->product->setPrice('8');
        $this->product->setQuantity('2');
        $this->product->setImage('https:\/\/rickandmortyapi.com\/api\/character\/avatar\/19.jpeg');
        $client = static::createClient();
        // Request a specific page
       $client->jsonRequest('POST', '/api/products', [
            'name' => $this->product->getName(),
            "price"=> $this->product->getPrice(),
            "quantity"=> $this->product->getQuantity(),
            "image"=> $this->product->getImage()
        ]
        );
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($this->product->getName(), $responseData['name']);
        $this->assertNotNull($this->product);
    }
    public function testGetProducts(): void
    {
        $client = static::createClient();
        // Request a specific page
        $client->jsonRequest('GET', '/api/products');
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        print_r($responseData);
        $this->assertNotNull($responseData);
    }

    public function getProducts(KernelBrowser $client)
    {
            // Request a specific page
            $client->jsonRequest('GET', '/api/products');
                    $response = $client->getResponse();
            $this->assertResponseIsSuccessful();
            $this->assertJson($response->getContent());
                    $responseData = json_decode($response->getContent(), true);
        // @todo faire un test qui vérifie que le produit ajouté avant sois ajouté via son nom
        //assertNotNull($responseData);
        return $responseData[0];
    }
    public function testGetProduct(){
        $client = static::createClient();
        $id = $this->getProducts($client)['id'];
        // Request a specific page
        $client->jsonRequest('GET', '/api/products/' . $id);
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($id, $responseData['id']);
       
    }
    
    /**
     * Undocumented function
     *
     * @return void
     */
    public function testCardToProductMany(): void
     {
         $client = static::createClient();
         // Request a specific page
         $id = $this->getProducts($client)['id'];
         $client->jsonRequest('POST', '/api/cart/' .$id,
             ['quantity' => 1000]
         );
         $response = $client->getResponse();
     
         $this->assertResponseIsSuccessful();
         $this->assertJson($response->getContent());
         $responseData = json_decode($response->getContent(), true);
         $this->assertEquals(["error" => "too many"], $responseData);
         print_r($responseData);
    
      }

    public function testCardToProduct(): void
    {
        $client = static::createClient();
        $id = $this->getProducts($client)['id'];
        // Request a specific page
        $client->jsonRequest('POST', '/api/cart/'.$id,
            ['quantity' => 2]
        );
        $response = $client->getResponse();
    
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent());
        print_r($responseData);
        $this->assertEquals($id, $responseData->products[0]->id);
        $this->assertEquals(1, count($responseData->products));

     }

    public function testCardGet(): void
    {
        $client = static::createClient();
        // Request a specific page
        $client->jsonRequest('GET', '/api/cart');
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        print_r($responseData);
        $this->assertNotEmpty($responseData);
        $this->assertNotNull($responseData);
    }


    public function testDeleteProductToCart(): void {
        $client = static::createClient();
        $id = $this->getProducts($client)['id'];
        // Request a specific page
        $client->jsonRequest('DELETE', '/api/cart/' . $id );
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent());
        print_r($responseData);
        $this->assertEquals(0, count($responseData->products));
      
    }
     public function testProductDelete(): void
    {
        $client = static::createClient();
        $id = $this->getProducts($client)['id'];
        // Request a specific page
        $client->jsonRequest('DELETE', '/api/products/' .$id);
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['delete' => "ok"], $responseData);
    }

  
    
    


}
