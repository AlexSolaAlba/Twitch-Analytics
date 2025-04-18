<?php

namespace Feature;

use PHPUnit\Framework\TestCase;

class StreamsTest extends TestCase
{
    /**
     * @test
     */
    public function givenWrongTokenReturnsError(): void
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://vyvbts.com/analytics/streams");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer 213423424234'
        ]);

        $httpResponse = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $httpResponseData = json_decode($httpResponse, true);
        $expectedResponseData = ["error" => "Unauthorized. Token is invalid or expired."];

        $this->assertEquals(401, $httpCode);
        $this->assertEquals($expectedResponseData, $httpResponseData);
    }
}
