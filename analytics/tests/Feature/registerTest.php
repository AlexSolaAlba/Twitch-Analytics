<?php

declare(strict_types=1);

namespace tests\Feature;

use PHPUnit\Framework\TestCase;

class RegisterTest extends TestCase
{
    /**
     * @test
     */
    public function givenWrongEmailReturnsError(): void
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://vyvbts.com/register");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $email = json_encode([
            "email" => "XXX"
        ]);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $email);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($email)
        ]);

        $httpResponse = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $httpResponseData = json_decode($httpResponse, true);
        $expectedResponseData = ["error" => "The email must be a valid email address"];

        $this->assertEquals(400, $httpCode);
        $this->assertEquals($expectedResponseData, $httpResponseData);
    }
    /**
     * @test
     */
    public function notGivenAnEmailReturnsError(): void
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://vyvbts.com/register");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $httpResponse = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $httpResponseData = json_decode($httpResponse, true);
        $expectedResponseData = ["error" => "The email is mandatory"];

        $this->assertEquals(400, $httpCode);
        $this->assertEquals($expectedResponseData, $httpResponseData);
    }
    /**
     * @test
     */
    public function givenValidEmailReturnsApiKey(): void
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://vyvbts.com/register");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $email = json_encode([
            "email" => "holaquetal@gmail.com"
        ]);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $email);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($email)
        ]);

        $httpResponse = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $httpResponseData = json_decode($httpResponse, true);
        //$expectedResponseData = ["api_key" => ""];

        $this->assertEquals(200, $httpCode);
        $this->assertArrayHasKey("api_key", $httpResponseData);
        //$this->assertEquals($expectedResponseData, $httpResponseData);
    }
}
