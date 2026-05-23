<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemsGroup;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Staff $admin;
    protected ItemsGroup $group;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->admin = Staff::where('email', 'admin@crm.com')->first();

        $this->group = ItemsGroup::create(['name' => 'Test Group']);
    }

    protected function authenticateAsAdmin(): void
    {
        $this->actingAs($this->admin, 'staff');
    }

    protected function createItem(array $overrides = []): Item
    {
        return Item::create(array_merge([
            'description' => 'Consulting Service',
            'rate' => 100.00,
            'unit' => 'hours',
            'group_id' => $this->group->id,
        ], $overrides));
    }

    public function test_guest_is_redirected_to_login_when_accessing_items(): void
    {
        $this->get(route('admin.items.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.items.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.items.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_items_list(): void
    {
        $this->authenticateAsAdmin();
        $this->createItem(['description' => 'Web Development']);

        $response = $this->get(route('admin.items.index'));
        $response->assertStatus(200);
        $response->assertSee('Web Development');
    }

    public function test_admin_can_create_item(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.items.store'), [
            'description' => 'Design Service',
            'long_description' => 'UI/UX design service',
            'rate' => 150.00,
            'unit' => 'hours',
            'group_id' => $this->group->id,
        ]);

        $response->assertRedirect(route('admin.items.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblitems', [
            'description' => 'Design Service',
            'rate' => 150.00,
        ]);
    }

    public function test_create_item_requires_required_fields(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.items.store'), []);
        $response->assertSessionHasErrors(['description', 'rate']);
    }

    public function test_admin_can_view_single_item(): void
    {
        $this->authenticateAsAdmin();
        $item = $this->createItem();

        $response = $this->get(route('admin.items.show', $item->id));
        $response->assertStatus(200);
        $response->assertSee('Consulting Service');
    }

    public function test_admin_can_update_item(): void
    {
        $this->authenticateAsAdmin();
        $item = $this->createItem();

        $response = $this->put(route('admin.items.update', $item->id), [
            'description' => 'Premium Consulting',
            'long_description' => 'Updated',
            'rate' => 200.00,
            'unit' => 'hours',
            'group_id' => $this->group->id,
        ]);

        $response->assertRedirect(route('admin.items.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblitems', [
            'id' => $item->id,
            'description' => 'Premium Consulting',
            'rate' => 200.00,
        ]);
    }

    public function test_admin_can_delete_item(): void
    {
        $this->authenticateAsAdmin();
        $item = $this->createItem();

        $response = $this->delete(route('admin.items.destroy', $item->id));

        $response->assertRedirect(route('admin.items.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblitems', ['id' => $item->id]);
    }

    public function test_filter_items_by_group(): void
    {
        $this->authenticateAsAdmin();
        $group2 = ItemsGroup::create(['name' => 'Second Group']);
        $this->createItem(['description' => 'Group 1 Item']);
        Item::create(['description' => 'Group 2 Item', 'rate' => 50.00, 'group_id' => $group2->id]);

        $response = $this->get(route('admin.items.index', ['group' => $group2->id]));
        $response->assertStatus(200);
        $response->assertSee('Group 2 Item');
        $response->assertDontSee('Group 1 Item');
    }

    // --- Items Group Tests ---

    public function test_guest_is_redirected_to_login_when_accessing_items_groups(): void
    {
        $this->get(route('admin.itemsgroups.index'))->assertRedirect(route('login.staff'));
        $this->get(route('admin.itemsgroups.create'))->assertRedirect(route('login.staff'));
        $this->post(route('admin.itemsgroups.store'), [])->assertRedirect(route('login.staff'));
    }

    public function test_admin_can_view_items_groups_list(): void
    {
        $this->authenticateAsAdmin();
        ItemsGroup::create(['name' => 'Hardware']);

        $response = $this->get(route('admin.itemsgroups.index'));
        $response->assertStatus(200);
        $response->assertSee('Hardware');
    }

    public function test_admin_can_create_items_group(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->post(route('admin.itemsgroups.store'), [
            'name' => 'Software',
        ]);

        $response->assertRedirect(route('admin.itemsgroups.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblitems_groups', [
            'name' => 'Software',
        ]);
    }

    public function test_admin_can_update_items_group(): void
    {
        $this->authenticateAsAdmin();
        $group = ItemsGroup::create(['name' => 'Old Name']);

        $response = $this->put(route('admin.itemsgroups.update', $group->id), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect(route('admin.itemsgroups.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tblitems_groups', [
            'id' => $group->id,
            'name' => 'New Name',
        ]);
    }

    public function test_admin_can_delete_items_group(): void
    {
        $this->authenticateAsAdmin();
        $group = ItemsGroup::create(['name' => 'Temporary']);

        $response = $this->delete(route('admin.itemsgroups.destroy', $group->id));

        $response->assertRedirect(route('admin.itemsgroups.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tblitems_groups', ['id' => $group->id]);
    }
}
