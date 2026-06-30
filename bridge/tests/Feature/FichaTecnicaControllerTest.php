<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class FichaTecnicaControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['bridge.token' => 'test-token']);
    }

    public function test_retorna_401_sem_token(): void
    {
        $response = $this->getJson('/api/ficha-tecnica/lote?lote=123&cod_peca=ABC');

        $response->assertStatus(401);
    }

    public function test_retorna_401_com_token_invalido(): void
    {
        $response = $this->withHeader('X-Bridge-Token', 'token-errado')
            ->getJson('/api/ficha-tecnica/lote?lote=123&cod_peca=ABC');

        $response->assertStatus(401);
    }

}
