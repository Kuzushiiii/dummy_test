<?php

// func get base url
function getURL($param = "")
{
    return base_url($param);
}
// func get view file
function getView($url, $datas = [])
{
    if (!empty($datas)) $view = view($url, $datas);
    $view = view($url);
    return $view;
}
// play with session
function getSession($key)
{
    return decrypting(session()->get($key . '-hrs-session'));
}
function setSession($key, $value)
{
    return session()->set($key . '-hrs-session', encrypting($value));
}
function removeSession($key)
{
    return session()->remove($key);
}
function destroySession()
{
    return session()->destroy();
}
// json encode decode
function encode($val)
{
    return json_encode($val);
}
function decode($val)
{
    return json_decode($val);
}
function cekAkses($menuid, $groupid, $componentid)
{
    $aksesmen = new Msaccessmenu();
    $ceks = $aksesmen->AccessCheck($menuid, $groupid, $componentid);
    return $ceks->countAllResults();
}
function checkEmpty($input, $msg)
{
    if (empty($input)) {
        throw new Exception($msg);
    }
}
// function getTypeName($typename)
// {
//     $type = new Mssttype();
//     $dt = $type->getByName($typename);
//     return $dt;
// }
// function getByTypeid($typeid)
// {
//     $type = new Mssttype();
//     $one = $type->getOne($typeid);
//     return $one;
// }
function getMenu($foruser = '')
{
    $menu = new Msmenu();
    $rowmenu = $menu->getMaster();
    if ($foruser != '') {
        $rowmenu = $menu->getSidebar();
    }
    $array_all_menu = [];
    foreach ($rowmenu as $rm) {
        $data = [
            'type' => 'master',
            'masterid' => 0,
            'menuname' => $rm['menuname'],
            'menulink' => $rm['menulink'],
            'menuicon' => $rm['menuicon'],
            'menuid' => $rm['menuid'],
        ];
        array_push($array_all_menu, $data);
        $rowsub = $menu->getAll($rm['menuid'])->get()->getResultArray();
        if ($foruser != '') {
            $rowsub = $menu->checkMenu($rm['menuid'])->get()->getResultArray();
        }
        foreach ($rowsub as $rs) {
            $datadt = [
                'type' => 'submenu',
                'masterid' => $rm['menuid'],
                'menuname' => $rs['menuname'],
                'menulink' => $rs['menulink'],
                'menuicon' => $rs['menuicon'],
                'menuid' => $rs['menuid']
            ];
            array_push($array_all_menu, $datadt);
        }
    }
    return $array_all_menu;
}
function getSidebarMenu()
{
    $menu = new Msmenu();
    $rowmenu = $menu->getSidebar();
    $arrAll = [];
    foreach ($rowmenu as $rm) {
        $data = [
            'type' => 'master',
            'masterid' => 0,
            'menuname' => $rm['menuname'],
            'menulink' => $rm['menulink'],
            'menuicon' => $rm['menuicon'],
            'menuid' => $rm['menuid'],
        ];;
        array_push($arrAll, $data);
    }
    return $arrAll;
}
function getSidebarSubMenu($masterid)
{
    $arrMenu = [];
    $menu = new Msmenu();
    $rowsub = $menu->checkMenu($masterid)->get()->getResultArray();
    foreach ($rowsub as $rs) {
        $data = [
            'type' => 'submenu',
            'masterid' => $masterid,
            'menuname' => $rs['menuname'],
            'menulink' => $rs['menulink'],
            'menuicon' => $rs['menuicon'],
            'menuid' => $rs['menuid'],
        ];
        array_push($arrMenu, $data);
    }
    return $arrMenu;
}
function getSidebarView($masterid)
{
    $menu = new Msmenu();
    $rowsub = $menu->checkMenu($masterid)->get()->getResultArray();
    $data = [
        'rowsub' => $rowsub,
    ];
    return view('template/v_subsidebar', $data);
}
function sidebarShrink($masterid)
{
    $menu = new Msmenu();
    $rowsub = $menu->checkMenu($masterid)->get()->getResultArray();
    $data = [
        'rowsub' => $rowsub,
    ];
    return view('template/v_sideopen', $data);
}
function getAccess($componentid)
{
    $data = (new Msaccessmenu())->getSpecificAccessUser(getSession('userid'), getSession('companyid'), getSession('menuid'), $componentid);
    $isValid = false;
    if ($data) {
        $isValid = true;
    }
    return $isValid;
}

function formatDate($format, $date = '')
{
    $d = date($format);
    if ($date != '') {
        $d = date($format, strtotime($date));
    }
    return $d;
}

function exp_number($numb)
{
    $exp_imp = explode(',', $numb);
    $number_update =  '';
    foreach ($exp_imp as $r) {
        $number_update .= $r;
    }
    $exp_dua = explode('.', $number_update);
    $number_updatedua =  '';
    $j = 0;
    foreach ($exp_dua as $rdua) {
        if ($j == 0) {
            $number_updatedua .=  $rdua;
        } else {
            $number_updatedua .= '.' . $rdua;
        }
        $j++;
    }
    return $number_updatedua * 1;
}

function encrypting($teks = '')
{
    if ($teks == '') {
        return '';
    }
    $enkripsi = \Config\Services::encrypter();
    $base62 = new Tuupola\Base62;
    try {
        $result = $base62->encode($enkripsi->encrypt($teks));
    } catch (Exception $e) {
        $result = $teks;
    }
    return $result;
}

function decrypting($teks = '')
{
    if ($teks == '') {
        return '';
    }
    $enkripsi = \Config\Services::encrypter();
    $base62 = new Tuupola\Base62;
    try {
        $result = $enkripsi->decrypt($base62->decode($teks));
    } catch (Exception $e) {
        $result = $teks;
    }
    return $result;
}

function base_encode($text)
{
    $txt = $text;
    for ($n = 0; $n < 6; $n++) {
        $txt = base64_encode($txt);
    }
    return $txt;
}

function base_decode($text)
{
    $txt = $text;
    for ($n = 0; $n < 6; $n++) {
        $txt = base64_decode($txt);
    }
    return $txt;
}

function formatNumber($nomor, $decimal_separator = '.', $thousand_separator = ',', $decimal = 0)
{
    $exp_no = explode($decimal_separator, $nomor);
    $text = number_format($exp_no[0], $decimal, $decimal_separator, $thousand_separator);
    if (count($exp_no) > 1) {
        if ($exp_no[1] > 0) {
            $trims = rtrim($exp_no[1], 0);
            $text .= $decimal_separator . $trims;
        }
    }
    return $text;
}

function idr($value, $decimal = 2, $decimal_separator = '.', $thousand_separator = ',')
{
    return number_format($value, $decimal, $decimal_separator, $thousand_separator);
}

function usd($value, $decimal = 2, $decimal_separator = '.', $thousand_separator = ',')
{
    return number_format($value, $decimal, $decimal_separator, $thousand_separator);
}

function idrHTML($value, $id = '')
{
    $textid = '';
    if ($id != '') {
        $textid = "id=\"$id\"";
    }
    return "<div class=\"text-end\" style=\"font-weight: bold;\" $textid>Rp. " . idr($value) . "</div>";
}

function dbIDR($value)
{
    $value = str_replace(',', '', $value);
    $value = str_replace('Rp.', '', $value);
    $value = strip_tags($value);

    return trim($value);
}

function numRegular($value)
{
    $value = str_replace(',', '', $value);
    $value = str_replace('.', '', $value);
    return trim($value);
}

function cutNum($num, $precision = 2)
{
    return floor($num) . substr(str_replace(floor($num), '', $num), 0, $precision + 1);
}

// Terbilang
function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " triliun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}

function terbilang($nilai)
{
    if ($nilai < 0) {
        $hasil = "minus " . trim(penyebut($nilai));
    } else {
        $hasil = trim(penyebut($nilai));
    }
    return $hasil;
}

function getIcon($ext)
{
    $icon = "";
    if ($ext == 'txt') {
        $icon = 'bx bxs-file-txt';
    } else if ($ext == 'png' || $ext == 'jpeg' || $ext == 'jpg') {
        $icon = 'bx bxs-file-image';
    } else if ($ext == 'doc' || $ext == 'docx') {
        $icon = 'bx bxs-file-doc';
    } else if ($ext == 'pdf') {
        $icon = 'bx bxs-file-pdf';
    } else {
        $icon = "bx bxs-file";
    }
    return $icon;
}

// function insert_history($data, $refid, $table, $pk, $status)
// {
//     $map = new TrtableMapping();
//     $log = new Msloghistory();
//     $field = implode(',', array_keys($data));
//     $query = $map->getAllTable($field, $table, $pk, $refid);
//     $createddate = date('Y-m-d H:i:s');
//     foreach ($data as $k => $v) {
//         $info = $map->getMapping($table, $k);
//         if (empty($info)) {
//             $map->store([
//                 'tablename' => $table,
//                 'columnname' => $k,
//                 'remark' => $k,
//                 'createddate' => date('Y-m-d H:i:s'),
//                 'createdby' => getSession('userid'),
//                 'updateddate' => date('Y-m-d H:i:s'),
//                 'updatedby' => getSession('userid'),
//                 'isactive' => 't',
//             ]);
//             $info = $map->getMapping($table, $k);
//         }
//         if (empty($query[$k])) {
//             continue;
//         }
//         if ($query[$k] == $v && $status != 'add') {
//             continue;
//         }
//         $insert = [];
//         $insert['tablename'] = $table;
//         $insert['column'] = $k;
//         $insert['remark'] = $info['remark'];
//         $insert['refid'] = $refid;
//         $insert['tablename'] = $table;
//         $insert['before'] = (($status == 'add') ? '' : $query[$k]);
//         $insert['after'] = $v;
//         $insert['createdby'] = getSession('userid');
//         $insert['createddate'] = $createddate;
//         $insert['status'] = $status;
//         $log->store($insert);
//     }
//     return true;
// }

// function insertActivity($description, $menuid, $transid, $paramid)
// {
//     $userid = getSession('userid');
//     $tanggal = date('Y-m-d H:i:s');
//     $activity = new Msactivity;
//     $data = [
//         'userid' => $userid,
//         'createddate' => $tanggal,
//         'description' => $description,
//         'menuid' => $menuid,
//         'transid' => $transid,
//         'paramid' => $paramid
//     ];

//     $activity->store($data);
//     return true;
// }

function validate_dir($dir)
{
    if (!file_exists($dir)) {
        mkdir($dir, 0770, true);
    }
    return true;
}

// function getTypeId($typename, $catname)
// {
//     $cats = new Msstcategory();
//     $type = new Mssttype();
//     $result = 0;
//     $getcat = $cats->getByName($catname);
//     if ($getcat) {
//         $gettype = $type->getByTypename($getcat['catname'], $typename);
//         if ($gettype) {
//             $result = $gettype['typeid'];
//         }
//     }
//     return $result;
// }

function release_logo($isrelease)
{
    $icn = "bx bxs-check-circle text-success";
    if ($isrelease != 't') {
        $icn = "";
    }
    return "<i class='$icn'></i>";
}

function pembilang($nilai)
{
    $nilai = floor(abs($nilai));

    $simpanNilaiBagi = 0;
    $huruf = [
        '',
        'Satu',
        'Dua',
        'Tiga',
        'Empat',
        'Lima',
        'Enam',
        'Tujuh',
        'Delapan',
        'Sembilan',
        'Sepuluh',
        'Sebelas',
    ];
    $temp = '';

    if ($nilai < 12) {
        $temp = ' ' . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = pembilang(floor($nilai - 10)) . ' Belas';
    } else if ($nilai < 100) {
        $simpanNilaiBagi = floor($nilai / 10);
        $temp = pembilang($simpanNilaiBagi) . ' Puluh' . pembilang($nilai % 10);
    } else if ($nilai < 200) {
        $temp = ' Seratus' . pembilang($nilai - 100);
    } else if ($nilai < 1000) {
        $simpanNilaiBagi = floor($nilai / 100);
        $temp = pembilang($simpanNilaiBagi) . ' Ratus' . pembilang($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = ' Seribu' . pembilang($nilai - 1000);
    } else if ($nilai < 1000000) {
        $simpanNilaiBagi = floor($nilai / 1000);
        $temp = pembilang($simpanNilaiBagi) . ' Ribu' . pembilang($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $simpanNilaiBagi = floor($nilai / 1000000);
        $temp = pembilang($simpanNilaiBagi) . ' Juta' . pembilang($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $simpanNilaiBagi = floor($nilai / 1000000000);
        $temp = pembilang($simpanNilaiBagi) . ' Miliar' . pembilang($nilai % 1000000000);
    } else if ($nilai < 1000000000000000) {
        $simpanNilaiBagi = floor($nilai / 1000000000000);
        $temp = pembilang($simpanNilaiBagi) . ' Triliun' . pembilang($nilai % 1000000000000);
    }

    return $temp;
}

function dateIndonesiaFormat($monthType = "long", $dateSource = '')
{
    $date = date('d m Y');
    if (!empty($dateSource)) {
        $date = date('d m Y', strtotime($dateSource));
    }

    $bulan = "";
    $bulanIndex = explode(" ", $date)[1];

    switch ($bulanIndex) {
        case 1:
            $bulan = "Januari";
            break;
        case 2:
            $bulan = "Februari";
            break;
        case 3:
            $bulan = "Maret";
            break;
        case 4:
            $bulan = "April";
            break;
        case 5:
            $bulan = "Mei";
            break;
        case 6:
            $bulan = "Juni";
            break;
        case 7:
            $bulan = "Juli";
            break;
        case 8:
            $bulan = "Agustus";
            break;
        case 9:
            $bulan = "September";
            break;
        case 10:
            $bulan = "Oktober";
            break;
        case 11:
            $bulan = "November";
            break;
        case 12:
            $bulan = "Desember";
            break;
    }

    $dateParts = explode(" ", $date);
    $day = $dateParts[0];
    $year = $dateParts[2];
    if ($monthType == "short") {
        $bulan = substr($bulan, 0, 3);
    }

    return "$day $bulan $year";
}

function generateSidebar()
{
    $menu = new Msmenu;
    $arr = $menu->getAllMenuByUser();
    $new = array_filter($arr, function ($var) {
        return ($var['masterid'] == null);
    });
    usort($new, function ($a, $b) {
        return $a['sequence'] <=> $b['sequence'];
    });
    $text = "";
    foreach ($new as $n) {
        $filtermaster = $n['menuid'];
        $checksub = array_filter($arr, function ($var) use ($filtermaster) {
            return ($var['masterid'] == $filtermaster);
        });
        usort($checksub, function ($a, $b) {
            return $a['sequence'] <=> $b['sequence'];
        });
        if (count($checksub) > 0) {
            $text .= '<div href="#" class="has-parent">
            <div class="sidebar-item side-parent">
                <i class="' . $n['menuicon'] . '"></i>
                <span class="fw-normal fs-7">' . ucwords($n['menuname']) . '</span>
                <div class="navicon">
                    <i class="bx bx-chevron-right"></i>
                </div>
                <div class="submenu-div">'
                . shrinkLoop($checksub, $arr) .
                '</div>
            </div>
            <div class="submenu">' .
                loadSubs($checksub, $arr)
                . '</div>
        </div>';
        } else {
            $text .= "
            <a href='" . getURL($n['menulink']) . "' class='no-parent'>
                <div class='sidebar-item'>
                    <i class='" . $n['menuicon'] . "'></i>
                    <span class='fw-normal fs-7'>" . ucwords($n['menuname']) . "</span>
                </div>
            </a>";
        }
    }

    return $text;
}

function loadSubs($checked, $arr)
{
    $text = "";
    foreach ($checked as $a) {
        $filtermaster = $a['menuid'];
        $checking = array_filter($arr, function ($var) use ($filtermaster) {
            return ($var['masterid'] == $filtermaster);
        });
        usort($checking, function ($a, $b) {
            return $a['sequence'] <=> $b['sequence'];
        });
        $text .= '<a class="sub-item submenu-item ' . (!empty($checking) ? 'haveSub' : '') . '" ' . (empty($checking) ? 'href="' . getURL($a['menulink']) . '"' : '') . '>
        <div class="dflex justify-between">
            <span class="fw-normal fs-7set">' . ucwords($a['menuname']) . '</span>';
        if (!empty($checking)) :
            $text .= "<div class='navicon'>
                    <i class='bx bx-chevron-right'></i>
                </div>";
        endif;
        $text .= '</div>';
        if (!empty($checking)) :
            $text .= '<div class="childSub">
                ' . loadSubs($checking, $arr) . '
            </div>';
        else :
            $text .= loadSubs($checking, $arr);
        endif;
        $text .= '</a>';
    }
    return $text;
}

function shrinkLoop($checked, $arr)
{
    $text = "";
    if (count($checked) > 0) {
        $text .= '<div class="submenu-child-div subChild">';
        foreach ($checked as $c) {
            $filtermaster = $c['menuid'];
            $checking = array_filter($arr, function ($var) use ($filtermaster) {
                return ($var['masterid'] == $filtermaster);
            });
            usort($checking, function ($a, $b) {
                return $a['sequence'] <=> $b['sequence'];
            });
            $text .= '<a class="sub-item submenu-item-side fs-7set ' . (!empty($checking) ? 'haveChild' : '') . '" href="' . getURL($c['menulink']) . '" style="padding-left: 14px !important;">
            <div class="dflex align-center">
                <div class="dflex align-center">';
            if (!empty($checking)) :
                $text .= '<i class="bx bxs-right-arrow margin-r-3" style="font-size: 6px;"></i>';
            endif;
            $text .= ucwords($c['menuname']);
            $text .= '</div>
            </div>
            </a>
            <div id="listSub">
                ' . shrinkLoop($checking, $arr) . '
            </div>
            ';
        }
        $text .= '</div>';
    }

    return $text;
}

function deleteDataParam($arr)
{
    $data = [];
    foreach ($arr as $a) {
        $data[$a] = null;
    }
    return $data;
}

function getAvatar($userid)
{
    $avatar = base_url('public/images/avatar/avatars.png');
    $profilepict = getSession('profilepict');
    if ($profilepict != null) {
        $avatar = getURL('public/images/avatar/user/' . $profilepict);
    }
    return $avatar;
}

function getIndonesianDateFormat($date)
{
    $monthNames = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];

    $indonesianDate = date('j', $date) . ' ' . $monthNames[date('F', $date)] . ' ' . date('Y', $date);

    return $indonesianDate;
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

// Request & Response Helpers
function respondAndDie($status, $msg)
{
    echo json_encode([
        'success' => $status,
        'msg' => $msg,
        'csrfToken' => csrf_hash()
    ]);
    die;
}

function convertObjectToArray($data)
{
    if (is_object($data)) {
        $data = get_object_vars($data);
    }

    if (is_array($data)) {
        return array_map(__FUNCTION__, $data);
    }

    return $data;
}
