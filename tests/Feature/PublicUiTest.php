<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\House;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_renders_with_hero_and_featured_cabins(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('NORDIC HUTS');
        $response->assertSee('Отдых наедине с тишиной карельского леса');
        $response->assertSee('Быстрый вход для преподавателя в 1 клик');
        $response->assertSee('Популярные домики');
        $response->assertSee('100% приватность');
        $response->assertSee('Банный чан на дровах');
    }

    public function test_catalog_page_renders_and_filters(): void
    {
        $response = $this->get(route('catalog'));

        $response->assertStatus(200);
        $response->assertSee('Коллекция уединенных домиков');
        $response->assertSee('Категория');
        $response->assertSee('Количество гостей');
        $response->assertSee('Найдено домиков');

        // Test filtering by capacity
        $filterResponse = $this->get(route('catalog', ['capacity' => 6]));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Лесное Шале «Медвежий угол»');
    }

    public function test_house_detail_page_renders_with_booking_form(): void
    {
        $house = House::first();

        $response = $this->get(route('houses.show', $house->slug));

        $response->assertStatus(200);
        $response->assertSee($house->name);
        $response->assertSee('Стоимость проживания');
        $response->assertSee('Все удобства и оснащение');
        $response->assertSee('Заезд (с 15:00)');
        $response->assertSee('Выезд (до 12:00)');
    }

    public function test_auth_pages_render_with_teacher_demo_shortcuts(): void
    {
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Вход в личный кабинет');
        $loginResponse->assertSee('Быстрый вход для преподавателя (1 клик)');

        $registerResponse = $this->get(route('register'));
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('Создание аккаунта');
    }

    public function test_teacher_quick_login_roles_redirection(): void
    {
        // 1. Client login
        $clientResponse = $this->get(route('quick-login', 'client'));
        $clientResponse->assertRedirect(route('client.bookings'));
        $this->assertAuthenticated();
        $this->assertEquals('client', auth()->user()->role);

        // 2. Manager login
        $managerResponse = $this->get(route('quick-login', 'manager'));
        $managerResponse->assertRedirect(route('manager.index'));
        $this->assertEquals('manager', auth()->user()->role);

        // 3. Admin login
        $adminResponse = $this->get(route('quick-login', 'admin'));
        $adminResponse->assertRedirect(route('admin.index'));
        $this->assertEquals('admin', auth()->user()->role);

        // 4. Guest logout
        $guestResponse = $this->get(route('quick-login', 'guest'));
        $guestResponse->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
