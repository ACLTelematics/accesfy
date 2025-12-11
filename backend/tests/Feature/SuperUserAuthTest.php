<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\SuperUser;
use Illuminate\Support\Facades\Hash;

class SuperUserAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_user_can_login_with_valid_credentials()
    {
        // Crear un SuperUser
        $superUser = SuperUser::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'active' => true,
        ]);

        // Intentar login
        $response = $this->postJson('/api/auth/super-user/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        // Verificar respuesta exitosa
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user',
                     'token',
                     'token_type',
                 ]);
    }

    public function test_super_user_cannot_login_with_invalid_credentials()
    {
        // Crear un SuperUser
        SuperUser::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'active' => true,
        ]);

        // Intentar login con credenciales incorrectas
        $response = $this->postJson('/api/auth/super-user/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        // Verificar error
        $response->assertStatus(422);
    }

    public function test_inactive_super_user_cannot_login()
    {
        // Crear un SuperUser inactivo
        SuperUser::create([
            'name' => 'Admin Inactive',
            'email' => 'inactive@test.com',
            'password' => Hash::make('password123'),
            'active' => false,
        ]);

        // Intentar login
        $response = $this->postJson('/api/auth/super-user/login', [
            'email' => 'inactive@test.com',
            'password' => 'password123',
        ]);

        // Verificar error
        $response->assertStatus(422);
    }
}