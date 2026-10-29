<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// CRUD kategori oleh admin (Route::resource)
class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        return User::where('email', 'admin@binus.edu')->first();
    }

    private function data(array $override = []): array
    {
        return array_merge([
            'name' => 'Wi-Fi & Jaringan',
            'description' => 'Internet kampus lambat, putus, atau tidak bisa login.',
            'icon' => 'bi-wifi',
        ], $override);
    }

    public function test_only_admin_can_open_category_pages(): void
    {
        $this->actingAs(User::where('email', 'petugas@binus.edu')->first())
            ->get(route('admin.categories.index'))->assertForbidden();

        $this->actingAs($this->admin())->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Fasilitas Rusak');
    }

    public function test_admin_can_create_category_and_it_appears_in_report_form(): void
    {
        $this->actingAs($this->admin())->post(route('admin.categories.store'), $this->data())
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Wi-Fi & Jaringan', 'slug' => 'wi-fi-jaringan', 'is_sensitive' => false]);

        $this->actingAs(User::where('email', 'mahasiswa@binus.ac.id')->first())
            ->get(route('reports.create'))
            ->assertSee('Wi-Fi &amp; Jaringan', false);
    }

    public function test_category_name_must_be_unique_and_icon_must_be_from_the_list(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.categories.store'), $this->data(['name' => 'Fasilitas Rusak', 'icon' => 'bi-bogus']))
            ->assertSessionHasErrors(['name', 'icon']);
    }

    public function test_admin_can_update_category_and_keep_its_own_name(): void
    {
        $category = Category::where('slug', 'fasilitas-rusak')->first();

        // Nama sama dengan miliknya sendiri tidak dianggap duplikat (Rule::unique()->ignore())
        $this->actingAs($this->admin())
            ->put(route('admin.categories.update', $category), [
                'name' => 'Fasilitas Rusak',
                'description' => 'Deskripsi baru.',
                'icon' => 'bi-tools',
                'is_sensitive' => '1',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $category->refresh();
        $this->assertSame('Deskripsi baru.', $category->description);
        $this->assertSame('bi-tools', $category->icon);
        $this->assertTrue($category->is_sensitive);
    }

    public function test_unused_category_can_be_deleted_but_used_one_cannot(): void
    {
        $unused = Category::create($this->data(['slug' => 'wi-fi-jaringan']));

        $this->actingAs($this->admin())->delete(route('admin.categories.destroy', $unused))
            ->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $unused->id]);

        // "Fasilitas Rusak" dipakai BN-2481 dan BN-2388
        $used = Category::where('slug', 'fasilitas-rusak')->first();
        $this->actingAs($this->admin())->delete(route('admin.categories.destroy', $used))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $used->id]);
    }
}
