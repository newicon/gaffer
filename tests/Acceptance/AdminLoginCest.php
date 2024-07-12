<?php


namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AdminLoginCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage('/admin');
        $I->fillField("Email", "dougall.winship@gmail.com");
        $I->fillField("Password", "dougall");
        $I->click("Login");
    }
}
