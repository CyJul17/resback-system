<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FeedbackExportController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('ResBack')
            ->setTitle('ResBack Feedback Export')
            ->setSubject('Student feedback with sentiment and language classifications');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Feedbacks');
        $sheet->setShowGridlines(false);

        $headers = [
            'Feedback ID',
            'Submission Date',
            'Campus Category',
            'Feedback',
            'Sentiment',
            'Sentiment Confidence',
            'Language Category',
            'Detected Languages',
            'Language Confidence',
            'Keywords',
            'Processing Status',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        Feedback::query()
            ->with(['category', 'sentimentResult'])
            ->orderBy('id')
            ->lazyById(500)
            ->each(function (Feedback $feedback) use ($sheet, &$row): void {
                $result = $feedback->sentimentResult;

                $sheet->setCellValue('A'.$row, $feedback->id);
                $sheet->setCellValue('B'.$row, Date::PHPToExcel($feedback->created_at));
                $this->setText($sheet, 'C'.$row, $feedback->category?->name ?? 'Uncategorized');
                $this->setText($sheet, 'D'.$row, $feedback->content);
                $this->setText($sheet, 'E'.$row, $result?->sentiment ? ucfirst($result->sentiment) : 'Unclassified');
                $sheet->setCellValue('F'.$row, $result?->confidence);
                $this->setText($sheet, 'G'.$row, $result?->language_category ?? 'Unclassified');
                $this->setText($sheet, 'H'.$row, implode(', ', $result?->detected_languages ?? []));
                $sheet->setCellValue('I'.$row, $result?->language_confidence);
                $this->setText($sheet, 'J'.$row, implode(', ', $result?->keywords ?? []));
                $this->setText($sheet, 'K'.$row, ucfirst($feedback->status));

                $row++;
            });

        $lastRow = max(1, $row - 1);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:K{$lastRow}");
        $sheet->getRowDimension(1)->setRowHeight(26);

        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Arial',
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3730A3'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        if ($lastRow >= 2) {
            $sheet->getStyle("A2:K{$lastRow}")->getFont()->setName('Arial')->setSize(10);
            $sheet->getStyle("A2:K{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle("B2:B{$lastRow}")->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm');
            $sheet->getStyle("F2:F{$lastRow}")->getNumberFormat()->setFormatCode('0.0%');
            $sheet->getStyle("I2:I{$lastRow}")->getNumberFormat()->setFormatCode('0.0%');
            $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("H2:J{$lastRow}")->getAlignment()->setWrapText(true);
        }

        foreach ([
            'A' => 12,
            'B' => 20,
            'C' => 22,
            'D' => 60,
            'E' => 14,
            'F' => 21,
            'G' => 24,
            'H' => 28,
            'I' => 21,
            'J' => 32,
            'K' => 18,
        ] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $filename = 'resback-feedbacks-'.now()->format('Y-m-d-His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function setText($sheet, string $cell, ?string $value): void
    {
        $sheet->setCellValueExplicit($cell, $value ?? '', DataType::TYPE_STRING);
    }
}
