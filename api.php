<?php
include "connection.php";

$url = "http://www.boomlings.com/database/downloadGJLevel22.php";
$fields = [
    'levelID' => $_GET["levelID"],
    'secret'  => 'Wmfd2893gb7'
];
$fields_string = http_build_query($fields);
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
$result = curl_exec($ch);
$data = explode(":", $result);

$levelID = -1;
$levelName = "";
$levelDesc = "";
$levelString = "";
$levelDownloads = 0;
$levelLikes = 0;
$levelAge = "";

if ($data != "-1") {
    for ($i = 0; $i < sizeof($data); $i += 2) {
        if ($data[$i] == "1") {
            $levelID = (int)$data[$i + 1];
        }
        if ($data[$i] == "2") {
            $levelName = $data[$i + 1];
        }
        if ($data[$i] == "3") {
            $levelDesc = base64_decode($data[$i + 1]);
        }
        if ($data[$i] == "4") {
            $levelString = gzdecode(base64_decode(str_replace("-", "+", str_replace("_", "/", $data[$i + 1]))));
        }
        if ($data[$i] == "10") {
            $levelDownloads = (int)$data[$i + 1];
        }
        if ($data[$i] == "14") {
            $levelLikes = (int)$data[$i + 1];
        }
        if ($data[$i] == "28") {
            $levelAge = $data[$i + 1];
        }
    }
}

if ($levelID == -1) {
    die('-1');
}

$blocks = explode(";", $levelString);

$levelSettings = explode(",", $blocks[0]);
$startGameMode = 0;
$kS38 = '';
$kS29 = '';

for ($i = 0; $i < sizeof($levelSettings); $i += 2) {
    if ($levelSettings[$i] == "kA2") {
        $startGameMode = (int)$levelSettings[$i + 1];
    }
    if ($levelSettings[$i] == "kS38") {
        $kS38 = $levelSettings[$i + 1];
    }
    if ($levelSettings[$i] == "kS29") {
        $kS29 = $levelSettings[$i + 1];
    }
}

if ($kS38 == '') {
    $kS38 = $kS29;
}

$kS38Property1 = explode("_", explode("|", $kS38)[0]);

$startBGColorR = -1;
$startBGColorG = -1;
$startBGColorB = -1;

for ($i = 0; $i < sizeof($kS38Property1); $i += 2) {
    if ($kS38Property1[$i] == "1") {
        $startBGColorR = (int)$kS38Property1[$i + 1];
    }
    if ($kS38Property1[$i] == "2") {
        $startBGColorG = (int)$kS38Property1[$i + 1];
    }
    if ($kS38Property1[$i] == "3") {
        $startBGColorB = (int)$kS38Property1[$i + 1];
    }
}

$realObjects = sizeof($blocks) - 1;
$frequency = array_fill(0, 2000, 0);
$maxX = 0;

for ($i = 0; $i < sizeof($blocks); $i += 1) {
    $property = explode(",", $blocks[$i]);
    if (sizeof($property) > 1) {
        for ($j = 0; $j < sizeof($property); $j += 2) {
            if ($property[$j] == "1") {
                if ((int)$property[$j + 1] < 2000) {
                    $frequency[(int)$property[$j + 1]]++;
                }
            }
            if ($property[$j] == "2") {
                if ((int)$property[$j + 1] > $maxX) {
                    $maxX = (int)$property[$j + 1];
                }
            }
        }
    }
}

$id_spikes = [8, 39, 103, 392, 216, 217, 218, 458, 144, 205, 145, 459, 177, 178, 179];

$spikes = 0;

for ($i = 0; $i < sizeof($id_spikes); $i += 1) {
    $spikes += $frequency[$i];
}

$sql_submit = "";

$sql2 = "SELECT * FROM levels WHERE levelID=$levelID";
$result = mysqli_query($conn, $sql2) or die('-1');
if (mysqli_num_rows($result) == 0) {
	$sql_submit = "INSERT INTO levels (levelID, levelName, levelDesc, levelDownloads, levelLikes, levelString, realObjects, wavePortals, cubePortals, shipPortals, ballPortals, ufoPortals, robotPortals, spikes, startGameMode, maxX, startBGColorR, startBGColorG, startBGColorB) VALUES ($levelID, '".mysqli_real_escape_string($conn, $levelName)."', '".mysqli_real_escape_string($conn, $levelDesc)."', $levelDownloads, $levelLikes, '".mysqli_real_escape_string($conn, $levelString)."', $realObjects, ".$frequency[660].", ".$frequency[12].", ".$frequency[13].", ".$frequency[47].", ".$frequency[111].", ".$frequency[745].", $spikes, $startGameMode, $maxX, $startBGColorR, $startBGColorG, $startBGColorB)";
} else if (mysqli_num_rows($result) == 1) {
	$sql_submit = "UPDATE levels SET levelDesc='".mysqli_real_escape_string($conn, $levelDesc)."', levelDownloads=$levelDownloads, levelLikes=$levelLikes, levelString='".mysqli_real_escape_string($conn, $levelString)."', realObjects=$realObjects, wavePortals=".$frequency[660].", cubePortals=".$frequency[12].", shipPortals=".$frequency[13].", ballPortals=".$frequency[47].", ufoPortals=".$frequency[111].", robotPortals=".$frequency[745].", spikes=$spikes, startGameMode=$startGameMode, maxX=$maxX, startBGColorR=$startBGColorR, startBGColorG=$startBGColorG, startBGColorB=$startBGColorB WHERE levelID=$levelID";
} else {
	die('-1');
}

mysqli_query($conn, $sql_submit) or die('-1');
?>
