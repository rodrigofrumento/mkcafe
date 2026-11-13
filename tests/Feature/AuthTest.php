<?php

namespace Tests\Feature;

use App\Mail\UserRegisteredEmail;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_pages_render(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_registration_creates_customer_and_ignores_role_field(): void
    {
        Mail::fake();

        $this->post(route('register'), [
            'name' => 'Maria',
            'email' => 'maria@example.com',
            'password' => 'senha-segura-123',
            'password_confirmation' => 'senha-segura-123',
            'role' => User::ROLE_OWNER,
        ])->assertRedirect(route('home'));

        $user = User::where('email', 'maria@example.com')->sole();
        $this->assertSame(User::ROLE_USER, $user->fresh()->role);
        $this->assertAuthenticatedAs($user);
        Mail::assertSent(UserRegisteredEmail::class, fn ($mail) => $mail->hasTo('maria@example.com'));
    }

    public function test_owner_is_redirected_to_admin_after_login(): void
    {
        $owner = Store::factory()->create()->user;

        $this->post(route('login'), ['email' => $owner->email, 'password' => 'password'])
            ->assertRedirect(route('admin.stores.index'));
    }

    public function test_customer_is_redirected_home_after_login(): void
    {
        $customer = User::factory()->create();

        $this->post(route('login'), ['email' => $customer->email, 'password' => 'password'])
            ->assertRedirect(route('home'));
    }

    public function test_wrong_password_fails(): void
    {
        $customer = User::factory()->create();

        $this->post(route('login'), ['email' => $customer->email, 'password' => 'errada'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
