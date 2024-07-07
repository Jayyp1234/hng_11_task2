<?php
require ('../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class OrganisationTest extends TestCase
{
    private $baseUrl = 'https://sunshinedom.com.ng/'; // Change to your API base URL

    public function testUserCannotSeeOtherUserOrganisations()
    {
        $client = new GuzzleHttp\Client();
        
        // Assuming $userToken1 and $userToken2 are tokens for two different users
        $userToken1 = 'token_for_user_1';
        $userToken2 = 'token_for_user_2';

        $response = $client->get($this->baseUrl . '/api/organisations', [
            'headers' => [
                'Authorization' => 'Bearer ' . $userToken1
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $organisationsUser1 = $data['data']['organisations'];

        $response = $client->get($this->baseUrl . '/api/organisations', [
            'headers' => [
                'Authorization' => 'Bearer ' . $userToken2
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $organisationsUser2 = $data['data']['organisations'];

        foreach ($organisationsUser2 as $org) {
            $this->assertNotContains($org, $organisationsUser1);
        }
    }
}
