<h1><?= $authParams['client']->getName() ?> would like to access:</h1>

<ul>
    <?php foreach ($authParams['scopes'] as $scope): ?>
        <li>
            <?= $scope->getId() ?>: <?= $scope->getDescription() ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php
echo $this->Form->create(null);
echo $this->Form->input('Approve', [
    'name' => 'authorization',
    'type' => 'submit'
]);
echo $this->Form->input('Deny', [
    'name' => 'authorization',
    'type' => 'submit'
]);
echo $this->Form->end();
