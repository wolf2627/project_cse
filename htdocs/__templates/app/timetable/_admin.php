<div>
    <table>
        <thead>
            <tr>
                <th>Sun</th>
                <th>Mon</th>
                <th>Tue</th>
                <th>Wed</th>
                <th>Thu</th>
                <th>Fri</th>
                <th>Sat</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $day = 1;
            for ($i = 0; $i < 5; $i++) {
                echo "<tr>";
                for ($j = 0; $j < 7; $j++) {
                    if ($day > 31) {
                        echo "<td></td>";
                    } else {
                        echo "<td>$day</td>";
                    }
                    $day++;
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>