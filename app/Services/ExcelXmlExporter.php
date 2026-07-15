<?php

declare(strict_types=1);

namespace App\Services;

final class ExcelXmlExporter
{
    private function __construct()
    {
    }

    public static function build(
        string $worksheetName,
        array $headers,
        array $rows
    ): string {
        $worksheetName = self::safeWorksheetName(
            $worksheetName
        );

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<?mso-application progid="Excel.Sheet"?>';
        $xml .= '<Workbook '
            . 'xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
            . 'xmlns:o="urn:schemas-microsoft-com:office:office" '
            . 'xmlns:x="urn:schemas-microsoft-com:office:excel" '
            . 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';

        $xml .= '<Styles>';
        $xml .= '<Style ss:ID="Default" ss:Name="Normal">'
            . '<Alignment ss:Vertical="Center"/>'
            . '<Font ss:FontName="Calibri" ss:Size="11"/>'
            . '</Style>';
        $xml .= '<Style ss:ID="Header">'
            . '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/>'
            . '<Font ss:Bold="1" ss:Color="#FFFFFF"/>'
            . '<Interior ss:Color="#2F7FBD" ss:Pattern="Solid"/>'
            . '</Style>';
        $xml .= '<Style ss:ID="Money">'
            . '<NumberFormat ss:Format="Currency"/>'
            . '</Style>';
        $xml .= '<Style ss:ID="Date">'
            . '<NumberFormat ss:Format="Short Date"/>'
            . '</Style>';
        $xml .= '</Styles>';

        $xml .= '<Worksheet ss:Name="'
            . self::escape($worksheetName)
            . '"><Table>';

        foreach ($headers as $_header) {
            $xml .= '<Column ss:AutoFitWidth="1" ss:Width="120"/>';
        }

        $xml .= '<Row ss:Height="24">';

        foreach ($headers as $header) {
            $xml .= self::cell(
                (string) $header,
                'String',
                'Header'
            );
        }

        $xml .= '</Row>';

        foreach ($rows as $row) {
            $xml .= '<Row>';

            foreach ($row as $value) {
                [$type, $style, $normalized] =
                    self::normalizeValue($value);

                $xml .= self::cell(
                    $normalized,
                    $type,
                    $style
                );
            }

            $xml .= '</Row>';
        }

        $xml .= '</Table>';
        $xml .= '<WorksheetOptions '
            . 'xmlns="urn:schemas-microsoft-com:office:excel">'
            . '<FreezePanes/>'
            . '<FrozenNoSplit/>'
            . '<SplitHorizontal>1</SplitHorizontal>'
            . '<TopRowBottomPane>1</TopRowBottomPane>'
            . '<ActivePane>2</ActivePane>'
            . '</WorksheetOptions>';
        $xml .= '</Worksheet>';
        $xml .= '</Workbook>';

        return $xml;
    }

    private static function normalizeValue(
        mixed $value
    ): array {
        if ($value === null) {
            return ['String', null, ''];
        }

        if (is_int($value) || is_float($value)) {
            return ['Number', null, (string) $value];
        }

        if (is_bool($value)) {
            return [
                'String',
                null,
                $value ? 'Sí' : 'No',
            ];
        }

        return ['String', null, (string) $value];
    }

    private static function cell(
        string $value,
        string $type,
        ?string $style
    ): string {
        $styleAttribute = $style !== null
            ? ' ss:StyleID="' . self::escape($style) . '"'
            : '';

        return '<Cell'
            . $styleAttribute
            . '><Data ss:Type="'
            . self::escape($type)
            . '">'
            . self::escape($value)
            . '</Data></Cell>';
    }

    private static function safeWorksheetName(
        string $name
    ): string {
        $name = preg_replace(
            '~[\\/?:*\[\]]~',
            ' ',
            $name
        ) ?? 'Reporte';

        $name = trim($name);

        if ($name === '') {
            $name = 'Reporte';
        }

        return substr($name, 0, 31);
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars(
            $value,
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        );
    }
}
