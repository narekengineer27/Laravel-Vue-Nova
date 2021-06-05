<?php

namespace Tests\Feature\API\Authentication;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use App\Events\SmsCodeVerified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifySmsNotification;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerificationSmsTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function send_verification_email()
    {
        Notification::fake();
       
        Notification::assertNothingSent();
       
        $user = make(User::class, [
            'email' => null,
            'email_verified_at' => null
        ]);

        $this->json('POST', '/api/v1/register', $user->toArray());
       
        Notification::assertSentTo(
            $user = User::first(),
            VerifySmsNotification::class,
            function ($notification) use ($user) {
                return $notification->pin == $user->verification_code;
            }
        );
    }

    /** @test */
    public function unauthenticated_sms_verification_fails()
    {
        $this->expectException(AuthenticationException::class);
        
        $this->post(route('verification.sms', 1));
    }
    
    /** @test */
    public function unauthorized_sms_verification_fails()
    {
        $this->expectException(AuthorizationException::class);
        
        $this->passportActingAs();
        
        $this->post(route('verification.sms', 2));
    }
    
    /** @test */
    public function it_returns_a_message_if_code_has_been_already_verified()
    {
        $this->passportActingAs(true);
        
        $this->postJson(route('verification.sms', 1))
            ->assertJson([
                'verification' => 'Verification code has been already verified.'
            ]);
    }

    /** @test */
    public function it_fails_when_verification_code_is_wrong()
    {
        $this->passportActingAs(false, 1);

        $this->postJson(route('verification.sms', [
                'id' => 1,
                'verification_code' => 2
            ]))->assertJson([
                'verification' => 'Wrong verification code.'
            ]);
    }
        
    /** @test */
    public function sms_verified()
    {
        Event::fake();
        
        $user = $this->passportActingAs(false, 1);

        $this->assertFalse($user->verified);
        
        $this->postJson(route('verification.sms', [
                'id' => 1,
                'verification_code' => $user->verification_code
            ]))->assertJson([
               'verification' => true
            ]);

        Event::assertDispatched(SmsCodeVerified::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });

        $this->assertDatabaseHas('users', [
            'verified' => true,
            'verification_code' => $user->verification_code
        ]);
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
