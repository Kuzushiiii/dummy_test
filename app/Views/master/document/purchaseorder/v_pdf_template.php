<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Purchase Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        h2 {
            font-weight: bolder;    
        }

        td,
        th {
            padding: 4px;
            vertical-align: top;
        }

        .border td,
        .border th {
            border: 1px solid #000;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .title {
            font-size: 24px;
            font-weight: bolder;
        }

        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-top: 8px;
        }

        .section-divider {
            border: none;
            border-bottom: 3px solid black;
            margin: 10px 0 20px 0;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <table>
        <tr>
            <td width="20%">
                <?php if (!empty($logo)): ?>
                    <img src="<?= $logo ?>" width="90">
                <?php else: ?>
                    <div style="width: 80px; height: 80px; background-color: #f0f0f0; border: 1px solid #ccc;">
                        No Logo
                    </div>
                <?php endif; ?>
            <td width="50%" class="center title"> PURCHASE ORDER</td>
            <td width="30%">
                <strong>PT AUTOCHEM</strong><br>
                Jl. Gatot Subroto Km 7 no 12 Jatake Jatiuwung Kota Tangerang <br> Banten 15136 Indonesia<br>
                Telp: +62(21)5900131
            </td>
        </tr>
    </table>

    <hr class="section-divider">

    <!-- INFO PURCHASE ORDER -->
    <table>
        <tr>
            <td width="50%">
                <table>
                    <tr>
                        <td width="40%">Transaction Code</td>
                        <td width="60%">: <?= esc($header['transcode']) ?></td>
                    </tr>
                    <tr>
                        <td>Supplier</td>
                        <td>: <?= esc($header['suppliername']) ?></td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table>
                    <tr>
                        <td width="40%">Tanggal Purchase Order</td>
                        <td width="60%">: <?= date('d F Y', strtotime($header['transdate'])) ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Supply</td>
                        <td>: <?= date('d F Y', strtotime($header['supplydate'])) ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <h2>Data Penjualan</h2>
    <table class="border">
        <thead>
            <tr class="center">
                <th width="5%">No</th>
                <th width="35%">Product Name</th>
                <th width="10%">UOM</th>
                <th width="10%">Qty</th>
                <th width="20%">Price</th>
                <th width="20%">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            $total = 0;
            foreach ($details as $d): $subtotal = $d['qty'] * $d['price'];
                $total += $subtotal; ?>
                <tr>
                    <td class="center"><?= $no++ ?></td>
                    <td><?= esc($d['productname']) ?></td>
                    <td class="center"><?= esc($d['uomnm']) ?></td>
                    <td class="right"><?= number_format($d['qty'], 0, ',', '.') ?></td>
                    <td class="right">Rp <?= number_format($d['price'], 3, ',', '.') ?></td>
                    <td class="right">Rp <?= number_format($subtotal, 3, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <!-- NOTES & SUMMARY -->
    <table style="width:30%; float:right; border-collapse: collapse;">
        <tr>
            <td>
                <table width="100%">
                    <tr>
                        <td><strong>Sub Total</strong></td>
                        <td class="right">Rp <?= number_format($total, 3, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Discount</strong></td>
                        <td class="right">0</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-top: 2px solid black;"></td>
                    <tr>
                        <td><strong>PPN (11%)</strong></td>
                        <td class="right">Rp <?= number_format($ppn, 3, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-top: 2px solid black;"></td>
                    </tr>
                    <tr>
                        <td><strong>Grand Total</strong></td>
                        <td class="right"><strong>Rp <?= number_format($grandtotal, 3, ',', '.') ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br><br>
</body>

</html>