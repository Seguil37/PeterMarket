<?php

namespace App\Support;

use Illuminate\Support\Str;

class SimplePdf
{
    protected float $width;
    protected float $height;
    protected float $margin;
    protected array $pages = [];
    protected int $currentPage = -1;
    protected float $cursorY = 0.0;

    public function __construct(float $width = 612, float $height = 792, float $margin = 36)
    {
        $this->width = $width;
        $this->height = $height;
        $this->margin = $margin;
    }

    public function addPage(): void
    {
        $this->pages[] = '';
        $this->currentPage++;
        $this->cursorY = $this->height - $this->margin;
    }

    public function addTitle(string $text): void
    {
        $this->writeLine($text, 18, 'F1');
        $this->spacer(6);
    }

    public function addSubtitle(string $text): void
    {
        $this->writeLine($text, 12, 'F1');
        $this->spacer(8);
    }

    public function spacer(float $height = 12): void
    {
        $this->cursorY -= $height;
        $this->ensureSpace();
    }

    public function addParagraph(string $text, float $size = 11, string $font = 'F1'): void
    {
        foreach ($this->wrapText($text, $size === 11 ? 94 : 88) as $line) {
            $this->writeLine($line, $size, $font);
        }
        $this->spacer(4);
    }

    public function addKeyValueRows(array $rows, float $size = 11): void
    {
        foreach ($rows as $label => $value) {
            $this->writeLine($label . ': ' . $value, $size, 'F1');
        }
        $this->spacer(6);
    }

    public function addTable(array $headers, array $rows, ?array $columnWidths = null): void
    {
        $widths = $columnWidths ?? array_fill(0, count($headers), (int) floor(($this->width - $this->margin * 2) / count($headers)));
        $normalizedRows = array_map(function (array $row) use ($widths) {
            return array_values(array_map(fn ($cell, $index) => $this->limitCell((string) $cell, $widths[$index] ?? 18), $row, array_keys($row)));
        }, $rows);

        $this->writeLine($this->formatRow($headers, $widths), 10, 'F2');
        $this->writeLine($this->formatDivider($widths), 10, 'F2');

        foreach ($normalizedRows as $row) {
            $this->writeLine($this->formatRow($row, $widths), 10, 'F2');
        }

        $this->spacer(10);
    }

    public function download(string $filename)
    {
        if ($this->currentPage === -1) {
            $this->addPage();
        }

        $pdf = $this->render();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function render(): string
    {
        $objects = [
            1 => '', // Catalog placeholder
            2 => '', // Pages placeholder
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>',
        ];

        $kids = [];
        foreach ($this->pages as $content) {
            $contentId = count($objects) + 1;
            $objects[$contentId] = "<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream";

            $pageId = $contentId + 1;
            $kids[] = $pageId;
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . $this->width . ' ' . $this->height . '] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents ' . $contentId . ' 0 R >>';
        }

        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', array_map(fn ($id) => $id . ' 0 R', $kids)) . '] /Count ' . count($kids) . ' >>';
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';

        $buffer = "%PDF-1.4\n";
        $offsets = [0];
        $objectIds = array_keys($objects);
        sort($objectIds);

        foreach ($objectIds as $id) {
            $offsets[$id] = strlen($buffer);
            $buffer .= $id . " 0 obj\n" . $objects[$id] . "\nendobj\n";
        }

        $xrefPosition = strlen($buffer);
        $buffer .= 'xref\n0 ' . (count($objects) + 1) . "\n";
        $buffer .= "0000000000 65535 f \n";

        foreach ($objectIds as $id) {
            $buffer .= sprintf('%010d 00000 n ', $offsets[$id]) . "\n";
        }

        $buffer .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>' . "\n";
        $buffer .= 'startxref' . "\n" . $xrefPosition . "\n";
        $buffer .= '%%EOF';

        return $buffer;
    }

    protected function writeLine(string $text, float $size, string $font): void
    {
        $this->ensureSpace();
        $escaped = $this->escapeText($text);
        $y = $this->cursorY;
        $x = $this->margin;

        $this->pages[$this->currentPage] .= sprintf("BT /%s %.2f Tf 1 0 0 1 %.2f %.2f Tm (%s) Tj ET\n", $font, $size, $x, $y, $escaped);

        $this->cursorY -= ($size + 4);
    }

    protected function escapeText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    protected function wrapText(string $text, int $maxCharacters): array
    {
        $clean = preg_replace('/\s+/', ' ', trim($text));

        if ($clean === '') {
            return [];
        }

        return explode("\n", wordwrap($clean, $maxCharacters, "\n", true));
    }

    protected function formatDivider(array $widths): string
    {
        return collect($widths)->map(fn ($width) => str_repeat('-', max(3, (int) ($width / 6))))->implode('   ');
    }

    protected function formatRow(array $columns, array $widths): string
    {
        $cells = [];
        foreach ($columns as $index => $value) {
            $width = (int) floor(($widths[$index] ?? 32) / 6);
            $cells[] = str_pad(mb_substr((string) $value, 0, $width), $width);
        }

        return implode('   ', $cells);
    }

    protected function limitCell(string $text, int $pixels): string
    {
        $maxChars = max(6, (int) floor($pixels / 6));
        return Str::limit($text, $maxChars, '…');
    }

    protected function ensureSpace(): void
    {
        if ($this->currentPage === -1) {
            $this->addPage();
        }

        if ($this->cursorY <= $this->margin) {
            $this->addPage();
        }
    }
}
