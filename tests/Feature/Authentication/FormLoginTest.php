<?php

declare(strict_types=1);

namespace App\Tests\Feature\Authentication;

use App\Tests\Feature\FeatureTestCase;

class FormLoginTest extends FeatureTestCase
{
    public function testLoginNormalUser(): void
    {
        $this->loginUser();

        $this->assertResponseRedirect();
        $this->assertSecurityRedirectLocation('index');

        $this->sendGet('index');

        $this->assertSessionSet();
    }

    public function testLoginAdminUser(): void
    {
        $this->loginAdmin();

        $this->assertResponseRedirect();
        $this->assertSecurityRedirectLocation('index');

        $this->sendGet('index');

        $this->assertSessionSet();
    }

    private function assertSessionSet(): void
    {
        $session = $this->getRequest()->getSession();

        $this->assertEquals($this->getSessionCookie()?->getValue(), $session->getId());
    }
}