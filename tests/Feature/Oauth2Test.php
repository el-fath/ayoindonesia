<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class Oauth2Test extends TestCase
{

    public function testOauth2AuthenticationSuccessfully()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertStatus(200);

        $user->delete();
    }

    /**
     * @dataProvider validationDataProvider
     */
    public function testValidation(array $invalidData)
    {
        $user = User::factory()->create();

        $validData = [
            'email' => $user->email,
            'password' => 'password',
        ];
        $data = array_merge($validData, $invalidData);

        $response = $this->postJson('/api/login', $data);
        $response->assertStatus(422);

        $user->delete();
    }

    public function validationDataProvider()
    {
        return [
            [['email' => null], 'email'],
            [['email' => ''], 'email'],
            [['email' => 'wrong_mail_format'], 'email'],
            [['email' => 'mailnotfound@mail.com'], 'email'],
            [['email' => []], 'email'],
            [['password' => 'wrong_password'], 'password'],
            [['password' => null], 'password'],
            [['password' => ''], 'password'],
            [['password' => []], 'password'],
        ];
    }
}
