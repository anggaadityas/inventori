<?php

    // Buat HTML table secara langsung di PHP
    $htmlTable = '
    <table>
        <tr>
            <th>Name</th>
            <th>Age</th>
            <th>Location</th>
        </tr>
        <tr>
            <td>John Doe</td>
            <td>30</td>
            <td>New York</td>
        </tr>
        <tr>
            <td>Jane Smith</td>
            <td>25</td>
            <td>Los Angeles</td>
        </tr>
    </table>';

    // Konversi HTML table menjadi file Excel (dengan format XML)
    $excelContent = '<?xml version="1.0"?>
    <ss:Workbook xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
        <ss:Worksheet ss:Name="Sheet1">
            <ss:Table>
                <ss:Row>
                    <ss:Cell><ss:Data ss:Type="String">Name</ss:Data></ss:Cell>
                    <ss:Cell><ss:Data ss:Type="String">Age</ss:Data></ss:Cell>
                    <ss:Cell><ss:Data ss:Type="String">Location</ss:Data></ss:Cell>
                </ss:Row>
                <ss:Row>
                    <ss:Cell><ss:Data ss:Type="String">John Doe</ss:Data></ss:Cell>
                    <ss:Cell><ss:Data ss:Type="Number">30</ss:Data></ss:Cell>
                    <ss:Cell><ss:Data ss:Type="String">New York</ss:Data></ss:Cell>
                </ss:Row>
                <ss:Row>
                    <ss:Cell><ss:Data ss:Type="String">Jane Smith</ss:Data></ss:Cell>
                    <ss:Cell><ss:Data ss:Type="Number">25</ss:Data></ss:Cell>
                    <ss:Cell><ss:Data ss:Type="String">Los Angeles</ss:Data></ss:Cell>
                </ss:Row>
            </ss:Table>
        </ss:Worksheet>
    </ss:Workbook>';

    // Set header untuk unduhan file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="downloaded_file.xls"');
    header('Cache-Control: max-age=0');

    // Kirim output
    echo $excelContent;
    exit;

?>
