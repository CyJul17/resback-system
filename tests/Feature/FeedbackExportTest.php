<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class FeedbackExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_faculty_can_export_classified_feedback_as_xlsx(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $category = Category::create([
            'name' => 'Facilities',
            'slug' => 'facilities',
            'is_active' => true,
        ]);

        $feedback = Feedback::create([
            'category_id' => $category->id,
            'content' => '=HYPERLINK("https://example.test", "formula injection")',
            'status' => 'analyzed',
        ]);

        $feedback->sentimentResult()->create([
            'sentiment' => 'negative',
            'confidence' => 0.94,
            'keywords' => ['wifi', 'slow'],
            'detected_languages' => ['English', 'Ilocano'],
            'language_category' => 'Iloclish',
            'language_confidence' => 0.91,
        ]);

        $response = $this->actingAs($faculty)->get(route('feedback.export'));

        $response->assertOk();
        $response->assertDownload();

        $temporaryFile = tempnam(sys_get_temp_dir(), 'resback-export-');
        file_put_contents($temporaryFile, $response->streamedContent());

        try {
            $spreadsheet = IOFactory::load($temporaryFile);
            $sheet = $spreadsheet->getSheetByName('Feedbacks');

            $this->assertNotNull($sheet);
            $this->assertSame('Submission Date', $sheet->getCell('B1')->getValue());
            $this->assertSame('Facilities', $sheet->getCell('C2')->getValue());
            $this->assertSame($feedback->content, $sheet->getCell('D2')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('D2')->getDataType());
            $this->assertSame('Negative', $sheet->getCell('E2')->getValue());
            $this->assertSame('Iloclish', $sheet->getCell('G2')->getValue());
            $this->assertSame('English, Ilocano', $sheet->getCell('H2')->getValue());
            $this->assertSame('wifi, slow', $sheet->getCell('J2')->getValue());
            $this->assertSame('Analyzed', $sheet->getCell('K2')->getValue());
            $this->assertSame('A2', $sheet->getFreezePane());
            $this->assertSame('A1:K2', $sheet->getAutoFilter()->getRange());
            $this->assertSame('3730A3', $sheet->getStyle('A1')->getFill()->getStartColor()->getRGB());
            $this->assertSame('yyyy-mm-dd hh:mm', $sheet->getStyle('B2')->getNumberFormat()->getFormatCode());
            $this->assertSame(60.0, $sheet->getColumnDimension('D')->getWidth());
        } finally {
            @unlink($temporaryFile);
        }
    }

    public function test_guests_cannot_export_feedback(): void
    {
        $this->get(route('feedback.export'))->assertRedirect(route('login'));
    }
}
