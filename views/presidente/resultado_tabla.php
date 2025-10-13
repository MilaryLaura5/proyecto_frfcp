<?php
// Esta parte ya está renderizada inicialmente
?>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Conjunto</th>
                        <th>Serie</th>
                        <th>Tipo Danza</th>
                        <th>Puntaje Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $idx => $r): ?>
                        <tr class="<?= $r['estado'] === 'descalificado' ? 'table-danger' : '' ?>">
                            <td>
                                <?php if ($r['estado'] !== 'descalificado'): ?>
                                    <span class="medalla">
                                        <?php if ($idx == 0): ?>🥇
                                        <?php elseif ($idx == 1): ?>🥈
                                        <?php elseif ($idx == 2): ?>🥉
                                        <?php else: ?><?= $idx + 1 ?>.
                                    <?php endif; ?>
                                    </span>
                                <?php else: ?>
                                    🚫
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($r['conjunto']) ?></td>
                            <td>Serie <?= $r['numero_serie'] ?></td>
                            <td><?= htmlspecialchars($r['tipo_danza']) ?></td>
                            <td>
                                <strong>
                                    <?php if ($r['estado'] === 'descalificado'): ?>
                                        DESCALIFICADO
                                    <?php else: ?>
                                        <?= number_format($r['puntaje_total'], 2) ?>
                                    <?php endif; ?>
                                </strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>