<ul>
    <?php foreach ($authParams['scopes'] as $scope): ?>
        <li>
            <?= $scope->getId() ?>: <?= $scope->getDescription() ?>
        </li>
    <?php endforeach; ?>
</ul>