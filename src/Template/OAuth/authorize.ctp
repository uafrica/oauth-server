<?php
/* @var $authParams array */
?>
<h1><?= h(__('{0} would like to access:', $authParams['client']->getName())) ?></h1>

<ul>
    <?php foreach ($authParams['scopes'] as $scope): ?>
        <li>
            <?= h($scope->id) ?>: <?= h($scope->description) ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php
echo $this->Form->create(null);
echo $this->Form->input(__('Approve'), [
    'name' => 'authorization',
    'type' => 'submit'
]);
echo $this->Form->input(__('Deny'), [
    'name' => 'authorization',
    'type' => 'submit'
]);
echo $this->Form->end();
