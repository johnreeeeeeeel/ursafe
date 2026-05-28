<?php
require 'fpdf/fpdf.php';
require '../db_connection.php';

// PAID AMOUNT
$stmtPaidAmount = $conn_local->prepare("CALL getPaidEndedLockerApplicationsAmount()");
$stmtPaidAmount->execute();
$paidAmount = $stmtPaidAmount->get_result()->fetch_assoc();
$stmtPaidAmount->close();
$conn_local->next_result();

// PAID COUNT
$stmtPaidCount = $conn_local->prepare("CALL getPaidEndedLockerApplicationsCount()");
$stmtPaidCount->execute();
$paidCount = $stmtPaidCount->get_result()->fetch_assoc();
$stmtPaidCount->close();
$conn_local->next_result();


// UNPAID AMOUNT
$stmtUnpaidAmount = $conn_local->prepare("CALL getUnpaidEndedLockerApplicationsAmount()");
$stmtUnpaidAmount->execute();
$unpaidAmount = $stmtUnpaidAmount->get_result()->fetch_assoc();
$stmtUnpaidAmount->close();
$conn_local->next_result();

// UNPAID COUNT
$stmtUnpaidCount = $conn_local->prepare("CALL getUnpaidEndedLockerApplicationsCount()");
$stmtUnpaidCount->execute();
$unpaidCount = $stmtUnpaidCount->get_result()->fetch_assoc();
$stmtUnpaidCount->close();
$conn_local->next_result();


$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial','B',16);
$pdf->Cell(190,10,'LOCKER APPLICATION PAYMENT REPORT',0,1,'C');

$pdf->Ln(1);

// Subtitle
$pdf->SetFont('Arial','',12);
$pdf->Cell(190,8,'For Ended Locker Applications',0,1,'C');

$pdf->Ln(8);

// Paid section
$pdf->SetFont('Arial','B',13);
$pdf->Cell(190,8,'PAID APPLICATIONS',0,1);

$pdf->SetFont('Arial','',11);

$pdf->Cell(60,8,'Total Transactions:',0,0);
$pdf->Cell(130,8,$paidCount['paid_total_transactions'],0,1);

$pdf->Cell(60,8,'Total Amount:',0,0);
$pdf->Cell(130,8,'PHP '.number_format($paidAmount['paid_total_amount'],2),0,1);

$pdf->Ln(6);

// Unpaid section
$pdf->SetFont('Arial','B',13);
$pdf->Cell(190,8,'UNPAID APPLICATIONS',0,1);

$pdf->SetFont('Arial','',11);

$pdf->Cell(60,8,'Total Transactions:',0,0);
$pdf->Cell(130,8,$unpaidCount['unpaid_total_transactions'],0,1);

$pdf->Cell(60,8,'Total Amount:',0,0);
$pdf->Cell(130,8,'PHP '.number_format($unpaidAmount['unpaid_total_amount'],2),0,1);

$pdf->Ln(8);


$pdf->Output('I', 'locker_payment_report.pdf');
?>