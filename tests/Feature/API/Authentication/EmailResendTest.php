<?php

namespace Tests\Feature\API\Authentication;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailResendTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_returns_a_message_if_email_has_been_already_verified()
    {
        $this->passportActingAs(now());

        $result = $this->getJson(route('verification.email.resend'))->json();
        
        $this->assertEquals('User has already verified email.', $result);
    }
    
    /** @test */
    public function email_resended()
    {
        Notification::fake();
    
        Notification::assertNothingSent();
    
        $user = $this->passportActingAs();
        
        $result = $this->getJson(route('verification.email.resend'))->json();
        
        $this->assertEquals('Email has been reseneded.', $result);

        Notification::assertSentTo(
            $user,
            VerifyEmailNotification::class
        );
    }
        
    public function passportActingAs($emailVerifiedAt = null)
    {
        return Passport::actingAs(factory(User::class)->create([
            'email_verified_at' => $emailVerifiedAt,
            'verified'=> false,
            ]));
    }
}
