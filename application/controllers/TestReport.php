<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TestReport extends CI_Controller
{
    public function index()
    {
        $this->load->database();
        $this->load->model('TestReport_model');
        $data['locations'] = $this->TestReport_model->getLocations();
        $data['products'] = $this->TestReport_model->getProducts();

        $this->load->view('test_report', $data);
    }

    public function export()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $location_id = $this->input->get('location_id');
        $item_id = $this->input->get('item_id');
        $format = $this->input->get('format');

        $this->load->database();
        $this->load->model('TestReport_model');

        $productSaleQuery = $this->TestReport_model->getRetailProducts(
            '',
            '',
            $start_date,
            $end_date,
            $location_id,
            $item_id
        );   //  getRetailProducts GROUPBY SALON

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Rows
        $row = 1;
        $currentSalonTag = '';
        $firstEntry = true;

        // Computed Variables
        $totalUnits = 0;
        $totalTake = 0;
        $totalUnitsPerSalon = 0;
        $totalTakePerSalon = 0;

        // Percentage variables
        $average_number_of_sales = 0;
        $average_by_value = 0;

        // PER SALON DATA ROWS ----------------------------------------------------------------------------------------------------------
        foreach ($productSaleQuery as $productSale) {

            // If current salon is not = current foreach salon; Salon Switched
            if ($currentSalonTag != $productSale['location_name']) {
                //  Mark Current Salon Switch
                $currentSalonTag = $productSale['location_name'];

                // If not first entry, meaning there's a previous rows without Total records, so insert it.
                if (!$firstEntry) {
                    $sheet->setCellValue('A' . $row, 'Total');
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);

                    $sheet->setCellValue('B' . $row, $totalUnitsPerSalon);
                    $sheet->getStyle('B' . $row)->getFont()->setBold(true);
                    $sheet->setCellValue('C' . $row, $totalTakePerSalon);
                    $sheet->getStyle('C' . $row)->getFont()->setBold(true);

                    $row += 2; // Skip two row to add new line spacing for every salon
                } else {
                    $firstEntry = false; // Don't skip one row, first salon row
                }

                //  Write Salon Name with Bold
                $sheet->setCellValue('A' . $row, $currentSalonTag);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('B' . $row, 'No. of Units');
                $sheet->getStyle('B' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('C' . $row, 'Total Take');
                $sheet->getStyle('C' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('D' . $row, '% Overall Product Sales (Number of Sales)');
                $sheet->getStyle('D' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('E' . $row, '% Overall Product Sales (by Value)');
                $sheet->getStyle('E' . $row)->getFont()->setBold(true);

                $row++;

                //  Get Total Units and Total Take per Salon
                $units_totaltake = $this->TestReport_model->getUnits_TotalTakePerSalon($currentSalonTag, $start_date, $end_date, $location_id, $item_id);   // filtered by Salon
                $totalUnitsPerSalon = $units_totaltake['no_of_units'];  //  Save Total Units per Salon
                $totalTakePerSalon = $units_totaltake['total_take'];  //  Save Total Take per Salon

            }

            $sheet->setCellValue('A' . $row, $productSale['name']);
            $sheet->setCellValue('B' . $row, $productSale['no_of_units']);
            $sheet->setCellValue('C' . $row, $productSale['total_take']);

            $average_number_of_sales = number_format(($productSale['no_of_units'] / $totalUnitsPerSalon) * 100, 2) . '%';
            $sheet->setCellValue('D' . $row, $average_number_of_sales);

            $average_by_value = number_format(($productSale['total_take'] / $totalTakePerSalon) * 100, 2) . '%';
            $sheet->setCellValue('E' . $row, $average_by_value);

            $row++;
        }

        //  After the loop has been stopped, the last salon don't have a total written due to there's no more array. Add the last salon after it stops.
        $sheet->setCellValue('A' . $row, 'Total');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('B' . $row, $totalUnitsPerSalon);
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('C' . $row, $totalTakePerSalon);
        $sheet->getStyle('C' . $row)->getFont()->setBold(true);

        $row += 3;

        //  All product sales without categorizing on each salon ---------------------------------------------------------------------------------------
        $productSaleLists = $this->TestReport_model->getRetailProducts('groupByProductName', '', $start_date, $end_date, $location_id, $item_id);   //  getRetailProducts GROUPBY Product name

        // Header
        $sheet->setCellValue('A' . $row, 'All Salons');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('B' . $row, 'No. of Units');
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('C' . $row, 'Total Take');
        $sheet->getStyle('C' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('D' . $row, '% Overall Product Sales (Number of Sales)');
        $sheet->getStyle('D' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('E' . $row, '% Overall Product Sales (by Value)');
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);

        $row++;

        $totalUnitsPerProduct = 0;
        $totalTakePerProduct = 0;

        foreach ($productSaleLists as $psData) {
            $totalUnitsPerProduct += $psData['no_of_units'];
            $totalTakePerProduct += $psData['total_take'];
        }

        foreach ($productSaleLists as $eachProductSale) {

            $sheet->setCellValue('A' . $row, $eachProductSale['name']);
            $sheet->setCellValue('B' . $row, $eachProductSale['no_of_units']);
            $sheet->setCellValue('C' . $row, $eachProductSale['total_take']);

            $each_average_number_of_sales = number_format(($eachProductSale['no_of_units'] / $totalUnitsPerProduct) * 100, 2) . '%';
            $sheet->setCellValue('D' . $row, $each_average_number_of_sales);

            $each_average_by_value = number_format(($eachProductSale['total_take'] / $totalTakePerProduct) * 100, 2) . '%';
            $sheet->setCellValue('E' . $row, $each_average_by_value);

            $row++;
        }

        $sheet->setCellValue('A' . $row, 'Total');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('B' . $row, $totalUnitsPerProduct);
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('C' . $row, $totalTakePerProduct);
        $sheet->getStyle('C' . $row)->getFont()->setBold(true);

        if ($format == 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="test_report.xls"');
            
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }

        if ($format == 'pdf') {
            $writer = IOFactory::createWriter($spreadsheet, 'Mpdf');

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="test_report.pdf"');

            $writer->save('php://output');
        }
    }
}
