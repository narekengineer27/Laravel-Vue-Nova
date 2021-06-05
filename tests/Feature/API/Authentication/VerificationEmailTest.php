<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\User;
use App\Events\EmailVerified;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use App\Http\Middleware\ValidateSignature;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Notification;
use App\Exceptions\ExpiredSignatureException;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class VerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function send_verification_email()
    {
        Notification::fake();
    
        Notification::assertNothingSent();
    
        $user = make(User::class, [
            'email_verified_at' => null,
            'verified'=> false,
        ]);
    
        $this->json('POST', '/api/v1/register', $user->toArray());
    
        Notification::assertSentTo(
            User::first(),
            VerifyEmailNotification::class
        );
    }

    /** @test */
    public function signed_middleware_with_invalid_signature_fails()
    {
        $this->expectException(InvalidSignatureException::class);
        
        $this->hit('get', 1, 'invalid', 1);
    }
    
    
    /** @test */
    public function signed_middleware_with_expired_signature_fails()
    {
        $this->expectException(ExpiredSignatureException::class);
        
        $this->hit('get', 1, 'valid', -1);
    }
    
    /** @test */
    public function unauthenticated_email_verification_fails()
    {
        $this->withoutMiddleware(ValidateSignature::class)
            ->expectException(AuthenticationException::class);
            
        $this->hit('get', 1, 'valid', 1);
    }
        
    /** @test */
    public function unauthorized_email_verification_fails()
    {
        $this->withoutMiddleware(ValidateSignature::class)
            ->expectException(AuthorizationException::class);
        
        $this->passportActingAs();
        
        $this->hit('get', 2, 'valid', 1);
    }
    
    /** @test */
    public function its_returns_a_message_if_email_has_been_already_verified()
    {
        $this->withoutMiddleware(ValidateSignature::class);
        
        $this->passportActingAs(now());
        
        $this->hit('getJson', 1, 'valid', 1)
            ->assertJson([
                'verification' => 'Email has been already verified.'
            ]);
    }
    
    /** @test */
    public function email_verified()
    {
        Event::fake();

        $this->withoutMiddleware(ValidateSignature::class);

        $user = $this->passportActingAs();

        $this->assertNull($user->email_verified_at);
        
        $this->hit('getJson', 1, 'valid', 1)
            ->assertJson([
                'verification' => true
            ]);

        Event::assertDispatched(EmailVerified::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });

        $this->assertDatabaseHas('users', ['verified' => true]);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function passportActingAs($emailVerifiedAt = null)
    {
        return Passport::actingAs(factory(User::class)->create([
            'email_verified_at' => $emailVerifiedAt,
            'verified'=> false,
        ]));
    }

    public function hit($method, $id, $signature, $expires)
    {
        return $this->{$method}(route('verification.email', [
                'id' => $id,
                'signature' => $signature,
                'expires'=> now()->addMinute($expires)->getTimestamp()
            ]));
    }
}
