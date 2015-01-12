<?php
use model\CellReportModel;

?>

<table id="dataTable">
    <thead>
    <tr>
        <th>Username</th>
        <?php foreach ($dates as $date): ?>
            <th><?= $date ?></th>
        <?php endforeach ?>
    </tr>

    </thead>
    <tbody>
    <?php foreach ($table as $key => $tableRow): ?>
        <tr>
            <td><?= $key ?></td>
            <?php foreach ($dates as $date): ?>
                <?php if ($tableRow[$date]['isWholeDay']) : ?>
                    <td>
                <?php else: ?>
                    <td style="color:red">
                <?php endif ?>
                <?php if (isset($tableRow[$date]['note'])): ?>
                    <?php echo $tableRow[$date]['note'] . '<br/>' ?>
                <?php endif ?>
                <?php if (isset($tableRow[$date]['messages'])): ?>
                    <?php echo $tableRow[$date]['messages'] ?>
                <?php endif ?>
                </td>
            <?php endforeach ?>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>

