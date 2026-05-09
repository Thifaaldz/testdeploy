<?php

namespace App\Services\Spreadsheets;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class XlsxValueReader
{
    public function read(string $path): array
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException("Tidak dapat membuka file spreadsheet: {$path}");
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetTargets = $this->readSheetTargets($zip);
        $rowsBySheet = [];

        foreach ($sheetTargets as $name => $target) {
            $rowsBySheet[$name] = $this->readSheetRows($zip, $target, $sharedStrings);
        }

        $zip->close();

        return $rowsBySheet;
    }

    public function rowsByHeading(array $sheetRows): array
    {
        if ($sheetRows === []) {
            return [];
        }

        $headerRow = array_shift($sheetRows);
        $headers = [];

        foreach ($headerRow as $column => $value) {
            $headers[$column] = trim((string) $value);
        }

        $records = [];

        foreach ($sheetRows as $row) {
            $record = [];

            foreach ($headers as $column => $heading) {
                if ($heading === '') {
                    continue;
                }

                $record[$heading] = $row[$column] ?? null;
            }

            $hasValue = false;

            foreach ($record as $value) {
                if ($value !== null && $value !== '') {
                    $hasValue = true;
                    break;
                }
            }

            if ($hasValue) {
                $records[] = $record;
            }
        }

        return $records;
    }

    private function extractCellValue(SimpleXMLElement $cell, array $sharedStrings): mixed
    {
        $attributes = $cell->attributes();
        $type = isset($attributes['t']) ? (string) $attributes['t'] : null;

        if ($type === 'inlineStr' && isset($cell->is)) {
            $parts = [];

            foreach ($cell->is->t as $textNode) {
                $parts[] = (string) $textNode;
            }

            if (isset($cell->is->r)) {
                foreach ($cell->is->r as $richText) {
                    if (isset($richText->t)) {
                        $parts[] = (string) $richText->t;
                    }
                }
            }

            return implode('', $parts);
        }

        if (! isset($cell->v)) {
            return null;
        }

        $value = (string) $cell->v;

        if ($type === 's') {
            return $sharedStrings[(int) $value] ?? null;
        }

        if ($type === 'b') {
            return $value === '1';
        }

        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }

        return $value;
    }

    private function readSharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if ($xml === false) {
            return [];
        }

        $sharedStringsXml = simplexml_load_string($xml);

        if (! $sharedStringsXml) {
            return [];
        }

        $strings = [];

        foreach ($sharedStringsXml->si as $item) {
            $parts = [];

            if (isset($item->t)) {
                $parts[] = (string) $item->t;
            }

            foreach ($item->r as $richText) {
                if (isset($richText->t)) {
                    $parts[] = (string) $richText->t;
                }
            }

            $strings[] = implode('', $parts);
        }

        return $strings;
    }

    private function readSheetRows(ZipArchive $zip, string $target, array $sharedStrings): array
    {
        $xml = $zip->getFromName($target);

        if ($xml === false) {
            return [];
        }

        $sheetXml = simplexml_load_string($xml);

        if (! $sheetXml || ! isset($sheetXml->sheetData)) {
            return [];
        }

        $rows = [];

        foreach ($sheetXml->sheetData->row as $row) {
            $cells = [];

            foreach ($row->c as $cell) {
                $reference = (string) $cell['r'];
                $column = preg_replace('/\d+/', '', $reference);
                $cells[$column] = $this->extractCellValue($cell, $sharedStrings);
            }

            $rows[] = $cells;
        }

        return $rows;
    }

    private function readSheetTargets(ZipArchive $zip): array
    {
        $workbookXml = simplexml_load_string($zip->getFromName('xl/workbook.xml'));
        $relationshipsXml = simplexml_load_string($zip->getFromName('xl/_rels/workbook.xml.rels'));

        if (! $workbookXml || ! $relationshipsXml) {
            throw new RuntimeException('Struktur workbook tidak valid.');
        }

        $relationshipNamespaces = $relationshipsXml->getNamespaces(true);
        $workbookNamespaces = $workbookXml->getNamespaces(true);

        $relationships = [];

        foreach ($relationshipsXml->Relationship as $relationship) {
            $id = (string) $relationship['Id'];
            $target = (string) $relationship['Target'];

            $relationships[$id] = 'xl/' . ltrim($target, '/');
        }

        $targets = [];

        foreach ($workbookXml->sheets->sheet as $sheet) {
            $name = (string) $sheet['name'];
            $attributes = $sheet->attributes($workbookNamespaces['r'] ?? null);
            $relationId = (string) ($attributes['id'] ?? '');

            if ($relationId !== '' && isset($relationships[$relationId])) {
                $targets[$name] = $relationships[$relationId];
            }
        }

        return $targets;
    }
}
