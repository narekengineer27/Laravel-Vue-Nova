<?php

namespace Tests\Feature\API\Authentication;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifySmsNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SmsResendTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_returns_a_message_if_sms_has_been_already_verified()
    {
        $this->passportActingAs(true);
   
        $result = $this->getJson(route('verification.sms.resend'))->json();
        
        $this->assertEquals('User has already verified sms.', $result);
    }

    /** @test */
    public function sms_resended()
    {
        Notification::fake();
    
        Notification::assertNothingSent();
    
        $user = $this->passportActingAs();
        
        $result = $this->getJson(route('verification.sms.resend'))->json();
        
        $this->assertEquals('Sms has been reseneded.', $result);
        
        Notification::assertSentTo(
            $user,
            VerifySmsNotification::class,
            function ($notification) use ($user) {
                return $notification->pin == $user->fresh()->verification_code;
            }
        );
    }
    
    public function passportActingAs($smsVerified = false, $pin = null)
    {
        return Passport::actingAs(factory(User::class)->create([
            'email' => null,
            'email_verified_at' => null,
            'verified' => $smsVerified,
            'verification_code' => $pin,
        ]));
    }
}
