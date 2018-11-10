<?php
include "connection.php";

$sql = "SELECT levelID FROM levels";
$result = mysqli_query($conn, $sql) or die('-1');
$registeredLevels = mysqli_num_rows($result);
?>
<html>
    <head>
        <title>Search - GD Stats</title>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }
            
            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }
            
            tr:nth-child(even) {
                background-color: #dddddd;
            }
            
            h1 {
                text-align:center;
            }
        </style>
    </head>
    
    <body>
        <div class="w3-container">
            <h1 onclick="window.location='/'">Geometry Dash Search Engine</h1>
            <p style="text-align:center"><?php echo $registeredLevels ?> Levels Loaded (<?php echo number_format($registeredLevels / 7266400 * 100, 2, '.', '') ?>%)</p>
            <form method="GET">
                <div id="options" class="w3-modal">
                    <div class="w3-modal-content w3-animate-top w3-card-4">
                        <header class="w3-container w3-teal"> 
                            <span onclick="document.getElementById('options').style.display='none'" class="w3-button w3-display-topright">&times;</span>
                            <h2>Search Options</h2>
                        </header>
                        <div class="w3-container">
                            <p>
                                <input type="number" name="levelID_min" value="0"> ≤ Level ID ≤ <input type="number" name="levelID_max" value="2147483647"><br>
                                <input type="number" name="realObjects_min" value="0"> ≤ Real Objects ≤ <input type="number" name="realObjects_max" value="2147483647"><br>
                                <input type="number" name="levelDownloads_min" value="0"> ≤ Downloads ≤ <input type="number" name="levelDownloads_max" value="2147483647"><br>
                                <input type="number" name="levelLikes_min" value="-2147483647"> ≤ Likes ≤ <input type="number" name="levelLikes_max" value="2147483647"><br>
                                <input type="number" name="spikes_min" value="0"> ≤ Spikes ≤ <input type="number" name="spikes_max" value="2147483647"><br>
                                <input type="number" name="maxX_min" value="0"> ≤ Max X ≤ <input type="number" name="maxX_max" value="2147483647"><br>
                                <span style="color:red">rgb(<input type="number" name="startBGColorR_min" value="0" style="width: 4em">, <input type="number" name="startBGColorG_min" value="0" style="width: 4em">, <input type="number" name="startBGColorB_min" value="0" style="width: 4em">) ≤ Start Background Color ≤ rgb(<input type="number" name="startBGColorR_max" value="255" style="width: 4em">, <input type="number" name="startBGColorG_max" value="255" style="width: 4em">, <input type="number" name="startBGColorB_max" value="255" style="width: 4em">)</span><br>
                                <input type="checkbox" name="waveOnly"> Wave Only <input type="checkbox" name="cubeOnly"> Cube Only<br>
                                <input type="text" name="levelName" placeholder="Level Name"><br>
                                <input type="text" name="levelDesc" placeholder="Level Description"><br>
                                Sort by: 
                                <select name="sortBy1">
                                    <option value="levelLikes">Likes</option>
                                    <option value="levelDownloads">Downloads</option>
                                    <option value="realObjects">Real Objects</option>
                                    <option value="spikes">Spikes</option>
                                    <option value="levelName">Level Name</option>
                                    <option value="levelDesc">Level Description</option>
                                    <option value="levelID">Level ID</option>
                                </select>
                                <select name="sortBy2">
                                    <option value="DESC">DESC</option>
                                    <option value="ASC">ASC</option>
                                </select>
                            </p>
                        </div>
                    </div>
                </div>
                <center>
                    <input type="button" onclick="document.getElementById('options').style.display='block'" class="w3-button w3-green" value="Search Options">
                    <button class="w3-button w3-green"><i class="fa fa-search"></i> Search</button>
                </center>
            </form>
            <?php
            if (isset($_GET["levelID_min"])) {
                $levelID_min = intval($_GET["levelID_min"]);
                $levelID_max = intval($_GET["levelID_max"]);
                $realObjects_min = intval($_GET["realObjects_min"]);
                $realObjects_max = intval($_GET["realObjects_max"]);
                $levelDownloads_min = intval($_GET["levelDownloads_min"]);
                $levelDownloads_max = intval($_GET["levelDownloads_max"]);
                $levelLikes_min = intval($_GET["levelLikes_min"]);
                $levelLikes_max = intval($_GET["levelLikes_max"]);
                $spikes_min = intval($_GET["spikes_min"]);
                $spikes_max = intval($_GET["spikes_max"]);
                $maxX_min = intval($_GET["maxX_min"]);
                $maxX_max = intval($_GET["maxX_max"]);
                $levelName = mysqli_real_escape_string($conn, $_GET["levelName"]);
                $levelDesc = mysqli_real_escape_string($conn, $_GET["levelDesc"]);
                $waveOnly = "";
                $cubeOnly = "";
                $sortBy1 = "levelLikes";
                $sortBy2 = "DESC";
                
                $sortCategory = ["realObjects", "spikes", "levelDownloads", "levelLikes", "levelName", "levelDesc", "levelID"];
                
                if ($_GET["waveOnly"] == "on") {
                    $waveOnly = " AND (cubePortals=0 AND shipPortals=0 AND ballPortals=0 AND ufoPortals=0 AND robotPortals=0 AND startGameMode=4)";
                }
                
                if ($_GET["cubeOnly"] == "on") {
                    $cubeOnly = " AND (wavePortals=0 AND shipPortals=0 AND ballPortals=0 AND ufoPortals=0 AND robotPortals=0 AND startGameMode=0)";
                }
                
                if (in_array($_GET["sortBy1"], $sortCategory)) {
                    $sortBy1 = $_GET["sortBy1"];
                }
                
                if ($_GET["sortBy2"] == "ASC") {
                    $sortBy2 = "ASC";
                }
                
                $sql2 = "SELECT levelID, levelName, levelDesc, levelDownloads, levelLikes, realObjects FROM levels WHERE levelName LIKE '%$levelName%' AND levelDesc LIKE '%$levelDesc%' AND (levelID BETWEEN $levelID_min AND $levelID_max) AND (realObjects BETWEEN $realObjects_min AND $realObjects_max) AND (levelDownloads BETWEEN $levelDownloads_min AND $levelDownloads_max) AND (levelLikes BETWEEN $levelLikes_min AND $levelLikes_max) AND (spikes BETWEEN $spikes_min AND $spikes_max) AND (maxX BETWEEN $maxX_min AND $maxX_max)$waveOnly$cubeOnly ORDER BY $sortBy1 $sortBy2 LIMIT 100";
                
                $result2 = mysqli_query($conn, $sql2) or die('-1');
                
                if (mysqli_num_rows($result2) == 0) {
                    die('');
                }
                
                echo "<table><tr><th>Level ID</th><th>Level Name</th><th>Level Description</th><th>Downloads</th><th>Likes</th><th>Real Objects</th></tr>";
                
                while ($row = mysqli_fetch_assoc($result2)) {
                    //file_get_contents("https://gdstats.pizzaroot.com/api.php?levelID=".$row['levelID']);
                    echo "<tr><td>".$row['levelID']."</td><td>".$row['levelName']."</td><td>".$row['levelDesc']."</td><td>".$row['levelDownloads']."</td><td>".$row['levelLikes']."</td><td>".$row['realObjects']."</td></tr>";
                    //flush();
                    //ob_flush();
                }
                echo "</table>";
            }
            ?>
        </div>
    </body>
</html>
