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