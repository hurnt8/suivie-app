<?php

use App\Models\User;

test('guests are redirected away from the admin panel', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('login'));
});

test('operators can access the dashboard but not user management', function () {
    $operator = User::factory()->create();

    $this->actingAs($operator);

    $this->get(route('admin.dashboard'))->assertOk();
    $this->get(route('admin.users.index'))->assertForbidden();
    $this->get(route('admin.settings.edit'))->assertForbidden();
});

test('admins can access every admin page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $this->get(route('admin.dashboard'))->assertOk();
    $this->get(route('admin.users.index'))->assertOk();
    $this->get(route('admin.settings.edit'))->assertOk();
});
