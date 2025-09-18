<?php include __DIR__ . '/header.php'; ?>

<h2>Mon panier</h2>

<?php if (empty($commandes)) : ?>
    <p>Votre panier est vide.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Nom LEGO</th>
            <th>Quantité</th>
            <th>Prix total</th>
            <th>Action</th>
        </tr>
        <?php
       // require_once __DIR__ . '/../models/lego_models.php'; pour plus tard
        $lego_model = new LegoModel();
        foreach($commandes as $c):
            $lego = $lego_model->getLegoById($c['id_lego']);
        ?>
        <tr>
            <td><?= htmlspecialchars($lego['nom']) ?></td>
            <td><?= $c['quantite'] ?></td>
            <td><?= number_format($c['prix'],2) ?> €</td>
            <td><a href="../control/commande_control.php?action=remove&id_lego=<?= $c['id_lego'] ?>">Supprimer</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<a href="../views/images_views.php">Retour aux LEGO</a>
