<?php
$I = new AcceptanceTester($scenario);
AcceptanceTester::test_login($I);

$I->am('logged in user');
$I->wantTo('ensure that the company listing page loads without errors');
$I->lookForwardTo('seeing it load without errors');
$I->amOnPage('/admin/settings/companies');
$I->waitForElement('.table', 5); // secs
$I->seeNumberOfElements('tr', [0,30]);
$I->seeInTitle('Companies');
$I->see('Companies');
$I->seeInPageSource('admin/settings/companies/create');
$I->dontSee('Companies', '.page-header');
$I->see('Companies', 'h1.pull-left');

/* Create Form */
$I->wantTo('ensure that the create company form loads without errors');
$I->lookForwardTo('seeing it load without errors');
$I->click(['link' => 'Create New']);
$I->amOnPage('/admin/settings/companies/create');
$I->dontSee('Create Company', '.page-header');
$I->see('Create Company', 'h1.pull-left');
$I->dontSee('&lt;span class=&quot;');
