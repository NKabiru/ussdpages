<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UssdMainMenuTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'phone_number' => $this->sessionData['phoneNumber']
        ]);

        $this->withoutExceptionHandling();
    }

    /** @test */
    public function it_should_navigate_to_the_first_main_menu_option()
    {
        $this->ussdPost()
            ->ussdPost('1234');

        $this->post(route('display-ussd.index'), $this->sessionData + ['text' => '1234*1'])
            ->assertSeeText("Enter item name:");
    }

    /** @test */
    public function it_should_navigate_to_the_second_main_menu_option()
    {
        $this->ussdPost()
            ->ussdPost('1234');

        $this->post(route('display-ussd.index'), $this->sessionData + ['text' => '1234*2'])
            ->assertSeeText("Enter name of item to remove:");
    }

    /** @test */
    public function it_should_navigate_to_the_third_main_menu_option()
    {
        $this->ussdPost()
            ->ussdPost('1234');

        $this->post(route('display-ussd.index'), $this->sessionData + ['text' => '1234*3'])
            ->assertSeeText('Your Stored Items:');
    }

    /** @test */
    public function it_should_navigate_to_the_fourth_main_menu_option()
    {
        $this->ussdPost()
            ->ussdPost('1234');

        $this->post(route('display-ussd.index'), $this->sessionData + ['text' => '1234*4'])
            ->assertSeeText('We will send you an Item Report via SMS shortly.');
    }


}
