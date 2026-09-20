<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_faculty_can_view_dashboard_and_export_action(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);

        $this->actingAs($faculty)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Export Feedbacks (.xlsx)')
            ->assertSee('Quick Actions')
            ->assertSee('View Feedback Form');
    }

    public function test_admin_dashboard_does_not_show_feedback_quick_actions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Quick Actions')
            ->assertDontSee('View Feedback Form')
            ->assertSee('Manage Accounts');

        $this->actingAs($admin)->get(route('feedback.create'))->assertForbidden();
        $this->actingAs($admin)->post(route('feedback.store'))->assertForbidden();
    }

    public function test_default_dashboard_shows_only_ten_mixed_feedbacks(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $ccis = Category::create(['name' => 'CCIS', 'slug' => 'ccis', 'is_active' => true]);
        $cas = Category::create(['name' => 'CAS', 'slug' => 'cas', 'is_active' => true]);

        foreach (range(1, 12) as $number) {
            Feedback::create([
                'category_id' => $number % 2 ? $ccis->id : $cas->id,
                'content' => "Mixed dashboard feedback {$number}",
                'status' => 'pending',
            ]);
        }

        $this->actingAs($faculty)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('All categories and languages (mixed)')
            ->assertViewHas('totalFeedbacks', 12)
            ->assertViewHas('recentFeedbacks', fn ($feedbacks) => $feedbacks->count() === 10);
    }

    public function test_category_selection_filters_every_dashboard_result(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $ccis = Category::create(['name' => 'CCIS', 'slug' => 'ccis', 'is_active' => true]);
        $cas = Category::create(['name' => 'CAS', 'slug' => 'cas', 'is_active' => true]);

        foreach (range(1, 12) as $number) {
            Feedback::create([
                'category_id' => $ccis->id,
                'content' => "CCIS-only feedback {$number}",
                'status' => 'pending',
            ]);
        }

        Feedback::create([
            'category_id' => $cas->id,
            'content' => 'CAS feedback must not appear',
            'status' => 'pending',
        ]);

        $this->actingAs($faculty)
            ->get(route('dashboard', ['category_id' => $ccis->id]))
            ->assertOk()
            ->assertSee('Only CCIS')
            ->assertDontSee('CAS feedback must not appear')
            ->assertViewHas('totalFeedbacks', 12)
            ->assertViewHas('selectedCategory', fn ($category) => $category->is($ccis))
            ->assertViewHas('recentFeedbacks', fn ($feedbacks) =>
                $feedbacks->total() === 12
                && $feedbacks->count() === 10
                && collect($feedbacks->items())->every(fn ($feedback) => $feedback->category_id === $ccis->id)
            );
    }

    public function test_category_and_language_filters_can_be_combined(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $ccis = Category::create(['name' => 'CCIS', 'slug' => 'ccis', 'is_active' => true]);
        $cas = Category::create(['name' => 'CAS', 'slug' => 'cas', 'is_active' => true]);

        foreach (range(1, 11) as $number) {
            $feedback = Feedback::create([
                'category_id' => $ccis->id,
                'content' => "CCIS Taglish feedback {$number}",
                'status' => 'analyzed',
            ]);
            $feedback->sentimentResult()->create([
                'sentiment' => 'positive',
                'confidence' => .9,
                'language_category' => 'Taglish',
            ]);
        }

        $englishFeedback = Feedback::create([
            'category_id' => $ccis->id,
            'content' => 'CCIS English feedback must not appear',
            'status' => 'analyzed',
        ]);
        $englishFeedback->sentimentResult()->create([
            'sentiment' => 'neutral',
            'confidence' => .8,
            'language_category' => 'English',
        ]);

        $otherCategoryFeedback = Feedback::create([
            'category_id' => $cas->id,
            'content' => 'CAS Taglish feedback must not appear',
            'status' => 'analyzed',
        ]);
        $otherCategoryFeedback->sentimentResult()->create([
            'sentiment' => 'negative',
            'confidence' => .8,
            'language_category' => 'Taglish',
        ]);

        $this->actingAs($faculty)
            ->get(route('dashboard', [
                'category_id' => $ccis->id,
                'language_category' => 'Taglish',
            ]))
            ->assertOk()
            ->assertSee('CCIS · Taglish')
            ->assertDontSee('CCIS English feedback must not appear')
            ->assertDontSee('CAS Taglish feedback must not appear')
            ->assertViewHas('totalFeedbacks', 11)
            ->assertViewHas('sentimentData', [
                'positive' => 11,
                'neutral' => 0,
                'negative' => 0,
            ])
            ->assertViewHas('recentFeedbacks', fn ($feedbacks) =>
                $feedbacks->total() === 11 && $feedbacks->count() === 10
            );
    }

    public function test_long_feedback_can_be_expanded_to_read_the_complete_message(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $category = Category::create(['name' => 'CCIS', 'slug' => 'ccis', 'is_active' => true]);
        $longFeedback = str_repeat('This is an important and detailed student concern. ', 8).'Complete ending marker.';

        Feedback::create([
            'category_id' => $category->id,
            'content' => $longFeedback,
            'status' => 'pending',
        ]);

        $this->actingAs($faculty)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Read full feedback')
            ->assertSee($longFeedback);
    }
}
