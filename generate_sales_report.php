<?php
require_once 'session_config.php';
require 'config.php';
require('fpdf.php');

$current_user = isset($_SESSION['valid']) ? $_SESSION['valid'] : 'Unknown User';

date_default_timezone_set('Australia/Perth');

$orderTableSQL = "`orders`";
$orderDetailTableSQL = "`order_detail`";
$productsTableSQL = "`products`";

function money($n){ return number_format((float)$n, 2); }

$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date   = isset($_POST['end_date'])   && $_POST['end_date']   !== '' ? $_POST['end_date']   : date('Y-m-d');

$start_dt = $start_date . ' 00:00:00';
$end_dt   = $end_date . ' 23:59:59';

$start_display = date("F j, Y", strtotime($start_date));
$end_display   = date("F j, Y", strtotime($end_date));

// ---- Summary
$stmt = $con->prepare("
    SELECT 
      COALESCE(SUM(od.quantity),0) AS total_units_sold,
      COALESCE(COUNT(DISTINCT o.order_id),0) AS total_orders
    FROM {$orderTableSQL} o
    LEFT JOIN {$orderDetailTableSQL} od ON o.order_id = od.order_id
    WHERE o.order_date BETWEEN ? AND ?
");
$stmt->bind_param("ss", $start_dt, $end_dt);
$stmt->execute();
$sales_summary = $stmt->get_result()->fetch_assoc();
$stmt->close();

$total_units_sold = (int)($sales_summary['total_units_sold'] ?? 0);
$total_orders     = (int)($sales_summary['total_orders'] ?? 0);

// ---- Product Breakdown
$stmt = $con->prepare("
    SELECT 
        p.product_id,
        p.barcode,
        p.name,
        p.price AS retail_price,
        p.cost AS wholesale_cost,
        SUM(od.quantity) AS units_sold
    FROM {$orderDetailTableSQL} od
    JOIN {$orderTableSQL} o ON od.order_id = o.order_id
    JOIN {$productsTableSQL} p ON od.product_id = p.product_id
    WHERE o.order_date BETWEEN ? AND ?
    GROUP BY p.product_id, p.barcode, p.name, p.price, p.cost
    ORDER BY units_sold DESC
");
$stmt->bind_param("ss", $start_dt, $end_dt);
$stmt->execute();
$res = $stmt->get_result();
$sales_by_product = [];
$product_ids = [];
while($r = $res->fetch_assoc()){
    $product_ids[] = $r['product_id'];
    $wholesale = (float)$r['wholesale_cost'];
    $markup    = $r['retail_price'] - $wholesale;
    $profit    = $markup * $r['units_sold'];
    $sales_by_product[$r['product_id']] = [
        'barcode'      => $r['barcode'],
        'product_name' => $r['name'],
        'wholesale'    => $wholesale,
        'units_sold'   => $r['units_sold'],
        'markup'       => $markup,
        'retail_price' => $r['retail_price'],
        'profit'       => $profit,
        'dates_sold'   => ''
    ];
}
$stmt->close();

// ---- Dates Sold
if (!empty($product_ids)) {
    $ids_placeholder = implode(',', array_fill(0, count($product_ids), '?'));
    $types = str_repeat('i', count($product_ids));

    $sql_dates = "
        SELECT 
            p.product_id,
            DATE(o.order_date) AS sale_date
        FROM {$orderDetailTableSQL} od
        JOIN {$orderTableSQL} o ON od.order_id = o.order_id
        JOIN {$productsTableSQL} p ON od.product_id = p.product_id
        WHERE o.order_date BETWEEN ? AND ?
          AND p.product_id IN ($ids_placeholder)
        GROUP BY p.product_id, DATE(o.order_date)
        ORDER BY p.product_id, sale_date
    ";

    $stmt = $con->prepare($sql_dates);
    $bind_types = "ss".$types;
    $bind_values = array_merge([$bind_types, $start_dt, $end_dt], $product_ids);
    $refs = [];
    foreach ($bind_values as $key => $value) {
        $refs[$key] = &$bind_values[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $refs);

    $stmt->execute();
    $res_dates = $stmt->get_result();
    $dates_by_product = [];
    while($row = $res_dates->fetch_assoc()){
        $pid = $row['product_id'];
        $date_str = date("F j", strtotime($row['sale_date']));
        $dates_by_product[$pid][] = $date_str;
    }
    $stmt->close();

    foreach ($dates_by_product as $pid => $dates) {
        if (count($dates) > 0) {
            $formatted_dates = [];
            foreach ($dates as $d) {
                $timestamp = strtotime($d);
                $formatted_dates[] = date('n/j', $timestamp);
            }
            $sales_by_product[$pid]['dates_sold'] = implode(', ', $formatted_dates);
        }
    }
}

// ---- PDF CLASS
class PDF extends FPDF {
    function Header(){
        global $current_user;
        $this->SetFont('Arial','B',18);
        $this->Cell(0,6,'Sales Report',0,1,'C');
        $this->SetFont('Arial','B',10);
        $this->Cell(0,6,'RBM Motorparts, Accessories, & Services',0,1,'C');
        if(!empty($current_user)){
            $this->SetFont('Arial','I',9);
        }
        $this->Ln(3);
    }

    function Footer(){
        global $start_display, $end_display;
        $this->SetY(-23);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,5,'Report period: '.$start_display.' to '.$end_display,0,1,'L');
        $this->Cell(0,5,'Generated: '.date('F j, Y g:i A'),0,1,'R');
    }
}

$pdf = new PDF('P','mm','A4');
$pdf->SetAutoPageBreak(true, 28);
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

// ---- Summary
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,6,'Generated by: '.$current_user,0,1,'L');
$pdf->Cell(0,5,"Report period: $start_display to $end_display",0,1,'L');
$pdf->Ln(2);
$pdf->SetFont('Arial','',10);
$pdf->Cell(70,6,"Total Purchases (Orders):",0,0);
$pdf->Cell(0,6,$total_orders,0,1);
$pdf->Cell(70,6,"Total Units Sold:",0,0);
$pdf->Cell(0,6,$total_units_sold,0,1);

// ---- Product Table
$pdf->Ln(6);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,6,'Sales by Product',0,1);
$pdf->SetFont('Arial','B',8);

$w = [20, 25, 35, 22, 18, 20, 22, 25]; 
$headers = ['Dates Sold','Product Code','Product','Wholesale','Units Sold','Markup','Retail','Profit'];
for($i=0;$i<count($headers);$i++){
    $pdf->Cell($w[$i],7,$headers[$i],1,0,'C');
}
$pdf->Ln();

$pdf->SetFont('Arial','',8);
$total_units = 0;
$total_profit = 0;

foreach($sales_by_product as $row){
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $pdf->MultiCell($w[0], 5, utf8_decode($row['dates_sold']), 1, 'L');
    $h = $pdf->GetY() - $y;
    $pdf->SetXY($x + $w[0], $y);

    $pdf->Cell($w[1], $h, $row['barcode'],1,0,'C');
    $pdf->Cell($w[2], $h, substr($row['product_name'],0,25),1,0,'L');
    $pdf->Cell($w[3], $h, money($row['wholesale']),1,0,'R');
    $pdf->Cell($w[4], $h, $row['units_sold'],1,0,'C');
    $pdf->Cell($w[5], $h, money($row['markup']),1,0,'R');
    $pdf->Cell($w[6], $h, money($row['retail_price']),1,0,'R');
    $pdf->Cell($w[7], $h, money($row['profit']),1,1,'R');

    $total_units += $row['units_sold'];
    $total_profit += $row['profit'];
}

$pdf->SetFont('Arial','B',8);
$pdf->Cell($w[0],6,'',1,0,'C');
$pdf->Cell($w[1],6,'TOTAL',1,0,'C');
$pdf->Cell($w[2]+$w[3],6,'',1,0);
$pdf->Cell($w[4],6,$total_units,1,0,'C');
$pdf->Cell($w[5]+$w[6],6,'',1,0);
$pdf->Cell($w[7],6,money($total_profit),1,1,'R');

// ---- Log the report generation to system_log

$user_stmt = $con->prepare("SELECT Id, Username, role FROM users WHERE Username = ?");
$user_stmt->bind_param("s", $current_user);
$user_stmt->execute();
$user_result = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

if ($user_result) {
    $user_id   = $user_result['Id'];
    $username  = $user_result['Username'];
    $user_role = $user_result['role'];
} else {
    $user_id   = 0;
    $username  = $current_user;
    $user_role = 'Unknown';
}

$action_type = 'Generate Sales Report';
$description = "Sales report generated for period $start_display to $end_display.";
$module      = 'Reports';
$submodule   = 'Generate Sales Report';
$result      = 'Success';
$log_date    = date('Y-m-d');
$log_time    = date('H:i:s');

$log_stmt = $con->prepare("
    INSERT INTO system_log 
    (user_id, username, user_role, action_type, description, module, submodule, result, date, time)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$log_stmt->bind_param(
    "isssssssss",
    $user_id,
    $username,
    $user_role,
    $action_type,
    $description,
    $module,
    $submodule,
    $result,
    $log_date,
    $log_time
);
$log_stmt->execute();
$log_stmt->close();

while (ob_get_level()) {
    ob_end_clean();
}

$pdf->Output('I', 'sales_report_'.$start_date.'_to_'.$end_date.'.pdf');
exit;
?>
