<?php
require ('../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class AuthSpecTest extends TestCase
{
    private $baseUrl = 'https://sunshinedom.com.ng/'; // Change to your API base URL

    public function testRegisterUserSuccessfully()
    {
        $client = new GuzzleHttp\Client();
        $response = $client->post($this->baseUrl . '/auth/register', [
            'json' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => 'Password123!',
                'phone' => '1234567890'
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);

        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('accessToken', $data['data']);
        $this->assertEquals('John\'s Organisation', $data['data']['user']['organisationName']);
    }

    public function testLoginUserSuccessfully()
    {
        $client = new GuzzleHttp\Client();
        $response = $client->post($this->baseUrl . '/auth/login', [
            'json' => [
                'email' => 'john.doe@example.com',
                'password' => 'Password123!'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);

        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('accessToken', $data['data']);
    }

    public function testRegisterUserFailsIfRequiredFieldsMissing()
    {
        $client = new GuzzleHttp\Client();
        $response = $client->post($this->baseUrl . '/auth/register', [
            'json' => [
                'firstName' => '',
                'lastName' => '',
                'email' => 'john.doe@example.com',
                'password' => '',
                'phone' => '1234567890'
            ],
            'http_errors' => false
        ]);

        $this->assertEquals(422, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);

        $this->assertEquals('error', $data['status']);
        $this->assertEquals(422, $data['statusCode']);
    }

    public function testRegisterUserFailsIfDuplicateEmail()
    {
        $client = new GuzzleHttp\Client();
        $response = $client->post($this->baseUrl . '/auth/register', [
            'json' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => 'Password123!',
                'phone' => '1234567890'
            ],
            'http_errors' => false
        ]);

        $this->assertEquals(422, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);

        $this->assertEquals('error', $data['status']);
        $this->assertEquals(422, $data['statusCode']);
    }
}
