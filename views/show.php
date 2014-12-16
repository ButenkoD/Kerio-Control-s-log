<table border='1'>
    <tr>
        <th>Username</th>

        <?php foreach ($dates as $date): ?>
            <th><?= $date ?></th>
        <?php endforeach ?>
    </tr>

    <?php foreach ($table as $key => $tableRow): ?>
        <tr>
            <td><?= $key ?></td>
            <?php foreach ($dates as $date): ?>
                <?php if (isset($tableRow[$date])): ?>
                    <?php
                        $value = isset($tableRow[$date]['valueIn']) ? $tableRow[$date]['valueIn']: '';
                        $value .= isset($tableRow[$date]['valueOut']) ? $tableRow[$date]['valueOut'] : '';
                    ?>
                    <?php if (isset($tableRow[$date]['late']) && $tableRow[$date]['late']): ?>
                        <td style="color:red;"><?= $value ?></td>
                    <?php else: ?>
                        <td><?= $value ?></td>
                    <?php endif ?>
                <?php else: ?>
                    <td>----</td>
                <?php endif ?>

            <?php endforeach ?>
        </tr>
    <?php endforeach ?>
</table>