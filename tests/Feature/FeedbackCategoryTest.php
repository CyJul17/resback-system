<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_form_shows_the_required_categories_in_the_requested_order(): void
    {
        $this->seed(CategorySeeder::class);
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('feedback.create'));

        $response->assertOk()
            ->assertSee('Department or Campus Area')
            ->assertSeeInOrder(Category::FEEDBACK_CATEGORIES);

        $this->assertSame(count(Category::FEEDBACK_CATEGORIES), Category::active()->count());
    }

    public function test_feedback_submission_requires_an_active_category(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $inactiveCategory = Category::create([
            'name' => 'Inactive Department',
            'slug' => 'inactive-department',
            'is_active' => false,
        ]);

        $this->actingAs($student)
            ->post(route('feedback.store'), ['content' => 'This feedback has no selected department.'])
            ->assertSessionHasErrors('category_id');

        $this->actingAs($student)
            ->post(route('feedback.store'), [
                'category_id' => $inactiveCategory->id,
                'content' => 'This feedback uses an inactive department.',
            ])
            ->assertSessionHasErrors('category_id');
    }
}
